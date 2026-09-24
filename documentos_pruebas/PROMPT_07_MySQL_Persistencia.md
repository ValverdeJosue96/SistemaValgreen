# PROMPT 7 — Pruebas de MySQL y persistencia

**Proyecto:** VALGREEN — Sistema de Gestión de Repostería
**Motor BD:** MySQL 8.0
**ORM:** Eloquent (Laravel)

---

## Información de la base de datos (14 tablas derivadas de modelos)

| ID | Tabla | Modelo | PK | Claves foráneas |
|----|-------|--------|----|-----------------|
| T01 | roles | Rol.php | id | — |
| T02 | usuarios | User.php | id | rol_id → roles.id |
| T03 | categorias | Categoria.php | id | — |
| T04 | productos | Producto.php | id | categoria_id → categorias.id |
| T05 | stock | Stock.php | id | producto_id → productos.id |
| T06 | tipos_movimiento | TipoMovimiento.php | id | — |
| T07 | movimientos_stock | MovimientoStock.php | id | producto_id, tipo_movimiento_id, created_by → usuarios |
| T08 | clientes | Cliente.php | id | — |
| T09 | estados_pedido | EstadoPedido.php | id | — |
| T10 | pedidos | Pedido.php | id | cliente_id, created_by (usuarios), updated_by (usuarios), estado_pedido_id |
| T11 | detalle_pedido | DetallePedido.php | id | pedido_id, producto_id |
| T12 | detalle_torta_personalizada | DetalleTortaPersonalizada.php | id | pedido_id |
| T13 | ventas | Venta.php | id | usuario_id → usuarios |
| T14 | detalle_venta | DetalleVenta.php | id | venta_id, producto_id |

---

## Pruebas de BD y Persistencia (BD-001 a BD-024)

### 1. Inserción correcta

**BD-001** Tabla `usuarios`
- **ID:** BD-001
- **Objetivo:** Usuario::create() inserta todos los campos + password hasheado.
- **Precondiciones:** Rol ID=2 existe.
- **Datos:** nombres="Test", primer_apellido="BD", segundo_apellido="Uno", carnet="TSTBD01", telefono="12345", rol_id=2, usuario="bd_user01", password="Plano123" (hash), estado=1
- **Pasos:** 1. UI /usuarios/create form → guardar. 2. SQL: SELECT * FROM usuarios WHERE usuario='bd_user01';
- **Resultado esperado:** 1 fila. Todos los campos coinciden. password ≠ "Plano123"; empieza "$2y$". created_at y updated_at NO NULL.
- **Evidencia:** Captura SELECT

**BD-002** Tabla `productos`
- **ID:** BD-002
- **Objetivo:** Insert producto con SoftDeletes.
- **Datos:** categoria_id=1, nombre="Producto BD Test", descripcion="Descripcion", precio=35.50, estado=1
- **Pasos:** UI crear producto. SQL SELECT id, categoria_id, nombre, precio, estado, deleted_at FROM productos WHERE nombre='Producto BD Test';
- **Resultado esperado:** Fila insertada. precio DECIMAL 35.50. deleted_at = NULL.
- **Evidencia:** Captura SQL

**BD-003** Tabla `clientes`
- **ID:** BD-003
- **Datos:** nombres="Cliente", primer_apellido="Prueba", segundo_apellido="BD", carnet="CLIBD01", telefono="777777"
- **Resultado esperado:** Registro insertado. campos NOT NULL. id autoincremental.

### 2. Consulta correcta

**BD-004** Producto + Categoría JOIN (Eager Loading)
- **ID:** BD-004
- **Precondiciones:** Categoría 1 con 2 productos.
- **Pasos:** UI /productos. Activar query log Laravel o DB::getQueryLog().
- **Resultado esperado:** 2 queries (1 categorias + 1 productos; NO N+1). Nombre categoría mostrado correctamente.
- **Evidencia:** Log queries si es posible + UI con categorías.

**BD-005** Pedido con múltiples relaciones (with)
- **ID:** BD-005
- **Pasos:** Ir a /pedidos y a /pedidos/{id}
- **Resultado esperado:** Consulta muestra cliente, estado, productos de detalle con nombre, datos torta. Ningún NULL en columnas clave.

### 3. Actualización

**BD-006** UPDATE usuarios (sin cambiar password)
- **ID:** BD-006
- **Precondiciones:** Usuario bd_user01. Password hash conocido (valor A).
- **Datos:** nombres="NombreActualizado", telefono="9999999"; password y confirmación VACÍOS.
- **Pasos:** 1. UI Editar usuario. 2. Actualizar nombres y teléfono. 3. SQL SELECT password, nombres, telefono, updated_at FROM usuarios WHERE id=X;
- **Resultado esperado:** nombres actualizado; telefono actualizado. password = valor A (idéntico). updated_at > created_at.
- **Evidencia:** SELECT comparando hash

**BD-007** UPDATE pedidos estado + updated_by
- **ID:** BD-007
- **Precondiciones:** Pedido id=1 estado_pedido_id=1. Usuario logueado id=5.
- **Datos:** estado_pedido_id=2
- **Resultado esperado:** estado_pedido_id=2; updated_by=5

### 4. Eliminación

**BD-008** Soft Delete productos (uso SoftDeletes)
- **ID:** BD-008
- **Objetivo:** Validar soft delete si existe ruta DELETE.
- **Precondiciones:** Producto creado BD-002.
- **Pasos:** 1. Ejecutar DELETE (si existe endpoint). 2. Consulta normal UI listado /productos. 3. SQL SELECT * FROM productos WHERE id=Z (mirar deleted_at). o desde Tinker Producto::withTrashed()->find(Z).
- **Resultado esperado:** Listado NO muestra. Consulta withTrashed SÍ devuelve deleted_at NOT NULL.
- **Observaciones:** [REQ-VER] Actualmente no hay rutas DELETE en web.php. Si no existen, marcar NO APLICA y crear DEFECTO si no puede desactivarse por UI (estado=0 sí existe en BD).
- **Evidencia:** SELECT deleted_at

**BD-009** Usuarios: borrado lógico por estado (desactivar)
- **ID:** BD-009
- **Precondiciones:** bd_user01 estado=1
- **Pasos:** 1. Listado → Desactivar. 2. SELECT id, estado FROM usuarios. 3. Intentar login.
- **Resultado esperado:** estado = 0. Fila sigue existiendo. Login bloqueado (RF-002).

### 5. Persistencia

**BD-010** Persistencia post-transacción: venta completa
- **ID:** BD-010
- **Precondiciones:** Venta CP-020 ejecutada OK (ProdA id=1 vendió 2 uds, stock inicial 10 → final 8, total=100).
- **Pasos:** 1. Cerrar navegador, reiniciar Apache/serve. 2. Login Admin. 3. 3 SELECT: ventas última, detalle_venta, stock producto 1.
- **Resultado esperado:** Todas filas persisten. Venta total=100; stock=8.
- **Evidencia:** Los 3 SELECT

### 6. Claves primarias (auto-inc + UNIQUE)

**BD-011** UNIQUE usuarios.usuario y carnet en BD
- **ID:** BD-011
- **Precondiciones:** bd_user01 (usuario="bd_user01", carnet="TSTBD01").
- **Pasos:** MySQL CLI: INSERT duplicado con mismo usuario y carnet.
- **Resultado esperado:** ERROR 1062 (23000): Duplicate entry... (2 errores separados en 2 INSERT distintos). 0 filas nuevas.
- **Evidencia:** Error SQL capturado

**BD-012** UNIQUE clientes.carnet en BD
- **ID:** BD-012
- **Pasos:** INSERT manual duplicado del carnet.
- **Resultado esperado:** Error 1062 Duplicate entry.

### 7. Claves foráneas (FK)

**BD-013** productos.categoria_id → categorias.id
- **ID:** BD-013
- **Pasos:** SQL: INSERT INTO productos (categoria_id, nombre, precio, estado_producto_id, estado) VALUES (9999, 'Prod sin cat', 10, 1, 1);
- **Resultado esperado:** ERROR 1452 (23000): Cannot add or update a child row: foreign key constraint fails.
- **Observaciones:** [REQ-VER] Depende si migraciones tienen ->foreign(). Si NO hay FK a nivel BD, DEFECTO CRÍTICO.

**BD-014** pedidos.cliente_id → clientes.id
- **ID:** BD-014
- **Pasos:** SQL INSERT pedido cliente_id=99999.
- **Resultado esperado:** Error 1452 FK.

### 8. Restricciones (CHECK, DEFAULT, NOT NULL)

**BD-015** usuarios.estado NOT NULL
- **ID:** BD-015
- **Pasos:** SQL INSERT usuarios estado NULL.
- **Resultado esperado:** ERROR 1048 (23000): Column 'estado' cannot be null.

**BD-016** stock.cantidad CHECK ≥ 0
- **ID:** BD-016
- **Pasos:** 1. Stock inicial=3. 2. SQL directo INSERT stock cantidad=-1 (o UPDATE).
- **Resultado esperado:** Si hay CHECK constraint definido → error. Si NO hay CHECK → se guarda negativo → DEFECTO CRÍTICO (falta restricción a nivel BD).

### 9. Duplicados

**BD-017** Duplicado usuario → user-friendly message
- **ID:** BD-017
- **Pasos:** UI crear usuario con usuario='bd_user01' (ya existente).
- **Resultado esperado:** Laravel validation: "El usuario ya ha sido registrado."
- **Evidencia:** Captura pantalla error.

### 10. Datos inexistentes → 404 (findOrFail)

**BD-018** findOrFail 404 en múltiples rutas
- **ID:** BD-018
- **Rutas:** GET /pedidos/99999, GET /usuarios/99999/edit, PUT /usuarios/99999/estado
- **Resultado esperado:** HTTP 404 NOT FOUND.
- **Evidencia:** Captura 404.

### 11. Relaciones

**BD-019** Pedido → DetallePedido (N) + DetalleTorta (N)
- **ID:** BD-019
- **Precondiciones:** Pedido con 2 productos + 1 torta personalizada.
- **Pasos:** 3 COUNT SQL: pedidos id=X; detalle_pedido pedido_id=X; detalle_torta_personalizada pedido_id=X.
- **Resultado esperado:** COUNT pedidos=1; COUNT detalle_pedido=2; COUNT torta=1.
- **Evidencia:** Los 3 COUNT.

### 12. Integridad referencial (DELETE CASCADE/RESTRICT)

**BD-020** DELETE categoría que tiene productos asociados
- **ID:** BD-020
- **Precondiciones:** Categoría 1 tiene productos.
- **Pasos:** SQL DELETE FROM categorias WHERE id=1;
- **Resultado esperado:** Si FK=RESTRICT/NO ACTION → Error 1451 (no borrar padre con hijos). Si FK=CASCADE → productos se borran también (probable DEFECTO).
- **Observaciones:** [REQ-VER] Actualmente no hay delete de categorías; prueba a nivel BD para detectar diseño.

**BD-021** DELETE pedido → detalle + torta
- **ID:** BD-021
- **Pasos:** SQL DELETE FROM pedidos WHERE id=X (con detalle).
- **Resultado esperado:** Uno: (a) error RESTRICT; o (b) detalle se elimina CASCADE. NUNCA detalle huérfano.
- **Evidencia:** Resultado DELETE + SELECT detalle WHERE pedido_id=X.

### 13. Consistencia de datos (sumatorias)

**BD-022** Venta: total vs Σ detalle.subtotal
- **ID:** BD-022
- **Precondiciones:** Venta ID=V con múltiples productos.
- **Pasos:** SQL SELECT total FROM ventas WHERE id=V; SELECT SUM(subtotal) AS suma_detalle FROM detalle_venta WHERE venta_id=V;
- **Resultado esperado:** Ambos valores idénticos (2 decimales).

**BD-023** Pedido: total = Σ(detalle_subtotal) + Σ(torta_precio); anticipo + pago_final + saldo = total
- **ID:** BD-023
- **Precondiciones:** Pedido mixto con anticipo y pago parcial.
- **Pasos:** SQL SELECT total, anticipo, pago_final, saldo FROM pedidos WHERE id=P. SUM detalle. SUM torta.
- **Fórmulas:** total = SUM(detalle) + SUM(torta). anticipo + pago_final + saldo = total.
- **Resultado esperado:** Igualdad exacta.

**BD-024** Stock: cantidad vs Σ Entradas - Σ Salidas (post último Ajuste)
- **ID:** BD-024
- **Precondiciones:** Producto con histórico: Ajuste 0 → Entrada 15 → Salida 4 → Entrada 2 → Salida 1.
- **Pasos:** SELECT stock.cantidad vs SELECT SUM(cantidad) BY tipo.
- **Resultado esperado:** Stock = (15+2) - (4+1) = 12.

---

## Resumen 24 pruebas BD

| Tipo | IDs | Cantidad |
|------|-----|----------|
| Inserción | BD-001/002/003 | 3 |
| Consulta | BD-004/005 | 2 |
| Actualización | BD-006/007 | 2 |
| Eliminación | BD-008/009 | 2 |
| Persistencia | BD-010 | 1 |
| PK / UNIQUE | BD-011/012 | 2 |
| FK | BD-013/014 | 2 |
| CHECK/DEFAULT/NOT NULL | BD-015/016 | 2 |
| Duplicados UI | BD-017 | 1 |
| findOrFail 404 | BD-018 | 1 |
| Relaciones hasMany | BD-019 | 1 |
| Integridad ref DELETE | BD-020/021 | 2 |
| Consistencia / Sumatorias | BD-022/023/024 | 3 |
