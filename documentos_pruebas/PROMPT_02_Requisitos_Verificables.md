# PROMPT 2 — Identificación de requisitos verificables

**Nombre del proyecto:** VALGREEN — Sistema de Gestión de Repostería

Los requisitos se extraen directamente del código (controladores, validaciones, rutas, middleware y modelos).

---

## Tabla de Requisitos Verificables

| ID | Tipo | Requisito | Módulo | Prioridad | Criterio de aceptación | Cómo puede verificarse | Caso(s) relacionado(s) |
|----|------|-----------|--------|-----------|------------------------|------------------------|--------------------------|
| RF-001 | Funcional | El sistema debe permitir el inicio de sesión con usuario y contraseña válidos | Autenticación | CRÍTICO | Usuario activo con credenciales correctas accede al dashboard y genera nueva sesión | Ingresar credenciales válidas y verificar redirección a /dashboard y sesión activa | CP-001 |
| RF-002 | Funcional | El sistema debe rechazar el inicio de sesión si el usuario tiene estado inactivo | Autenticación | CRÍTICO | Usuario inactivo con contraseña correcta recibe error y no accede | Intentar login con usuario desactivado y verificar permanencia en login con error | CP-002 |
| RF-003 | Funcional | El sistema debe rechazar credenciales incorrectas | Autenticación | CRÍTICO | Mensaje de error mostrado, sin acceso al sistema | Probar contraseña incorrecta y usuario inexistente | CP-003, CP-004 |
| RF-004 | Funcional | El sistema debe cerrar la sesión al invocar logout | Autenticación | ALTA | Sesión invalidada, regeneración de token CSRF, redirección a /login | Hacer clic en logout y verificar que no se pueda navegar a /dashboard sin login | CP-005 |
| RF-005 | Funcional | Usuario con rol Administrador puede acceder a gestión de productos, stock y usuarios | Permisos | CRÍTICO | Rutas /productos, /productos/create, /stock/create, /usuarios, /usuarios/create responden 200 | Iniciar sesión como Admin y navegar a cada ruta | CP-006, CP-007 |
| RF-006 | Funcional | Usuario con rol Vendedor NO puede acceder a gestión de productos, stock ni usuarios | Permisos | CRÍTICO | Al intentar acceder recibe HTTP 403 "No tienes permiso..." | Iniciar sesión como Vendedor y acceder directamente por URL | CP-008, CP-009 |
| RF-007 | Funcional | Usuario no autenticado es redirigido a /login al acceder a cualquier ruta protegida | Permisos | CRÍTICO | Todas las rutas dentro del middleware auth redirigen a login | Sin iniciar sesión, navegar a /dashboard, /ventas, /pedidos | CP-010 |
| RF-008 | Funcional | El sistema debe permitir registrar un producto con categoría, nombre, descripción, precio e imagen | Productos | ALTA | Producto guardado en BD, precio ≥ 0, nombre ≤ 100, imagen ≤ 2MB (jpg/png/webp) | Llenar formulario correcto, enviar y verificar listado + BD | CP-011 |
| RF-009 | Funcional | El sistema debe listar solo productos activos (estado=1) con su categoría | Productos | ALTA | Listado /productos muestra solo estado=1, incluye nombre de categoría | Consultar listado, verificar producto desactivado no aparece | CP-012 |
| RF-010 | Funcional | El sistema debe registrar movimiento de stock y actualizar cantidad | Stock | CRÍTICO | stock.cantidad refleja el movimiento; movimientos_stock registra la operación | Probar Entrada (suma), Salida (resta), Ajuste (reemplaza) | CP-013, CP-014, CP-015 |
| RF-011 | Funcional | No debe permitirse Movimiento de Salida si cantidad > stock disponible | Stock | CRÍTICO | Operación abortada con error 422, stock sin cambios | Intentar salida mayor al stock actual | CP-016 |
| RF-012 | Funcional | El sistema debe permitir registrar usuarios con rol, datos, usuario único, carnet único y contraseña ≥6 confirmada | Usuarios | ALTA | Usuario guardado con pass hasheado; errores ante duplicados o contraseña no coincidente | Completar formulario, probar duplicados, probar contraseñas distintas | CP-017, CP-018 |
| RF-013 | Funcional | El sistema debe permitir editar usuario (cambio opcional contraseña) y activar/desactivar | Usuarios | ALTA | Datos actualizados, unicidad mantiene (excluyendo self), estado togglea 1↔0 | Editar sin cambiar contraseña, editar cambiando contraseña, cambiar estado | CP-019, CP-020 |
| RF-014 | Funcional | El sistema debe permitir registrar clientes con carnet único | Clientes | ALTA | Cliente guardado; carnet repetido → error de validación | Completar formulario correcto y con carnet duplicado | CP-021, CP-022 |
| RF-015 | Funcional | El sistema debe registrar una venta directa, descontar stock y guardar detalle transaccionalmente | Ventas | CRÍTICO | Si todo OK: venta + detalle_venta + stock decrementado; si error → rollback total | Venta con productos OK; Venta con 1 producto excediendo stock | CP-023, CP-024 |
| RF-016 | Funcional | La venta debe tener al menos un producto con cantidad > 0 | Ventas | ALTA | Error "Debe seleccionar al menos un producto." | Enviar formulario de venta con todas las cantidades en 0 | CP-025 |
| RF-017 | Funcional | El sistema debe registrar un pedido con cliente, fecha entrega, anticipo, productos y/o torta | Pedidos | CRÍTICO | Pedido creado, estado inicial = 1 (Pendiente), total ≥ anticipo ≥ 0 | Pedido con solo productos; pedido con solo torta; pedido con ambos | CP-026, CP-027 |
| RF-018 | Funcional | El pedido debe tener al menos un producto (cant >0) o torta con precio >0 | Pedidos | CRÍTICO | Error: "Debe agregar al menos un producto o una torta personalizada." | Enviar pedido sin productos ni torta, o torta sin precio | CP-028 |
| RF-019 | Funcional | El anticipo del pedido no debe exceder el total | Pedidos | CRÍTICO | Error: "El anticipo no puede ser mayor que el total del pedido." | Pedido con anticipo > total calculado | CP-029 |
| RF-020 | Funcional | Los pedidos NO descuentan stock automáticamente | Pedidos | ALTA | Después de crear pedido, stock.cantidad permanece igual | Crear pedido y verificar que stock no cambia | CP-030 |
| RF-021 | Funcional | El sistema debe permitir actualizar el estado de un pedido | Pedidos | ALTA | estado_pedido_id actualizado, updated_by registra usuario | Cambiar de Pendiente → En preparación → Listo → Entregado | CP-031 |
| RF-022 | Funcional | El sistema debe permitir registrar pago final de pedido, monto ≤ saldo | Pedidos | CRÍTICO | pago_final incrementa, saldo decrementa; pago > saldo → error | Pago exacto igual al saldo; pago parcial; pago > saldo | CP-032, CP-033 |
| RF-023 | Funcional | Dashboard Admin muestra métricas: ventas hoy, pedidos activos, usuarios activos, stock bajo, pedidos entrega hoy | Dashboard | MEDIA | Valores numéricos calculados correctamente desde la BD | Insertar datos y verificar Dashboard muestra los totales correctos | CP-034 |
| RF-024 | Funcional | Dashboard Vendedor muestra: ventas hoy, pedidos pendientes, pedidos entrega hoy | Dashboard | MEDIA | Métricas aparecen según rol | Iniciar sesión como Vendedor y verificar métricas mostradas | CP-035 |
| RF-025 | Funcional | Torta personalizada debe aceptar: porciones, sabor, relleno, cobertura, decoración, mensaje, observaciones, precio | Pedidos | MEDIA | Si sabor+precio completos, se guarda detalle_torta_personalizada | Completar datos de torta y verificar registro | CP-027 |
| RNF-001 | No Funcional | Las contraseñas deben almacenarse hasheadas (no en texto plano) | Seguridad | CRÍTICO | Campo password en BD no es legible (hash Bcrypt/Argon2) | Consultar tabla usuarios y verificar formato hash | CP-036 |
| RNF-002 | No Funcional | Rutas protegidas usan middleware auth y role | Seguridad | CRÍTICO | Rutas admin/rol dentro de groups middleware correspondientes | Revisión de routes/web.php + prueba de acceso URL | CP-006 al CP-010 |
| RNF-003 | No Funcional | Operaciones multi-tabla deben ser transaccionales | Integridad | CRÍTICO | Uso de DB::transaction() en Pedido store, Venta store, Stock store | Verificar uso de transacciones + probar fallo mitad de operación | CP-024, CP-030 |
| RNF-004 | No Funcional | Imágenes de producto ≤ 2MB y formato jpg/jpeg/png/webp | Rendimiento/Seguridad | ALTA | Archivo mayor a 2MB → error; pdf/exe → error | Subir imagen 3MB; .exe; .png válido | CP-037 |
| RNF-005 | No Funcional | Mensajes de error y éxito claros en idioma español | UX | MEDIA | Todos controladores usan with('success') o withErrors en español | Navegar todo el sistema y tomar captura de mensajes | CP-xxx (todos) |
| RNF-006 | No Funcional | Contraseña de usuario debe tener mínimo 6 caracteres | Seguridad | ALTA | Validación falla con 5 caracteres | Probar crear usuario con contraseña de 5 caracteres | CP-038 |
| RNF-007 | No Funcional | Aplicación debe usar HTTPS en producción [AMBIGUO - no confirmado] | Seguridad | CRÍTICO | Encabezados HSTS y redirección HTTP→HTTPS activos | Desplegar y verificar con navegador | Requiere definición |
| RNF-008 | No Funcional | Página debe cargar en ≤ 3 segundos [INCOMPLETO] | Rendimiento | MEDIA | Tiempo respuesta servidor ≤ 3s bajo 1 usuario | Cronómetro manual o DevTools Network | Requiere definición |
| RNF-009 | No Funcional | Nombres de usuario y carnet deben ser únicos por registro | Integridad | ALTA | Restricción UNIQUE en BD + validación Laravel | Insertar duplicado y verificar error (BD y Laravel) | CP-018, CP-022 |
| RNF-010 | No Funcional | Soft delete para productos y categorías [AMBIGUO] | Integridad | MEDIA | Productos eliminados no aparecen en listado pero persisten en BD | Si hay implementación, probar delete y restauración | Requiere definición |

---

## Información adicional necesaria para completar la matriz

1. Confirmar existencia de migraciones personalizadas (estructura exacta de cada tabla).
2. Confirmar IDs y nombres exactos de roles, estados_pedido, tipos_movimiento.
3. RNF-007 (HTTPS): ¿Requisito obligatorio?
4. RNF-008 (Tiempo de carga): ¿Valor umbral aceptable?
5. RNF-010 (Soft delete): ¿Implementado? ¿Rutas para restaurar?
6. ¿Reportes PDF/Excel, búsqueda/filtrado avanzado, paginación?
7. Requisitos de accesibilidad (WCAG) / responsive mobile.
8. Requisito de backup y recuperación.
