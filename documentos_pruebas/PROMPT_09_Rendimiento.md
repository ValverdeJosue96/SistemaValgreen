# PROMPT 9 — Plan básico de pruebas de rendimiento

**Proyecto:** VALGREEN — Sistema de Gestión de Repostería
**Contexto:** Proyecto académico (Titulación)
**Stack:** Laravel 11 / PHP 8.2 / MySQL 8 / Apache/Nginx
**Tipo aplicación:** Monolítico web (Blade + Bootstrap)

---

## 1. Descripción del sistema para rendimiento

VALGREEN gestiona repostería. Uso esperado real: 2-10 usuarios simultáneos pico. Tamaño de BD moderado (1 000 a 50 000 registros por tabla en 2 años). Operaciones escritura (ventas/pedidos/stock) horario atención. Consultas y métricas dashboard todo el día.

- Usuarios concurrentes promedio: 3
- Usuarios concurrentes pico: 10
- Usuarios activos cartera: < 20 cuentas

**Operaciones críticas a medir (10):**

| ID | Operación | Impacto |
|----|-----------|---------|
| OP-01 | Inicio de sesión | Punto único acceso |
| OP-02 | Carga Dashboard Admin (6 consultas agregadas) | Mayor carga de queries |
| OP-03 | Listado Ventas / Pedidos (JOIN múltiples, N filas) | Prueba paginación/carga |
| OP-04 | Registro Venta (transacción + lockForUpdate) | Transaccional y locks |
| OP-05 | Registro Pedido Mixto (transacción + cálculos) | Múltiples inserts |
| OP-06 | Detalle Pedido (Eager Loading 4 relaciones) | Complejidad queries |
| OP-07 | Movimiento Stock Salida + validación stock | Transacción simple |
| OP-08 | Crear Producto con subida imagen 2MB | I/O disco + validación MIME |
| OP-09 | Registrar Pago Final Pedido | UPDATE consistencia |
| OP-10 | Consulta Stock completo (Producto hasOne Stock) | Consulta JOIN hasOne |

---

## 2. Métricas a registrar (10)

| Categoría | Métrica | Unidad | Obtención |
|-----------|---------|--------|-----------|
| Respuesta servidor | TTFB (Time To First Byte) | ms | Chrome DevTools → Network → TTFB |
| Carga total | Tiempo evento `load` | s | DevTools → Network |
| PHP procesamiento | Backend response time | ms | Laravel Debugbar o Clockwork |
| Consultas BD | Total queries / tiempo total / query más lenta | Cant + ms | Debugbar Queries tab o DB::getQueryLog() |
| Número queries SQL | Conteo por request | Cant | Debugbar |
| Memoria PHP | Pico por request | MB | Debugbar o memory_get_peak_usage |
| HTTP status | % 2xx, 4xx, 5xx | % | DevTools Status |
| Errores durante prueba | Cantidad y tipo | uds | Registro manual |
| Tamaño respuesta | Payload HTML | KB | DevTools → Size |
| Uso CPU/RAM servidor | % uso | % | Task Manager / `top` Linux |

---

## 3. Escenarios a probar (6)

| ID | Nombre | Condiciones datos | Operaciones | Usuarios conc. |
|----|--------|-------------------|-------------|----------------|
| ESC-01 | Nominal | Datos mínimos (10 prod, 10 ventas, 10 pedidos) | OP-01 a OP-10 | 1 usuario secuencial |
| ESC-02 | Datos moderados | 100 prod / 500 ventas / 500 pedidos | OP-02, OP-03, OP-06, OP-10 | 1 |
| ESC-03 | Datos altos (tamaño 2 años) | 500 prod / 5000 ventas / 5000 pedidos | OP-02, OP-03, OP-06, OP-10 | 1 |
| ESC-04 | Concurrencia baja (realista pico) | ESC-02 | OP-04 Venta + OP-05 Pedido | 3 concurrentes |
| ESC-05 | Concurrencia pico estimada | ESC-02 | OP-04 y OP-05 (transaccional) | 10 concurrentes |
| ESC-06 | Subida archivos | ESC-01 | OP-08 10 imágenes 2MB | 1 |

> **Concurrencia sin herramientas:** 3-10 navegadores privados simultáneos / 2 equipos / curl `Start-Job` PowerShell. Para titulación: 10 pestañas simultáneas aceptable.

---

## 4. Datos a utilizar (por escenario)

| Tabla | ESC-01 | ESC-02 | ESC-03 | Generación |
|-------|--------|--------|--------|------------|
| roles | 2 | 2 | 2 | Manual / Seeder |
| usuarios | 3 | 10 | 20 | User::factory + Rol |
| categorias | 3 | 10 | 20 | Seeder |
| productos | 10 | 100 | 500 | Factory |
| stock | 10 | 100 | 500 | Junto a producto |
| clientes | 5 | 200 | 1000 | Factory |
| estados_pedido | 5 | 5 | 5 | Manual |
| pedidos | 10 | 500 | 5000 | Factory + estado random |
| detalle_pedido | 20 | 2500 | 20000 | Factory |
| detalle_torta | 3 | 100 | 500 | Factory |
| ventas | 10 | 500 | 5000 | Factory |
| detalle_venta | 20 | 2500 | 20000 | Factory |
| tipos_movimiento | 3 | 3 | 3 | Manual |
| movimientos_stock | 20 | 500 | 5000 | Factory |

**Recomendación Laravel:** Crear factories + DatabaseSeeder con comando:
`php artisan db:seed --class=PerformanceSeeder --size=small|medium|large`

---

## 5. Herramientas recomendadas

| Nivel | Herramienta | Propósito | Instalación |
|-------|-------------|-----------|-------------|
| 🔰 Básico (recomendado Titulación) | Chrome DevTools (F12) | TTFB, Load, Size, Status | Incluido Chrome |
| 🔰 Básico | Laravel Debugbar (barryvdh) | Tiempo, Queries, Memoria, Vistas | composer require --dev barryvdh/laravel-debugbar |
| 🔰 Básico | Excel / Google Sheets | Promedios, desviaciones, gráficos | Libre |
| 🟡 Intermedio | Clockwork | Profiling alternativo | composer require itsgoingd/clockwork |
| 🟡 Intermedio | Apache Bench (ab) | Carga simple sin sesión | Incluido XAMPP/WAMP |
| 🔴 Avanzado | JMeter / k6 / Locust | Carga con sesión multi-endpoint | Descargar / pip install |
| 🔴 Avanzado | Laravel Telescope | Request watcher, queries, excepciones | composer require --dev laravel/telescope |

**Stack recomendado VALGREEN (proyecto académico):** Chrome DevTools + Debugbar + Excel.

---

## 6. Criterios de aceptación (umbrales tentativos)

Ajustar y confirmar con el asesor/PO.

| Escenario | Operación | TTFB servidor ≤ | Adicional |
|-----------|-----------|-----------------|-----------|
| ESC-01 Nominal | OP-01 Login | 800 ms | 100% éxito |
| ESC-01 Nominal | OP-02 Dashboard Admin | 1500 ms | ≤ 20 consultas SQL |
| ESC-01 Nominal | OP-04 Venta | 1500 ms | 0 error, transacción OK |
| ESC-01 Nominal | OP-05 Pedido | 1500 ms | 0 error, transacción OK |
| ESC-01 Nominal | OP-08 Imagen 2MB | 3000 ms | Imagen guardada storage/ |
| ESC-02 Moderada | OP-03 Listado 500 ventas | 2000 ms | ≤ 5 queries (sin N+1) |
| ESC-03 Alta | OP-02 Dashboard 5000 ventas | 3000 ms | Tiempo BD ≤ 50% tiempo total |
| ESC-04 Concurrencia 3 | OP-04 + OP-05 | 3000 ms promedio | 0% error (ningún 500/422 inesperado) |
| ESC-05 Concurrencia 10 | OP-04 Venta (lock) | 5000 ms promedio | ≤ 5% timeout/lock; 0 inconsistencias stock |
| ESC-06 Archivos | OP-08 Subida múltiple | 5000 ms/petición | Todas en storage/ |

**Umbrales globales:**
- Éxito HTTP 2xx/3xx ≥ 95%
- Error 5xx = 0%
- Cero problemas N+1 (Debugbar: 1 query por relación + 1 principal)

---

## 7. Cómo documentar resultados

### 7.1 Plantilla por operación / escenario

```
===========================================================================
RENDIMIENTO — Resultado ESC-__ : [Nombre]
Operación medida: OP-__ : [Nombre operación]
Fecha / Hora: ________________  Tester: ______________
Entorno: PHP __  MySQL __   RAM servidor __ MB
Datos cargados: (tamaño ESC-01/02/03)
Herramienta: Chrome DevTools + Debugbar
===========================================================================

Iteración | TTFB ms | Total Load s | N° Queries | Tiempo Queries ms | Mem MB | HTTP Code | Errores
----------|---------|--------------|------------|-------------------|--------|-----------|--------
1         |         |              |            |                   |        |           |
2         |         |              |            |                   |        |           |
3         |         |              |            |                   |        |           |
4         |         |              |            |                   |        |           |
5         |         |              |            |                   |        |           |
==========|=========|==============|============|===================|========|===========|========
PROMEDIO  |         |              |            |                   |        |           |
MÍNIMO    |         |              |            |                   |        |           |
MÁXIMO    |         |              |            |                   |        |           |

¿Cumple Criterio de Aceptación?  □ SÍ   □ NO    ¿Por qué?
________________________________________________________________________

Observaciones / Capturas:
- [ ] Captura DevTools Network timeline
- [ ] Captura Debugbar Queries con query más lenta resaltada
```

### 7.2 Informe final de rendimiento (11 secciones)

1. **Carátula:** Título, proyecto, equipo, fecha.
2. **Resumen Ejecutivo:** Conclusiones clave 1 párrafo.
3. **Entorno de Prueba:** Versiones PHP/MySQL, RAM, CPU, servidor.
4. **Datos Semilla Utilizados:** Cantidades por tabla por ESC.
5. **Resultados por Escenario:** Tabla resumen promedios vs umbral.
6. **Gráficos comparativos:** Barras. Promedio TTFB por ESC × Operación. (Excel/Sheets)
7. **Análisis consultas lentas:** Top 3 queries más lentas (Debugbar).
8. **Problemas detectados:** Memory leaks, N+1 queries, assets pesados.
9. **Recomendaciones optimización:** Índices BD, Eager Loading, paginación, caché, compresión assets.
10. **Conclusiones:** ¿Cumple criterios? ¿Recomendado para producción?
11. **Anexos:** Capturas DevTools/Debugbar por iteración.

---

> **Nota final académica:** Dado que VALGREEN está orientado a 2-5 usuarios simultáneos reales, el sistema probablemente supere ESC-01/02/04 con holgura. Si aparecen problemas en ESC-03 (5000 pedidos) o ESC-05 (10 concurrentes), se pueden documentar como oportunidades de mejora en futuras versiones **sin bloquear el pase a producción**, siempre y cuando se cumplan los escenarios nominales reales.
