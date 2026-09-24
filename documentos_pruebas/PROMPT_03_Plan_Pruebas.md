# PROMPT 3 — Plan de Pruebas Formal

**Proyecto:** VALGREEN — Sistema de Gestión de Repostería
**Versión del Plan:** 1.0
**Fecha de elaboración:** 2026-09-23
**Estado:** Borrador (pendiente de aprobación)

---

## 1. Introducción

El presente Plan de Pruebas define la estrategia, alcances, recursos, cronograma y criterios para la ejecución de pruebas del sistema VALGREEN, una aplicación web de gestión integral para repostería desarrollada con PHP (Laravel 11+), MySQL y Bootstrap 5. El plan cubre pruebas funcionales manuales, pruebas de seguridad (autenticación/autorización), pruebas de validación, pruebas de persistencia en base de datos, pruebas de integración de flujos y pruebas básicas de rendimiento. Este documento se elabora como parte de la documentación requerida para el proyecto de titulación.

## 2. Objetivo general de las pruebas

Garantizar que el sistema VALGREEN cumpla con los requisitos funcionales y no funcionales identificados, detectando la mayor cantidad posible de defectos antes del pase a producción, con especial atención en la seguridad de accesos, la integridad transaccional de ventas y pedidos, y la consistencia del inventario.

## 3. Objetivos específicos

1. Verificar el correcto funcionamiento del módulo de autenticación y control de acceso por roles (Administrador / Vendedor).
2. Validar que las reglas de negocio críticas (RN-01 a RN-10) se cumplan en todos los escenarios.
3. Comprobar la integridad de los datos almacenados en MySQL después de operaciones CRUD y transacciones.
4. Asegurar que todos los formularios apliquen validaciones de entrada (campos obligatorios, tipos, longitudes, formatos, duplicados).
5. Verificar el funcionamiento de los flujos end-to-end: registrar cliente → crear pedido → pagar → entregar.
6. Confirmar que los dashboards muestren métricas consistentes con los datos reales.
7. Detectar vulnerabilidades de acceso no autorizado por manipulación de URL.
8. Evaluar el rendimiento básico del sistema en escenarios de uso típico.

## 4. Alcance

**Incluido:**
- Módulo de Autenticación (inicio/cierre de sesión)
- Control de acceso por roles (middleware auth y role)
- Gestión de Usuarios (crear, editar, activar/desactivar)
- Gestión de Clientes (registro)
- Gestión de Productos (registro con imagen)
- Gestión de Stock y Movimientos (Entrada, Salida, Ajuste)
- Gestión de Ventas Directas (con descuento de stock transaccional)
- Gestión de Pedidos (registro con productos/torta, actualización de estado, pagos)
- Dashboard Administrador y Dashboard Vendedor
- Validaciones de entrada de todos los formularios
- Persistencia en MySQL (integridad referencial, constraints)
- Integración Rutas → Controladores → Modelos → Vistas

## 5. Fuera de alcance

- Pruebas automáticas unitarias (PHPUnit/Pest); solo se especifican pruebas manuales.
- Pruebas de carga/estrés avanzadas con JMeter, Locust.
- Pruebas cross-browser extensivas (solo Chrome/Firefox recomendados).
- Pruebas de seguridad avanzada (penetration testing, OWASP Top 10 completo).
- Pruebas de recuperación ante desastres / backup-restore.
- Pruebas en dispositivos móviles nativos (solo vista responsive desktop).
- Endpoints API REST (si existen); solo el sistema web con Blade.
- Funcionalidades no implementadas: edición/eliminación clientes, edición/eliminación productos, reportes PDF/Excel, búsqueda y filtrado.

## 6. Descripción del sistema a probar

VALGREEN es una aplicación monolítica desarrollada en Laravel 11 siguiendo el patrón MVC. El acceso a datos se realiza mediante Eloquent ORM sobre MySQL. La capa de presentación utiliza plantillas Blade con Bootstrap 5. La seguridad se implementa mediante middlewares auth y role personalizado. Las operaciones críticas (ventas, pedidos, movimientos de stock) utilizan DB::transaction() para garantizar atomicidad.

**Arquitectura lógica:**
- Capa de Rutas: routes/web.php
- Capa de Controladores: app/Http/Controllers/*
- Capa de Modelos: app/Models/* (14 modelos)
- Capa de Middleware: RoleMiddleware.php (RBAC)
- Capa de Vistas: resources/views/**/*.blade.php (20+ vistas)
- Capa de Persistencia: MySQL mediante migraciones.

## 7. Módulos incluidos

| ID Módulo | Nombre | Responsable funcional |
|-----------|--------|----------------------|
| MOD-01 | Autenticación y Sesiones | AuthController |
| MOD-02 | Gestión de Usuarios y Roles | UsuarioController |
| MOD-03 | Gestión de Productos y Categorías | ProductoController |
| MOD-04 | Gestión de Stock y Movimientos | StockController |
| MOD-05 | Gestión de Clientes | ClienteController |
| MOD-06 | Gestión de Ventas Directas | VentaController |
| MOD-07 | Gestión de Pedidos y Torta Personalizada | PedidoController |
| MOD-08 | Dashboard (Admin / Vendedor) | DashboardController |

## 8. Tipos de pruebas

| Tipo | Descripción | Herramientas |
|------|-------------|--------------|
| T1 - Pruebas Funcionales Manuales | Validación de cada requisito RF-001 a RF-025 | Navegador web, hoja de casos CP-xxx |
| T2 - Pruebas de Validación de Entrada | Campos vacíos, límites, formatos, duplicados | Navegador + datos de prueba |
| T3 - Pruebas de Seguridad / Autenticación | Login, logout, acceso por URL, restricción de roles | Navegador + edición manual de URL |
| T4 - Pruebas de Base de Datos y Persistencia | CRUD, FK, constraints, transacciones, soft delete | Navegador + phpMyAdmin / MySQL CLI |
| T5 - Pruebas de Integración (E2E) | Flujos completos entre múltiples módulos | Navegador + seguimiento de BD |
| T6 - Pruebas de Reglas de Negocio | Validación RN-01 a RN-10 | Navegador + escenarios diseñados |
| T7 - Pruebas de UI/Navegación | Enlaces, menús, redirecciones, mensajes | Navegador + inspección visual |
| T8 - Pruebas de Rendimiento Básicas | Tiempos de carga, consultas N+1 | DevTools Network / Clockwork (opcional) |

## 9. Estrategia de pruebas

**Priorización Riesgo × Impacto:**
- **Nivel ALTO:** Autenticación, roles, ventas transaccionales, pedidos (anticipo/pagos), stock salida.
- **Nivel MEDIO:** CRUD productos, usuarios, clientes, actualización estado pedido.
- **Nivel BAJO:** Dashboard, validaciones UX, navegación.

**Orden de ejecución sugerido:**
1. **Fase 1 — Smoke Testing:** Login de ambos roles, navegación básica, creación 1 registro por entidad.
2. **Fase 2 — Pruebas Funcionales:** CP-001 a CP-038 (casos por módulo).
3. **Fase 3 — Pruebas de Validación:** Escenarios negativos de campos.
4. **Fase 4 — Pruebas de Seguridad/Roles:** Autenticación/autorización.
5. **Fase 5 — Pruebas de BD:** Persistencia y transacciones.
6. **Fase 6 — Pruebas de Integración:** Flujos E2E.
7. **Fase 7 — Pruebas de Rendimiento:** Medición básica.
8. **Fase 8 — Pruebas de Regresión:** Repetición de casos críticos tras correcciones.
9. **Fase 9 — Evaluación final y pase a producción.**

## 10. Ambiente de pruebas

| Componente | Valor recomendado |
|------------|-------------------|
| Sistema Operativo | Windows 10/11 o Ubuntu 22.04 LTS |
| Servidor Web | Apache 2.4 / Nginx 1.20 |
| PHP | 8.2+ con extensiones mbstring, pdo_mysql, gd, fileinfo, openssl |
| Framework | Laravel 11.x (composer install) |
| Base de Datos | MySQL 8.0+ (collation utf8mb4_unicode_ci) |
| Navegador | Google Chrome 120+ o Mozilla Firefox 120+ |
| APP_ENV | testing o local; APP_DEBUG=true |
| Permisos | storage/ y bootstrap/cache/ escribibles |

## 11. Tecnologías utilizadas

- Laravel 11.x — Framework PHP
- Eloquent ORM — Mapeo objeto-relacional
- MySQL 8.0 — Motor de persistencia
- Blade + Bootstrap 5 — Capa presentación
- PHP Hash (Bcrypt/Argon2) — Almacenamiento seguro contraseñas
- PHPUnit 10+ — Framework testing (opcional automatización)
- Chrome DevTools — Medición tiempos y análisis red
- phpMyAdmin / HeidiSQL — Inspección base datos
- Google Sheets / Excel — Registro de resultados y evidencias

## 12. Datos de prueba

**Datos maestros (antes iniciar pruebas):**
- roles: ID=1 Administrador, ID=2 Vendedor
- estados_pedido: 1 Pendiente, 2 En preparación, 3 Listo, 4 Entregado, 5 Cancelado
- tipos_movimiento: 1 Entrada, 2 Salida, 3 Ajuste
- categorias: 2-3 categorías activas
- usuarios: 1 Admin activo, 1 Vendedor activo, 1 usuario inactivo
- productos: 5 productos (1 sin stock, 1 stock bajo ≤5, 3 stock normal)
- clientes: 3 clientes registrados

## 13. Roles y responsables

| Rol | Actividades |
|-----|-------------|
| QA Lead | Elaborar plan, asignar casos, revisar defectos, aprobar salida |
| Tester 1 (Estudiante) | Ejecutar pruebas Funcionales, Validación, UI |
| Tester 2 (Estudiante) | Ejecutar pruebas Seguridad, BD, Integración, Rendimiento |
| Desarrollador | Corregir defectos, proporcionar datos semilla |
| Product Owner | Aprobar requisitos, validar aceptación, firmar pase |

## 14. Criterios de entrada

Para iniciar la ejecución de pruebas se requiere:
1. Plan de Pruebas aprobado.
2. Ambiente de pruebas desplegado y verificado (login exitoso Admin).
3. Datos maestros cargados (Seeds o script SQL).
4. Casos de prueba diseñados y revisados.
5. Código fuente congelado en versión a probar (tag Git).
6. Permisos de escritura en storage/ para imágenes.
7. Plantilla registro de defectos disponible.

## 15. Criterios de salida

La fase de pruebas se da por terminada cuando:
1. 100% de los casos con prioridad CRÍTICA han sido ejecutados.
2. 90% de los casos ALTA prioridad ejecutados; ≥80% aprobados.
3. Cero defectos abiertos con severidad CRÍTICA o ALTA sin resolver.
4. No más del 10% de casos con severidad MEDIA pendientes.
5. Todas reglas negocio críticas RN-01 a RN-06 validadas.
6. Matriz de trazabilidad con cobertura ≥90% en RF críticos.
7. Informe de pruebas final elaborado y firmado.

## 16. Riesgos de las pruebas

| ID | Descripción | Probabilidad | Impacto | Mitigación |
|----|-------------|--------------|---------|------------|
| RIES-01 | Estructura de tablas no definida en migraciones | Alta | Alto | Confirmar script SQL / migraciones antes Fase 1 |
| RIES-02 | Falta de datos semilla reales | Media | Medio | Generar script SQL inserción datos maestros |
| RIES-03 | Vendedor accede ruta Admin no detectado | Media | Alto | Pruebas T3 exhaustivas con múltiples URL |
| RIES-04 | Venta concurrida cause stock negativo | Baja | Alto | Probar con dos navegadores simultáneos; revisar lockForUpdate |
| RIES-05 | Imágenes no se guardan por permisos storage/ | Media | Medio | Verificar permisos en prerequisitos; incluir caso CP-037 |
| RIES-06 | Cálculo totales/saldo con decimales imprecisos | Media | Medio | Verificar DECIMAL columnas y casting en modelos |
| RIES-07 | Tiempo insuficiente para cubrir casos | Media | Medio | Priorizar casos críticos; documentar no-ejecutados |

## 17. Gestión de defectos

**Ciclo de vida:** Detectado → Reportado → Asignado → En corrección → Pendiente verificación → Verificado OK → Cerrado.

**Plantilla reporte:**
- ID defecto (DEF-001 incremental)
- Resumen, caso relacionado, módulo
- Pasos reproducir, resultado esperado / obtenido
- Severidad CRÍTICA / ALTA / MEDIA / BAJA
- Prioridad fix P1 / P2 / P3
- Entorno, responsable, evidencias

## 18. Evidencias requeridas

1. Capturas pantalla (PNG/JPG) por cada caso (nombrado CP-XXX_fecha_N.png)
2. Log del sistema storage/logs/laravel.log con errores
3. Consultas SQL de verificación tras operaciones CRUD
4. Video/GIF (opcional) para flujos integración complejos
5. Hoja registro de ejecución: caso, resultado, observaciones, tester
6. Reporte de defectos con todos los DEF-XXX

## 19. Cronograma sugerido (8 semanas académicas)

| Semana | Actividad | Entregable |
|--------|-----------|------------|
| 1 | Elaborar Plan de Pruebas y Casos (Prompts 1-4) | Plan aprobado + CP listos |
| 2 | Ambiente + datos semilla + Smoke Testing | Ambiente OK + Informe Smoke |
| 3 | Fase 2: Funcionales MOD-01 a MOD-04 | Primer informe avance + DEF |
| 4 | Fase 2: Funcionales MOD-05 a MOD-08 + Fase 3 Validación | Segundo informe |
| 5 | Fase 4 Seguridad + Fase 5 BD | Tercer informe |
| 6 | Fase 6 Integración E2E + Fase 8 Rendimiento | Cuarto informe |
| 7 | Corrección defectos desarrollador + Fase 7 Regresión | Informe regresión |
| 8 | Evaluación Criterios de Salida, Matriz trazabilidad, Informe Final | Matriz + Informe Final + Acta pase |

## 20. Criterios para determinar si el sistema está listo para pasar a producción

1. Cero defectos CRÍTICOS abiertos.
2. Cero defectos ALTA abiertos sin plan mitigación PO.
3. Cobertura casos: ≥95% ejecutados CP críticos; ≥85% CP alto.
4. Matriz trazabilidad: cada RF-001 a RF-022 tiene al menos 1 caso PASADO.
5. Integración: 3 flujos E2E principales ejecutados OK.
6. Seguridad: Ninguna vulnerabilidad acceso URL sin login/rol.
7. Integridad BD: Auditoría 10 transacciones sin datos huérfanos.
8. Aprobación formal: Acta firmada por QA Lead + PO + Desarrollador.

## 21. Conclusiones

El Plan de Pruebas establece una ruta estructurada y medible para la validación del sistema VALGREEN. Dado que las funcionalidades más críticas dependen de la integridad transaccional y la separación de roles, se recomienda asignar mayor esfuerzo en Fases 4 (Seguridad) y 5 (Base de Datos). La calidad del ambiente de pruebas y de los datos semilla será determinante para obtener resultados confiables; se sugiere ejecutar primero Smoke Testing exhaustivo. Al finalizar, la matriz de trazabilidad confirmará la cobertura alcanzada y servirá como documento soporte para la aprobación de pase a producción del proyecto de titulación.
