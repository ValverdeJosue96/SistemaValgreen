<?php
/**
 * convert.php — Convierte archivos PROMPT_XX_*.md → .docx y .pdf
 * Ubicación:   documentos_pruebas/convert.php
 * Requisitos:  PHP 8.x con extensiones: zip, mbstring (ambas nativas).
 *              Microsoft Edge instalado (C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe)
 * NO MODIFICA NADA FUERA DE documentos_pruebas/.
 */
set_time_limit(0);
ignore_user_abort(true);
ini_set('memory_limit','1G');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set('America/La_Paz');

$BASE_DIR = __DIR__;
$TMP_DIR  = $BASE_DIR . DIRECTORY_SEPARATOR . '_convert_tmp';
if (!is_dir($TMP_DIR)) { mkdir($TMP_DIR, 0777, true); }

$LOG = $BASE_DIR . '/_convert_log.txt';
if (is_file($LOG)) @unlink($LOG);
function wlog($msg) {
    global $LOG;
    $line = "[".date('H:i:s')."] $msg";
    @file_put_contents($LOG, $line . PHP_EOL, FILE_APPEND);
    echo $line . PHP_EOL;
}

register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_RECOVERABLE_ERROR, E_USER_ERROR])) {
        wlog("!!! PHP FATAL en {$err['file']}:{$err['line']} => {$err['message']}");
    }
});

wlog("=== INICIO CONVERSIÓN ===");
wlog("Base dir : $BASE_DIR");

/* ==============================================================
 * LISTA DE ARCHIVOS MD (orden natural 01..10)
 * ============================================================= */
$mdFiles = glob($BASE_DIR . DIRECTORY_SEPARATOR . 'PROMPT_*.md');
if (!$mdFiles) { wlog("ERROR: no se encontraron archivos .md en $BASE_DIR"); exit(1); }
usort($mdFiles, 'strnatcmp');
wlog("Encontrados ".count($mdFiles)." archivos Markdown.");

/* ==============================================================
 * PARSER MARKDOWN → HTML (simplificado, cubre el contenido real)
 * ============================================================= */
function escapeXml($s) {
    return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8', false);
}
function escapeHtml($s) {
    return htmlspecialchars($s, ENT_HTML5 | ENT_QUOTES, 'UTF-8', false);
}

function inlineMd($text) {
    // 1. Negrita **xx**
    $text = preg_replace('/\*\*(.+?)\*\*/us', '<strong>$1</strong>', $text);
    // 2. Cursiva *xx*  (cuidado con listas `- ` que ya se manejaron antes)
    $text = preg_replace('/(?<!\*)\*(?!\s)(.+?)(?<!\s)\*(?!\*)/us', '<em>$1</em>', $text);
    // 3. Código inline `x`
    $text = preg_replace('/`([^`]+)`/u', '<code>$1</code>', $text);
    return $text;
}

function parseMarkdownToHtml($mdText) {
    $mdText = str_replace(["\r\n", "\r"], "\n", $mdText);
    $lines = explode("\n", $mdText);

    $html = '';
    $i = 0;
    $n = count($lines);

    while ($i < $n) {
        $line = $lines[$i];
        $trim = trim($line);

        // 1) Encabezados
        if (preg_match('/^(#{1,6})\s+(.*)$/u', $trim, $m)) {
            $level = strlen($m[1]);
            $text = inlineMd(escapeHtml($m[2]));
            $html .= "<h$level>$text</h$level>\n";
            $i++;
            continue;
        }

        // 2) Separador HR ---
        if (preg_match('/^-{3,}$/u', $trim)) {
            $html .= "<hr>\n";
            $i++;
            continue;
        }

        // 3) Tablas Markdown
        if (strpos($trim, '|') === 0 && ($i+1 < $n) && preg_match('/^\|?\s*:?-{2,}/u', trim($lines[$i+1]))) {
            // 3a) Cabecera
            $header = array_map('trim', explode('|', substr($lines[$i], 1, -1)));
            $i++;
            // 3b) Línea separador (skip)
            $i++;
            $rows = [];
            while ($i < $n && strpos(trim($lines[$i]), '|') === 0) {
                $row = array_map('trim', explode('|', substr(trim($lines[$i]), 1, -1)));
                $rows[] = $row;
                $i++;
            }
            $html .= "<table class=\"md-table\">\n<thead>\n<tr>";
            foreach ($header as $h) {
                $html .= "<th>".inlineMd(escapeHtml($h))."</th>";
            }
            $html .= "</tr>\n</thead>\n<tbody>\n";
            foreach ($rows as $r) {
                $html .= "<tr>";
                foreach ($r as $c) {
                    $html .= "<td>".inlineMd(escapeHtml($c))."</td>";
                }
                $html .= "</tr>\n";
            }
            $html .= "</tbody>\n</table>\n";
            continue;
        }

        // 4) Citas > xxxx (multilínea)
        if (strpos($trim, '>') === 0) {
            $buf = [];
            while ($i < $n && strpos(trim($lines[$i]), '>') === 0) {
                $l = $lines[$i];
                $l = preg_replace('/^>\s?/u', '', $l);
                $buf[] = $l;
                $i++;
            }
            $text = inlineMd(escapeHtml(trim(implode(' ', $buf))));
            $html .= "<blockquote>$text</blockquote>\n";
            continue;
        }

        // 5) Listas no ordenadas `- item` (multilínea)
        if (preg_match('/^-\s+(.+)$/u', $trim, $m)) {
            $html .= "<ul>\n";
            while ($i < $n && preg_match('/^-\s+(.+)$/u', trim($lines[$i]), $mi)) {
                $html .= "<li>".inlineMd(escapeHtml($mi[1]))."</li>\n";
                $i++;
            }
            $html .= "</ul>\n";
            continue;
        }

        // 6) Listas ordenadas `1. item` (multilínea)
        if (preg_match('/^\d+\.\s+(.+)$/u', $trim, $m)) {
            $html .= "<ol>\n";
            while ($i < $n && preg_match('/^\d+\.\s+(.+)$/u', trim($lines[$i]), $mi)) {
                $html .= "<li>".inlineMd(escapeHtml($mi[1]))."</li>\n";
                $i++;
            }
            $html .= "</ol>\n";
            continue;
        }

        // 7) Línea vacía (skip)
        if ($trim === '') { $i++; continue; }

        // 8) Párrafo regular (multilínea hasta línea vacía)
        $buf = [$line];
        $i++;
        while ($i < $n && trim($lines[$i]) !== ''
               && !preg_match('/^(#{1,6})\s+/u', trim($lines[$i]))
               && !preg_match('/^-{3,}$/u', trim($lines[$i]))
               && !(strpos(trim($lines[$i]), '|') === 0 && isset($lines[$i+1]) && preg_match('/^\|?\s*:?-{2,}/u', trim($lines[$i+1])))
               && strpos(trim($lines[$i]), '>') !== 0
               && !preg_match('/^-\s+/u', trim($lines[$i]))
               && !preg_match('/^\d+\.\s+/u', trim($lines[$i]))) {
            $buf[] = $lines[$i];
            $i++;
        }
        $text = inlineMd(escapeHtml(trim(implode(' ', $buf))));
        $html .= "<p>$text</p>\n";
    }

    return $html;
}

/* ==============================================================
 * HTML WRAPPER (para PDF)
 * ============================================================= */
function buildFullHtmlDoc($title, $bodyHtml) {
    $t = escapeHtml($title);
    $dateStr = date('Y-m-d H:i');
    return <<<"HTMLEOF"
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>$t</title>
<style>
  @page { size: Letter; margin: 1.8cm 1.6cm; }
  * { box-sizing: border-box; }
  html, body {
    font-family: "Segoe UI", "Calibri", "Arial", "Noto Color Emoji", "Segoe UI Emoji", sans-serif;
    font-size: 11.5pt; line-height: 1.45; color: #1a1a1a;
  }
  h1 { font-size: 20pt; color: #0b5d3d; border-bottom: 2px solid #0b5d3d; padding-bottom: .3em; margin-top: 0; }
  h2 { font-size: 15.5pt; color: #0b5d3d; border-left: 4px solid #17a673; padding-left: .55em; margin-top: 1.4em; }
  h3 { font-size: 13pt; color: #185240; margin-top: 1.1em; }
  h4 { font-size: 12pt; color: #185240; }
  p { margin: .45em 0; orphans: 3; widows: 3; }
  ul, ol { margin: .45em 0 .45em 1.6em; padding-left: 0; }
  li { margin: .2em 0; }
  blockquote {
    background: #f1f8f4; border-left: 4px solid #17a673;
    margin: .7em 0; padding: .6em .9em; color: #253;
    font-style: normal;
  }
  code {
    background: #eef3f0; padding: 1px 5px; border-radius: 3px;
    font-family: "Cascadia Code", "Consolas", monospace; font-size: .95em;
    color: #163;
  }
  hr { border: 0; border-top: 1px solid #cfd8d4; margin: 1.1em 0; }
  table.md-table {
    border-collapse: collapse; width: 100%; margin: .8em 0;
    font-size: 10pt; page-break-inside: auto;
  }
  table.md-table thead th {
    background: #0b5d3d; color: #fff; border: 1px solid #083a27;
    padding: 6px 8px; text-align: left; font-weight: 600;
    position: sticky; top: 0;
  }
  table.md-table tbody td {
    border: 1px solid #c2d3cc; padding: 4px 7px; vertical-align: top;
  }
  table.md-table tbody tr:nth-child(even) td { background: #f4faf7; }
  .meta {
    background: #f4faf7; border: 1px solid #b7d6c9; border-radius: 6px;
    padding: .7em 1em; margin-bottom: 1.1em; color: #223;
  }
  .meta p { margin: .1em 0; }
</style>
</head>
<body>
  <div class="meta">
    <p><strong>Proyecto:</strong> VALGREEN — Sistema de Gestión de Repostería</p>
    <p><strong>Documento:</strong> $t</p>
    <p><strong>Generado el:</strong> $dateStr</p>
  </div>
$bodyHtml
</body>
</html>
HTMLEOF;
}

/* ==============================================================
 * GENERADOR DOCX (ZIP + OOXML) — SIN LIBRERÍAS, PHP NATIVO
 * ============================================================= */
function makeDocx($outputDocx, $title, $mdText) {
    $bodyXml = buildDocxBody($mdText);
    $dateStr = date('Y-m-d H:i');
    $z = new ZipArchive();
    if (file_exists($outputDocx)) { @unlink($outputDocx); }
    if ($z->open($outputDocx, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return false;
    }
    // Archivos OOXML mínimos
    $z->addFromString('[Content_Types].xml', contentTypesXml());
    $z->addFromString('_rels/.rels', relsXml());
    $z->addFromString('docProps/app.xml', appXml($title));
    $z->addFromString('docProps/core.xml', coreXml($title));
    $z->addFromString('word/_rels/document.xml.rels', documentRelsXml());
    $z->addFromString('word/styles.xml', stylesXml());
    $z->addFromString('word/fontTable.xml', fontTableXml());
    $z->addFromString('word/settings.xml', settingsXml());
    $z->addFromString('word/document.xml', documentXml($title, $bodyXml, $dateStr));
    $z->close();
    return true;
}

function documentXml($title, $bodyXml, $dateStr) {
    $t = escapeXml($title);
    $d = escapeXml($dateStr);
    return <<<XMLEOF
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
            xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
            xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml">
  <w:body>
    <w:p><w:pPr><w:pStyle w:val="Title"/></w:pPr>
      <w:r><w:t xml:space="preserve">$t</w:t></w:r>
    </w:p>
    <w:p><w:pPr><w:pStyle w:val="Subtitle"/></w:pPr>
      <w:r><w:rPr><w:b/><w:sz w:val="20"/></w:rPr><w:t xml:space="preserve">Proyecto VALGREEN — Sistema de Gestión de Repostería    |    Generado el: $d</w:t></w:r>
    </w:p>
    <w:p/>
$bodyXml
    <w:sectPr>
      <w:pgSz w:w="12240" w:h="15840"/>
      <w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="708" w:footer="708" w:gutter="0"/>
      <w:cols w:space="708"/>
      <w:docGrid w:linePitch="360"/>
    </w:sectPr>
  </w:body>
</w:document>
XMLEOF;
}

function buildDocxBody($mdText) {
    $mdText = str_replace(["\r\n", "\r"], "\n", $mdText);
    $lines = explode("\n", $mdText);
    $xml = '';
    $i = 0; $n = count($lines);

    while ($i < $n) {
        $line = $lines[$i];
        $trim = trim($line);

        // Encabezados
        if (preg_match('/^(#{1,6})\s+(.*)$/u', $trim, $m)) {
            $lvl = (int)strlen($m[1]);
            $style = 'Heading'.min($lvl, 4);
            $xml .= docxParagraph($m[2], $style, true);
            $i++; continue;
        }
        // HR
        if (preg_match('/^-{3,}$/u', $trim)) {
            $xml .= '<w:p><w:pPr><w:pBdr><w:bottom w:val="single" w:sz="6" w:space="1" w:color="888888"/></w:pBdr></w:pPr></w:p>'."\n";
            $i++; continue;
        }
        // Tablas
        if (strpos($trim, '|') === 0 && ($i+1 < $n) && preg_match('/^\|?\s*:?-{2,}/u', trim($lines[$i+1]))) {
            $header = array_map('trim', explode('|', substr($lines[$i], 1, -1)));
            $i++;
            $i++;
            $rows = [];
            while ($i < $n && strpos(trim($lines[$i]), '|') === 0) {
                $row = array_map('trim', explode('|', substr(trim($lines[$i]), 1, -1)));
                $rows[] = $row;
                $i++;
            }
            $xml .= docxTable($header, $rows);
            $xml .= '<w:p/>'."\n";
            continue;
        }
        // Blockquote
        if (strpos($trim, '>') === 0) {
            $buf = [];
            while ($i < $n && strpos(trim($lines[$i]), '>') === 0) {
                $l = preg_replace('/^>\s?/u', '', $lines[$i]);
                $buf[] = $l; $i++;
            }
            $xml .= docxParagraph(trim(implode(' ', $buf)), 'Quote', false);
            continue;
        }
        // Lista no ordenada
        if (preg_match('/^-\s+(.+)$/u', $trim, $m)) {
            while ($i < $n && preg_match('/^-\s+(.+)$/u', trim($lines[$i]), $mi)) {
                $xml .= docxParagraph($mi[1], 'ListBullet', false);
                $i++;
            }
            continue;
        }
        // Lista ordenada
        if (preg_match('/^\d+\.\s+(.+)$/u', $trim, $m)) {
            while ($i < $n && preg_match('/^\d+\.\s+(.+)$/u', trim($lines[$i]), $mi)) {
                $xml .= docxParagraph($mi[1], 'ListNumber', false);
                $i++;
            }
            continue;
        }
        if ($trim === '') { $i++; continue; }
        // Párrafo multilínea
        $buf = [$line]; $i++;
        while ($i < $n && trim($lines[$i]) !== ''
               && !preg_match('/^(#{1,6})\s+/u', trim($lines[$i]))
               && !preg_match('/^-{3,}$/u', trim($lines[$i]))
               && !(strpos(trim($lines[$i]), '|') === 0 && isset($lines[$i+1]) && preg_match('/^\|?\s*:?-{2,}/u', trim($lines[$i+1])))
               && strpos(trim($lines[$i]), '>') !== 0
               && !preg_match('/^-\s+/u', trim($lines[$i]))
               && !preg_match('/^\d+\.\s+/u', trim($lines[$i]))) {
            $buf[] = $lines[$i]; $i++;
        }
        $xml .= docxParagraph(trim(implode(' ', $buf)), null, false);
    }
    return $xml;
}

function parseInlineRunXml($text) {
    $out = '';
    // Segmentamos por **bold**, *italic*, `code`
    $pattern = '/(\*\*.+?\*\*)|(`[^`]+`)|((?<!\*)\*(?!\s).+?(?<!\s)\*(?!\*))/us';
    $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    if (!$parts) { $parts = [$text]; }
    foreach ($parts as $p) {
        $bold = false; $italic = false; $code = false;
        if (strpos($p, '**') === 0) { $bold = true; $p = substr($p, 2, -2); }
        elseif (strpos($p, '`') === 0) { $code = true; $p = substr($p, 1, -1); }
        elseif (strpos($p, '*') === 0) { $italic = true; $p = substr($p, 1, -1); }
        $pr = '';
        if ($bold)   $pr .= '<w:b/>';
        if ($italic) $pr .= '<w:i/>';
        if ($code)   $pr .= '<w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:color w:val="117733"/>';
        $out .= '<w:r>';
        if ($pr) $out .= "<w:rPr>$pr</w:rPr>";
        $out .= '<w:t xml:space="preserve">'.escapeXml($p).'</w:t>';
        $out .= '</w:r>';
    }
    return $out;
}

function docxParagraph($text, $style = null, $keep = false) {
    $pPr = '<w:pPr>';
    if ($style) $pPr .= '<w:pStyle w:val="'.$style.'"/>';
    if ($keep)  $pPr .= '<w:keepNext/>';
    $pPr .= '<w:spacing w:before="60" w:after="120" w:line="276" w:lineRule="auto"/>';
    $pPr .= '</w:pPr>';
    return '<w:p>'.$pPr.parseInlineRunXml($text).'</w:p>'."\n";
}

function docxTable($header, $rows) {
    $cols = count($header);
    // Apertura
    $x = '<w:tbl>'."\n";
    // Propiedades
    $x .= '<w:tblPr>';
    $x .= '<w:tblStyle w:val="TableGrid"/>';
    $x .= '<w:tblW w:w="5000" w:type="pct"/>';
    $x .= '<w:tblBorders>';
    $x .= '<w:top w:val="single" w:sz="4" w:space="0" w:color="888888"/>';
    $x .= '<w:left w:val="single" w:sz="4" w:space="0" w:color="888888"/>';
    $x .= '<w:bottom w:val="single" w:sz="4" w:space="0" w:color="888888"/>';
    $x .= '<w:right w:val="single" w:sz="4" w:space="0" w:color="888888"/>';
    $x .= '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="AAAAAA"/>';
    $x .= '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="AAAAAA"/>';
    $x .= '</w:tblBorders>';
    $x .= '<w:tblLook w:val="04A0"/>';
    $x .= '</w:tblPr>'."\n";
    // Ancho columnas
    $colW = (int)(9360 / max(1, $cols));
    $x .= '<w:tblGrid>';
    for ($c = 0; $c < $cols; $c++) { $x .= '<w:gridCol w:w="'.$colW.'"/>'; }
    $x .= '</w:tblGrid>'."\n";
    // Fila cabecera
    $x .= '<w:tr>'."\n";
    foreach ($header as $h) {
        $x .= '<w:tc><w:tcPr><w:tcW w:w="'.$colW.'" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="0B5D3D"/></w:tcPr>';
        $x .= '<w:p><w:pPr><w:spacing w:before="40" w:after="40"/></w:pPr>';
        $x .= '<w:r><w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr><w:t xml:space="preserve">'.escapeXml($h).'</w:t></w:r></w:p>';
        $x .= '</w:tc>'."\n";
    }
    $x .= '</w:tr>'."\n";
    // Filas datos
    $alt = false;
    foreach ($rows as $row) {
        $alt = !$alt;
        $x .= '<w:tr>'."\n";
        for ($c = 0; $c < $cols; $c++) {
            $cell = $row[$c] ?? '';
            $x .= '<w:tc><w:tcPr><w:tcW w:w="'.$colW.'" w:type="dxa"/>';
            if ($alt) $x .= '<w:shd w:val="clear" w:color="auto" w:fill="F4FAF7"/>';
            $x .= '</w:tcPr><w:p><w:pPr><w:spacing w:before="30" w:after="30"/></w:pPr>';
            $x .= parseInlineRunXml($cell);
            $x .= '</w:p></w:tc>'."\n";
        }
        $x .= '</w:tr>'."\n";
    }
    $x .= '</w:tbl>'."\n";
    return $x;
}

/* --- Archivos estáticos OOXML --- */
function contentTypesXml() {
    return <<<XMLEOF
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
  <Override PartName="/word/fontTable.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.fontTable+xml"/>
  <Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
</Types>
XMLEOF;
}
function relsXml() {
    return <<<XMLEOF
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XMLEOF;
}
function documentRelsXml() {
    return <<<XMLEOF
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/fontTable" Target="fontTable.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings" Target="settings.xml"/>
</Relationships>
XMLEOF;
}
function appXml($title) {
    $t = escapeXml($title);
    return <<<XMLEOF
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"
            xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>VALGREEN-DocConverter</Application>
  <AppVersion>1.0</AppVersion>
  <Company>Universidad</Company>
  <TitlesOfParts><vt:vector size="1" baseType="lpstr"><vt:lpstr>$t</vt:lpstr></vt:vector></TitlesOfParts>
  <Lines>100</Lines>
  <Characters>5000</Characters>
</Properties>
XMLEOF;
}
function coreXml($title) {
    $t = escapeXml($title);
    $now = date('Y-m-d\TH:i:s\Z');
    return <<<XMLEOF
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
                   xmlns:dc="http://purl.org/dc/elements/1.1/"
                   xmlns:dcterms="http://purl.org/dc/terms/"
                   xmlns:dcmitype="http://purl.org/dc/dcmitype/"
                   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:title>$t</dc:title>
  <dc:creator>Equipo QA Valgreen</dc:creator>
  <cp:lastModifiedBy>Equipo QA Valgreen</cp:lastModifiedBy>
  <dcterms:created xsi:type="dcterms:W3CDTF">$now</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">$now</dcterms:modified>
  <dc:subject>Pruebas de Software QA</dc:subject>
  <cp:keywords>Pruebas, QA, Valgreen, Laravel, MySQL</cp:keywords>
  <dc:description>Documento de pruebas para el sistema Valgreen.</dc:description>
</cp:coreProperties>
XMLEOF;
}
function fontTableXml() {
    return <<<XMLEOF
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:fonts xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:font w:name="Calibri"><w:panose1 w:val="020F0502020204030204"/></w:font>
  <w:font w:name="Segoe UI"><w:panose1 w:val="020F0502020204030204"/></w:font>
  <w:font w:name="Consolas"><w:panose1 w:val="02070309020205020404"/></w:font>
</w:fonts>
XMLEOF;
}
function settingsXml() {
    return <<<XMLEOF
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:defaultTabStop w:val="720"/>
  <w:compat><w:compatSetting w:name="compatibilityMode" w:uri="http://schemas.microsoft.com/office/word" w:val="15"/></w:compat>
</w:settings>
XMLEOF;
}
function stylesXml() {
    return <<<XMLEOF
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri" w:cs="Calibri"/><w:sz w:val="22"/><w:lang w:val="es-BO"/></w:rPr></w:rPrDefault>
    <w:pPrDefault><w:pPr><w:spacing w:after="120" w:line="276" w:lineRule="auto"/></w:pPr></w:pPrDefault>
  </w:docDefaults>
  <w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/></w:style>
  <w:style w:type="paragraph" w:styleId="Title"><w:name w:val="Title"/><w:basedOn w:val="Normal"/><w:pPr><w:spacing w:before="0" w:after="200"/><w:jc w:val="left"/></w:pPr><w:rPr><w:b/><w:color w:val="0B5D3D"/><w:sz w:val="36"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="Subtitle"><w:name w:val="Subtitle"/><w:basedOn w:val="Normal"/><w:rPr><w:color w:val="333333"/><w:sz w:val="20"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:basedOn w:val="Normal"/><w:uiPriority w:val="9"/><w:qFormat/><w:pPr><w:spacing w:before="240" w:after="120"/><w:pBdr><w:bottom w:val="single" w:sz="8" w:space="4" w:color="0B5D3D"/></w:pBdr></w:pPr><w:rPr><w:b/><w:color w:val="0B5D3D"/><w:sz w:val="30"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="Heading2"><w:name w:val="heading 2"/><w:basedOn w:val="Normal"/><w:uiPriority w:val="9"/><w:qFormat/><w:pPr><w:spacing w:before="200" w:after="100"/></w:pPr><w:rPr><w:b/><w:color w:val="0B5D3D"/><w:sz w:val="26"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="Heading3"><w:name w:val="heading 3"/><w:basedOn w:val="Normal"/><w:uiPriority w:val="9"/><w:qFormat/><w:pPr><w:spacing w:before="160" w:after="80"/></w:pPr><w:rPr><w:b/><w:color w:val="185240"/><w:sz w:val="24"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="Heading4"><w:name w:val="heading 4"/><w:basedOn w:val="Normal"/><w:uiPriority w:val="9"/><w:qFormat/><w:pPr><w:spacing w:before="120" w:after="60"/></w:pPr><w:rPr><w:b/><w:color w:val="185240"/><w:sz w:val="22"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="Quote"><w:name w:val="Quote"/><w:basedOn w:val="Normal"/><w:pPr><w:ind w:left="360"/><w:pBdr><w:left w:val="single" w:sz="18" w:space="10" w:color="17A673"/></w:pBdr><w:spacing w:before="80" w:after="80"/></w:pPr><w:rPr><w:color w:val="225533"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="ListBullet"><w:name w:val="List Bullet"/><w:basedOn w:val="Normal"/><w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr><w:rPr><w:sz w:val="22"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="ListNumber"><w:name w:val="List Number"/><w:basedOn w:val="Normal"/><w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr><w:rPr><w:sz w:val="22"/></w:rPr></w:style>
</w:styles>
XMLEOF;
}

/* ==============================================================
 * PDF VÍA EDGE HEADLESS
 * ============================================================= */
function findEdge() {
    $candidates = [
        "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe",
        "C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe",
        "C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe",
        "C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe",
    ];
    foreach ($candidates as $p) { if (is_file($p)) return $p; }
    return null;
}

function renderHtmlToPdf($htmlFile, $pdfFile, $browserBin) {
    if (!file_exists($htmlFile)) return false;
    if (file_exists($pdfFile)) @unlink($pdfFile);
    $htmlPath = realpath($htmlFile);
    $pdfPath  = realpath(dirname($pdfFile)) . DIRECTORY_SEPARATOR . basename($pdfFile);
    $url = 'file:///' . str_replace('\\', '/', $htmlPath);

    // flags: headless, sin cabecera, tamaño carta, márgenes, escala 0.92
    $cmd = sprintf(
        '"%s" --headless --disable-gpu --hide-scrollbars --no-sandbox --disable-dev-shm-usage --virtual-time-budget=5000 --run-all-compositor-stages-before-draw --print-to-pdf-no-header --print-to-pdf="%s" "%s" 2>&1',
        $browserBin,
        $pdfPath,
        $url
    );
    $desc = [0=>['pipe','r'], 1=>['pipe','w'], 2=>['pipe','w']];
    $pipes = [];
    $proc = proc_open($cmd, $desc, $pipes);
    if (!is_resource($proc)) return false;
    fclose($pipes[0]);
    $out = stream_get_contents($pipes[1]); fclose($pipes[1]);
    $err = stream_get_contents($pipes[2]); fclose($pipes[2]);
    $exitCode = proc_close($proc);
    // Espera corta a que Edge acabe de escribir
    $tries = 0;
    while (!file_exists($pdfPath) || filesize($pdfPath) < 1024) {
        usleep(200000);
        if (++$tries > 40) break;
    }
    return is_file($pdfPath) && filesize($pdfPath) > 2048;
}

/* ==============================================================
 * LOOP PRINCIPAL
 * ============================================================= */
$browser = findEdge();
if (!$browser) { wlog("ERROR: no se encontró Edge/Chrome."); exit(2); }
wlog("Navegador headless: $browser");

$ok = 0; $fail = 0;
foreach ($mdFiles as $mdIdx => $mdPath) {
    $base = pathinfo($mdPath, PATHINFO_FILENAME);
    $title = preg_replace('/^PROMPT_\d+_/u', '', $base);
    $title = str_replace('_', ' ', $title);
    $htmlTmp = $TMP_DIR . DIRECTORY_SEPARATOR . $base . '.html';
    $docxOut = $BASE_DIR . DIRECTORY_SEPARATOR . $base . '.docx';
    $pdfOut  = $BASE_DIR . DIRECTORY_SEPARATOR . $base . '.pdf';

    // Idempotencia CONSERVADORA: solo regenera si falta o está corrupto (pequeño).
    $docxGood = (is_file($docxOut) && filesize($docxOut) > 3072);
    $pdfGood  = (is_file($pdfOut)  && filesize($pdfOut)  > 10240);
    // Solo borramos lo que vamos a regenerar (después de confirmar que vamos a hacerlo, no antes)
    if (!$docxGood && is_file($docxOut)) { @unlink($docxOut); }
    if (!$pdfGood  && is_file($pdfOut))  { @unlink($pdfOut);  }
    // Si ambos están bien: SALTAMOS este archivo (no tocamos nada => seguro)
    if ($docxGood && $pdfGood) {
        wlog("[$mdIdx/".(count($mdFiles)-1)."] SKIP $base (ya existe docx=".round(filesize($docxOut)/1024,1)."KB + pdf=".round(filesize($pdfOut)/1024,1)."KB)");
        $ok += 2;
        continue;
    }

    try {
        wlog("[$mdIdx/".(count($mdFiles)-1)."] Procesando $base ... (docx regenerar=".(!$docxGood?"SI":"NO")." / pdf regenerar=".(!$pdfGood?"SI":"NO").")");
        $md = file_get_contents($mdPath);
        if ($md === false) { throw new Exception("no puedo leer .md"); }

        // 1. MD → HTML (para PDF y como backup de visualización)
        if (!$pdfGood) {
            $bodyHtml = parseMarkdownToHtml($md);
            $fullHtml = buildFullHtmlDoc($title, $bodyHtml);
            $wb = file_put_contents($htmlTmp, $fullHtml);
            if ($wb === false) { throw new Exception("no se pudo escribir HTML tmp"); }
        }

        // 2. DOCX (solo si falta)
        if (!$docxGood) {
            wlog("  -> generando DOCX ...");
            $docxOk = makeDocx($docxOut, $title, $md);
            if (!$docxOk || !is_file($docxOut) || filesize($docxOut) < 1024) {
                throw new Exception("DOCX no válido (size=".(is_file($docxOut)?filesize($docxOut):0).")");
            }
            wlog("OK $base.docx  (".number_format(filesize($docxOut)/1024,1)." KB)");
        }
        $ok++;

        // 3. PDF (solo si falta)
        if (!$pdfGood) {
            wlog("  -> generando PDF via Edge headless ...");
            $pdfOk = renderHtmlToPdf($htmlTmp, $pdfOut, $browser);
            if (!$pdfOk || !is_file($pdfOut) || filesize($pdfOut) < 2048) {
                throw new Exception("PDF no válido (size=".(is_file($pdfOut)?filesize($pdfOut):0).")");
            }
            wlog("OK $base.pdf   (".number_format(filesize($pdfOut)/1024,1)." KB)");
        }
        $ok++;
    } catch (\Throwable $e) {
        wlog("KO $base => ".$e->getMessage());
        wlog("   file=".$e->getFile()." line=".$e->getLine());
        $fail++;
        // Continúa con el siguiente archivo
    }
}

wlog("=== FIN CONVERSIÓN: $ok OK / $fail FALLOS ===");

// Limpiar HTML tmp si todo OK
if ($fail === 0) {
    foreach (glob($TMP_DIR.'/*.html') as $f) { @unlink($f); }
    @rmdir($TMP_DIR);
    wlog("Carpeta temporal limpiada.");
} else {
    wlog("Se mantiene _convert_tmp/ para depurar fallos.");
}
