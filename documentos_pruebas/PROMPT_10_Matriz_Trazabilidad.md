# PROMPT 10 — Matriz de trazabilidad

**Proyecto:** VALGREEN — Sistema de Gestión de Repostería
**Versión:** 1.0
**Fecha:** 2026-09-23

## Objetivo
Verificar que cada requisito funcional (RF) y no funcional (RNF) del PROMPT 2 cuente con al menos un caso de prueba asociado, asegurando cobertura sobre los requisitos críticos.

**Fuentes:** 25 RF + 10 RNF; Casos CP-001..CP-038; F1..F8 (120 validaciones); AA-001..AA-015 (Auth/Autorización); BD-001..BD-024 (MySQL); INT-001..INT-010 (Integración).

---

## Matriz Requisito ↔ Caso de Prueba

| Requisito | Descripción | Módulo | Caso(s) prueba asociado(s) | Tipo de prueba | Prioridad requisito | Estado CP |
|-----------|-------------|--------|----------------------------|----------------|---------------------|-----------|
| RF-001 | Login exitoso usuario activo | MOD-01 | CP-001, AA-001, INT-001 | AUT, INT | 🔴 CRÍTICO | PENDIENTE |
| RF-002 | Bloqueo usuario inactivo | MOD-01 | CP-002, AA-002, AA-012, BD-009 | AUT, BD | 🔴 CRÍTICO | PENDIENTE |
| RF-003 | Rechazo credenciales incorrectas/vacías | MOD-01 | CP-003,CP-004,AA-002,AA-003,AA-004,F1-01..F1-10 | AUT, VAL | 🔴 CRÍTICO | PENDIENTE |
| RF-004 | Cierre sesión válido | MOD-01 | CP-005,AA-005,AA-009,INT-001 | AUT, INT | 🟠 ALTO | PENDIENTE |
| RF-005 | Admin accede Productos/Stock/Usuarios | MOD-02 | CP-006,CP-007,AA-007,AA-008,INT-001 | AUTZ, INT | 🔴 CRÍTICO | PENDIENTE |
| RF-006 | Vendedor NO accede rutas Admin | MOD-02 | CP-008,CP-009,CP-033,CP-034,AA-007,AA-011,INT-001,INT-006 | AUTZ, SEG, INT | 🔴 CRÍTICO | PENDIENTE |
| RF-007 | No autenticado redirect /login | MOD-02 | CP-010,AA-006,AA-009,INT-001 | AUTZ | 🔴 CRÍTICO | PENDIENTE |
| RF-008 | Registrar producto (cat, precio, imagen) | MOD-03 | CP-011,BD-002,F4-01,F4-02,F4-12,F4-14,INT-002 | FUNC, BD, VAL, INT | 🟠 ALTO | PENDIENTE |
| RF-009 | Listar solo productos activos | MOD-03 | CP-012, BD-004 | FUNC, BD | 🟠 ALTO | PENDIENTE |
| RF-010 | Movimientos Entrada/Salida/Ajuste actualizan stock | MOD-04 | CP-013,CP-014,CP-015,BD-010,INT-002,INT-008,F5-01,F5-02,F5-04 | FUNC, BD, VAL, INT | 🔴 CRÍTICO | PENDIENTE |
| RF-011 | Bloqueo Salida > stock | MOD-04 | CP-016,BD-016,F5-03 | NEG, RN, BD | 🔴 CRÍTICO | PENDIENTE |
| RF-012 | Registrar usuario (único, pass ≥6, confirmada) | MOD-02 | CP-017,CP-018,CP-036,CP-038,BD-001,BD-011,F2-01..F2-17,INT-006,INT-009 | FUNC, BD, VAL, INT | 🟠 ALTO | PENDIENTE |
| RF-013 | Editar usuario + activar/desactivar | MOD-02 | CP-019,CP-020,BD-006,BD-009,INT-006 | FUNC, BD, INT | 🟠 ALTO | PENDIENTE |
| RF-014 | Registrar cliente carnet único | MOD-05 | CP-021,CP-022,BD-003,BD-012,F3-01..F3-15 | FUNC, BD, VAL | 🟠 ALTO | PENDIENTE |
| RF-015 | Registrar Venta transaccional + stock | MOD-06 | CP-020,BD-010,BD-022,INT-002,INT-004,F6-01,F6-02,F6-05,F6-09 | FUNC, BD, INT, VAL | 🔴 CRÍTICO | PENDIENTE |
| RF-016 | Venta requiere al menos 1 producto cant>0 | MOD-06 | CP-022,F6-03,F6-04 | NEG, VAL | 🟠 ALTO | PENDIENTE |
| RF-017 | Crear pedido (cliente, fecha, anticipo, prod/torta) | MOD-07 | CP-023,CP-024,BD-019,INT-003,F7-01,F7-02,F7-03 | FUNC, BD, INT, VAL | 🔴 CRÍTICO | PENDIENTE |
| RF-018 | Pedido requiere ≥1 producto O torta precio>0 | MOD-07 | CP-025,F7-04,F7-05,F7-06,F7-07,INT-005 | NEG, VAL, INT | 🔴 CRÍTICO | PENDIENTE |
| RF-019 | Anticipo ≤ Total pedido | MOD-07 | CP-026,BD-023,F7-14,INT-005 | RN, VAL, BD, INT | 🔴 CRÍTICO | PENDIENTE |
| RF-020 | Pedidos NO descuentan stock | MOD-07 | CP-023,CP-030,INT-003,BD-024 | RN, FUNC, INT, BD | 🟠 ALTO | PENDIENTE |
| RF-021 | Actualizar estado de pedido | MOD-07 | CP-027,BD-007,INT-003 | FUNC, BD, INT | 🟠 ALTO | PENDIENTE |
| RF-022 | Registrar pago final ≤ saldo | MOD-07 | CP-028,CP-029,BD-023,INT-003,F8-01..F8-08 | RN, VAL, BD, INT | 🔴 CRÍTICO | PENDIENTE |
| RF-023 | Dashboard Admin métricas correctas | MOD-08 | CP-031,BD-004,INT-007 | FUNC, BD, INT | 🟡 MEDIO | PENDIENTE |
| RF-024 | Dashboard Vendedor sus propias métricas | MOD-08 | CP-032,INT-001 | FUNC, INT | 🟡 MEDIO | PENDIENTE |
| RF-025 | Torta personalizada 8 campos + precio | MOD-07 | CP-024,INT-003,F7-16,F7-17,F7-18 | FUNC, VAL, INT | 🟡 MEDIO | PENDIENTE |
| RNF-001 | Contraseñas hasheadas (no texto plano) | Seguridad | CP-036,AA-013,BD-001 | SEG, BD | 🔴 CRÍTICO | PENDIENTE |
| RNF-002 | Rutas protegidas middleware auth/role | Seguridad | AA-006,AA-007,AA-008,AA-011,AA-015 | SEG, AUTZ | 🔴 CRÍTICO | PENDIENTE |
| RNF-003 | Operaciones multi-tabla DB::transaction() | Integridad | CP-020,CP-023,CP-013,INT-002,INT-003,INT-004,INT-005,BD-010,BD-013,BD-014,BD-020,BD-021,BD-022,BD-023 | INT, BD, SEG | 🔴 CRÍTICO | PENDIENTE |
| RNF-004 | Imagen producto ≤2MB y jpg/jpeg/png/webp | Rend/Seg | CP-037,F4-12,F4-13,F4-14,F4-15,INT-010 | VAL, SEG | 🟠 ALTO | PENDIENTE |
| RNF-005 | Mensajes error/éxito claros ES | UX | Todos CP, F, AA, BD, INT | UX | 🟡 MEDIO | PENDIENTE |
| RNF-006 | Password usuario min 6 | Seguridad | CP-038,F2-08,F2-09,INT-009 | VAL, SEG | 🟠 ALTO | PENDIENTE |
| RNF-007 | HTTPS producción | Seguridad | — [REQ INF] | SEG | 🔴 CRÍTICO | NO APLICA (Fuera alcance) |
| RNF-008 | Página carga ≤3s | Rendimiento | Prompt 9 completo (ESC-01..ESC-06) | REND | 🟡 MEDIO | PENDIENTE |
| RNF-009 | Usuario y carnet UNIQUE | Integridad | CP-018,CP-022,BD-011,BD-012,BD-017 | BD, VAL | 🟠 ALTO | PENDIENTE |
| RNF-010 | Soft delete productos y categorías | Integridad | BD-008,BD-020 | BD | 🟡 MEDIO | PENDIENTE |

---

## Verificación de las 5 reglas de trazabilidad

### 1. ¿Cada requisito tiene al menos 1 caso asociado?

| Grupo | Total | Con ≥1 CP | % Cobertura |
|-------|-------|-----------|-------------|
| RF (funcionales) | 25 | 25 | **100%** |
| RNF (no funcionales) | 10 | 9* | **90%** |
| Total RF + RNF | 35 | 34 | **97.1%** |

> *RNF-007 (HTTPS) se excluye explícitamente como fuera de alcance (entorno local). Cobertura real válida: 34/34 = 100% en fases de testing.*

**Conclusión 1:** ✅ 97.1% global; 100% RF. Cumplimiento excelente para titulación.

---

### 2. ¿Existen casos de prueba sin requisito relacionado?

| Caso sin RF | ¿Aplica? | Acción |
|-------------|----------|--------|
| CP-035 (Cancelar en Venta) | RNF-005 UX | ✅ Ya mapeado |
| INT-010 (Navegación Cancelar/Volver) | RNF-005 UX | ✅ Incluido en RNF-005 |
| BD-015 (NOT NULL constraint) | RNF-003 Integridad trans/BD | ✅ Incluido implícito RNF-003 |
| BD-018 (findOrFail 404) | Podría formalizarse | Sugerencia: formalizar RF-026 "Recurso inexistente → 404" o mantener implícito |

**Conclusión 2:** ✅ No hay CP huérfanos críticos. Todos mapeables. Solo RF-026 opcional para formalismo.

---

### 3. ¿Requisitos CRÍTICOS tienen cobertura SUFICIENTE? (≥ 3 CP se considera buena)

| Requisito CRÍTICO | Cantidad CP asociados | ¿Cobertura? |
|-------------------|-----------------------|-------------|
| RF-001 Login OK | 3 | ✅ Buena |
| RF-002 Bloqueo inactivo | 4 | ✅ Buena |
| RF-003 Credenciales KO | 7 | ✅ Excelente |
| RF-005 Admin → rutas Admin | 5 | ✅ Buena |
| RF-006 Vendedor bloqueado | 8 | ✅ Excelente |
| RF-007 Sin sesión bloqueado | 4 | ✅ Buena |
| RF-010 Stock Movimientos | 9 | ✅ Excelente |
| RF-011 Salida > stock | 3 | ✅ Buena |
| RF-015 Venta transaccional | 9 | ✅ Excelente |
| RF-017 Pedido creación | 7 | ✅ Excelente |
| RF-018 Pedido total>0 | 6 | ✅ Excelente |
| RF-019 Anticipo ≤ total | 4 | ✅ Buena |
| RF-022 Pago ≤ saldo | 4 | ✅ Buena |
| RNF-001 Password hash | 3 | ✅ Buena |
| RNF-002 Middleware | 5 | ✅ Buena |
| RNF-003 Transacciones | 14 | ✅ Excelente |
| RNF-007 HTTPS | 0 | ❌ Fuera alcance (no bloqueante) |

**Conclusión 3:** ✅ 16/17 requisitos CRÍTICOS cobertura buena/excelente (3 a 14 CP). Único pendiente: RNF-007 = entorno despliegue.

---

### 4. Requisitos SIN pruebas identificados

| ID | Estado | Acción recomendada |
|----|--------|--------------------|
| RNF-007 HTTPS | ❌ Sin CP | Incluir en checklist de despliegue. No probar en local. |
| RF-026 (opcional) 404 recurso inexistente | ⚠️ Parcial | BD-018 cubre; puede formalizarse RF si se requiere. |
| RF-027 (opcional) Búsqueda/filtrado/paginación | ⚠️ REQ-VER | Si existe UI → agregar CP. Si no (actualmente): NO APLICA. |
| RF-028 (opcional) Reportes PDF/Excel | ⚠️ REQ-VER | Actualmente no implementado; no aplica. |

**Conclusión 4:** Solo RNF-007 queda sin cobertura (fase despliegue, no testing). 2 RF opcionales depende de confirmación.

---

### 5. Casos de prueba que deberían REVISARSE antes de ejecutar

| Caso | Motivo | Acción recomendada |
|------|--------|--------------------|
| CP-012, BD-008, RNF-010 | Depende de existencia ruta DELETE producto + soft delete UI | Confirmar rutas DELETE. Si no hay vía UI, marcar NO APLICA. Ver si producto puede desactivarse por estado=0. |
| AA-012 Usuario desactivado en sesión | Comportamiento ambiguo por defecto Laravel | Documentar comportamiento real. Si se desea auto-logout, reportar mejora. |
| INT-004 Race condition venta | Prueba manual compleja | 2 testers sincronizados / Postman Collection Runner paralelo. |
| F2-15, F3-15 (XSS) | Confirmar autoescape Blade `{{ }}` | Ver visualmente tras guardar que no ejecuta JS. |
| Prompt 9 ESC-05 (10 concurrentes) | Requiere semilla grande + 10 sesiones | Confirmar disponibilidad recursos; si no, documentar NO APLICA con justificación. |
| BD-016 CHECK stock≥0 a nivel BD | Posible DEFECTO diseño | Si INSERT directo stock=-1 funciona → reportar DEFECTO CRÍTICO (falta constraint CHECK BD) |
| BD-013, BD-014 FK a nivel BD | Falta migraciones definidas | Antes de ejecutar, correr SHOW CREATE TABLE y confirmar cláusula FOREIGN KEY. Si no existe a nivel BD → DEFECTO CRÍTICO. |

---

## 📊 Resumen general de cobertura

| Grupo | Total Requisitos | Con ≥1 CP | % Cobertura |
|-------|-------------------|-----------|-------------|
| RF Funcionales | 25 | 25 | **100%** |
| RNF No funcionales | 10 | 9* | **90%** |
| Requisitos CRÍTICOS | 17 | 16 | **94.1%** |
| Requisitos ALTO | 11 | 11 | **100%** |
| Requisitos MEDIO | 7 | 7 | **100%** |
| **TOTAL (general)** | **35** | **34** | **97.1%** |

*RNF-007 (HTTPS) se excluye de testing local (es despliegue). Si contamos solo testing: 34/34 = 100%.

---

## Conclusiones de la trazabilidad

1. ✅ **Cobertura global ≥ 97%**, excelente para proyecto de titulación.
2. ✅ **100% RF funcionales (25/25)** cubiertos por ≥ 1 caso de prueba.
3. ✅ **16/17 requisitos CRÍTICOS** con cobertura robusta (3 a 14 casos cada uno).
4. ⚠️ **Requisitos pendientes por confirmar:**
   - RNF-007 HTTPS (entorno despliegue).
   - Existencia de rutas DELETE para soft delete productos/categorías.
   - FK y constraints CHECK a nivel MySQL (revisar migraciones).
   - RF opcionales: RF-026 (404 formal), RF-027 (búsqueda paginación), RF-028 (reportes).
5. ⚠️ **Recomendado antes ejecutar pruebas:**
   - Completar migraciones reales database/migrations/*.php acordes a los modelos.
   - Ejecutar DatabaseSeeder con roles, estados, tipos, categorías.
   - Confirmar IDs exactos catálogos con el asesor.
6. ✅ **Documento apto para proyecto de titulación.** La matriz puede imprimirse y adjuntarse al Informe Final de Pruebas como anexo de cumplimiento.
