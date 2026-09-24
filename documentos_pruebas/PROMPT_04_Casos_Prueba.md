# PROMPT 4 — Especificación profesional de casos de prueba

**Sistema:** VALGREEN — Sistema de Gestión de Repostería
**Versión:** 1.0
**Fecha:** 2026-09-23

Leyenda prioridad: 🔴 CRÍTICO | 🟠 ALTO | 🟡 MEDIO | 🟢 BAJO

---

## Estructura estándar por caso de prueba
- ID del caso
- Requisito relacionado
- Módulo
- Nombre del caso
- Objetivo
- Prioridad
- Precondiciones
- Datos de entrada
- Pasos de ejecución
- Resultado esperado
- Resultado obtenido (VACÍO, se completa tras ejecución)
- Estado (PENDIENTE)
- Evidencia
- Observaciones

---

## MOD-01 — Autenticación y Sesiones

**CP-001**
- Requisito: RF-001
- Módulo: MOD-01
- Nombre: Inicio sesión exitoso Administrador activo
- Objetivo: Verificar usuario Admin con credenciales válidas accede correctamente
- Prioridad: 🔴 CRÍTICO
- Precondiciones: Usuario admin existe con estado=1, rol=Administrador
- Datos: usuario="admin", password="Admin123"
- Pasos: 1. Navegar /login. 2. Llenar credenciales. 3. Clic Iniciar sesión.
- Resultado esperado: Redirección /dashboard. Dashboard admin visible. Sesión creada.
- Resultado obtenido:
- Estado: PENDIENTE
- Evidencia: Captura URL + dashboard renderizado

**CP-002**
- Requisito: RF-002
- Módulo: MOD-01
- Nombre: Bloqueo inicio sesión usuario inactivo
- Objetivo: Validar rechazo usuario estado=0 con credenciales correctas
- Prioridad: 🔴 CRÍTICO
- Precondiciones: Usuario inactivo existe
- Datos: usuario="inactivo", password correcta
- Pasos: 1. /login. 2. Datos. 3. Enviar.
- Resultado esperado: Permanece /login, alerta "Usuario o contraseña incorrectos"
- Resultado obtenido:
- Estado: PENDIENTE
- Evidencia: Captura error

**CP-003**
- Requisito: RF-003
- Módulo: MOD-01
- Nombre: Rechazo contraseña incorrecta
- Prioridad: 🔴 CRÍTICO
- Datos: usuario correcto, password incorrecta
- Pasos: Completar formulario con password KO
- Resultado esperado: Vuelve login con error visible
- Resultado obtenido:
- Estado: PENDIENTE

**CP-004**
- Requisito: RF-003
- Módulo: MOD-01
- Nombre: Rechazo usuario inexistente
- Prioridad: 🟠 ALTO
- Datos: usuario="noexiste_999", password cualquiera
- Resultado esperado: Error mostrado en login
- Resultado obtenido:
- Estado: PENDIENTE

**CP-005**
- Requisito: RF-004
- Módulo: MOD-01
- Nombre: Cierre sesión correcto
- Prioridad: 🔴 CRÍTICO
- Precondiciones: Sesión iniciada como admin
- Pasos: 1. Clic logout. 2. Navegar manual /dashboard
- Resultado esperado: Redirect /login. Dashboard pide login nuevamente
- Resultado obtenido:
- Estado: PENDIENTE

---

## MOD-02 — Usuarios y Permisos

**CP-006**
- Requisito: RF-005, RF-012
- Módulo: MOD-02
- Nombre: Registro exitoso nuevo Vendedor
- Prioridad: 🟠 ALTO
- Datos: nombres="Ana Maria", primer_apellido="Gomez", segundo_apellido="Perez", carnet="1234589", telefono="71012345", rol_id=2, usuario="anagomez", password="Ana1234", confirmation="Ana1234"
- Pasos: 1. /usuarios/create. 2. Llenar. 3. Guardar.
- Resultado esperado: Redirect /usuarios, success flash. Nuevo usuario listado. Password hasheado.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-007**
- Requisito: RF-013
- Módulo: MOD-02
- Nombre: Editar usuario (sin cambiar password)
- Prioridad: 🟠 ALTO
- Precondiciones: Usuario existente
- Datos: nombres="NombreActualizado", password y confirmación vacíos
- Resultado esperado: Datos actualizados. Password NO modifica. Login con contraseña antigua funciona.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-008**
- Requisito: RF-013
- Módulo: MOD-02
- Nombre: Cambiar contraseña usuario
- Prioridad: 🟠 ALTO
- Datos: password="NuevaPass789", confirmation="NuevaPass789"
- Pasos: Editar → nueva contraseña → guardar → login nueva pass
- Resultado esperado: Contraseña actualizada. Login nueva OK; antigua falla.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-009**
- Requisito: RF-013
- Módulo: MOD-02
- Nombre: Desactivar/Activar usuario (estado toggle)
- Prioridad: 🟠 ALTO
- Pasos: 1. Listado → Desactivar. 2. Intentar login usuario. 3. Activar. 4. Login
- Resultado esperado: Paso 2 bloqueado, paso 4 funciona.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-010**
- Requisito: RF-006
- Módulo: MOD-02
- Nombre: Vendedor intenta listar usuarios (403)
- Prioridad: 🔴 CRÍTICO
- Precondiciones: Sesión Vendedor
- Pasos: URL bar → /usuarios
- Resultado esperado: 403 Forbidden "No tienes permiso..."
- Resultado obtenido:
- Estado: PENDIENTE
- Evidencia: Captura pantalla 403

---

## MOD-03 — Productos

**CP-011**
- Requisito: RF-008
- Módulo: MOD-03
- Nombre: Registro producto correcto sin imagen
- Prioridad: 🟠 ALTO
- Precondiciones: Categoría ID=1 existe
- Datos: categoria_id=1, nombre="Torta Red Velvet", descripcion="...", precio=85.50
- Resultado esperado: Redirect /productos success. Producto aparece lista. precio DECIMAL
- Resultado obtenido:
- Estado: PENDIENTE

**CP-012**
- Requisito: RF-009
- Módulo: MOD-03
- Nombre: Listado solo muestra productos activos
- Prioridad: 🟡 MEDIO
- Precondiciones: Producto 1 activo, Producto 2 inactivo (estado=0)
- Pasos: Navegar /productos
- Resultado esperado: Solo activo visible
- Resultado obtenido:
- Estado: PENDIENTE

---

## MOD-04 — Stock

**CP-013**
- Requisito: RF-010
- Módulo: MOD-04
- Nombre: Movimiento Entrada incrementa stock
- Prioridad: 🔴 CRÍTICO
- Precondiciones: Prod stock=5, Tipo Entrada=1
- Datos: cantidad=10
- Resultado esperado: Stock = 5+10 = 15. movimientos_stock registro nuevo.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-014**
- Requisito: RF-010
- Módulo: MOD-04
- Nombre: Movimiento Salida válido decrementa stock
- Prioridad: 🔴 CRÍTICO
- Precondiciones: Stock=15, Salida=2
- Datos: cantidad=7
- Resultado esperado: Stock=15-7=8
- Resultado obtenido:
- Estado: PENDIENTE

**CP-015**
- Requisito: RF-010
- Módulo: MOD-04
- Nombre: Movimiento Ajuste reemplaza cantidad exacta
- Prioridad: 🟡 MEDIO
- Datos: cantidad=50
- Resultado esperado: Stock exactamente = 50
- Resultado obtenido:
- Estado: PENDIENTE

**CP-016**
- Requisito: RF-011
- Módulo: MOD-04
- Nombre: Movimiento Salida excede stock disponible (debe fallar)
- Prioridad: 🔴 CRÍTICO
- Precondiciones: Stock=3
- Datos: cantidad=5
- Resultado esperado: 422 "No hay suficiente stock". Stock permanece 3.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-017**
- Requisito: RF-010
- Módulo: MOD-04
- Nombre: Vendedor consulta stock OK; intenta create stock 403
- Prioridad: 🟠 ALTO
- Precondiciones: Login Vendedor
- Pasos: 1. /stock (OK). 2. /stock/create
- Resultado esperado: 1 → 200. 2 → 403.
- Resultado obtenido:
- Estado: PENDIENTE

---

## MOD-05 — Clientes

**CP-018**
- Requisito: RF-014
- Módulo: MOD-05
- Nombre: Registro correcto de cliente
- Prioridad: 🟠 ALTO
- Datos: nombres="Luis Fernando", primer_apellido="Rodriguez", segundo_apellido="Mamani", carnet="4455667 LP", telefono="69011223"
- Resultado esperado: Redirect /clientes con success. Registro aparece.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-019**
- Requisito: RF-014
- Módulo: MOD-05
- Nombre: Rechazo cliente con carnet duplicado
- Prioridad: 🟠 ALTO
- Precondiciones: Cliente con carnet="4455667 LP" existe
- Resultado esperado: Error validation carnet ya ha sido registrado
- Resultado obtenido:
- Estado: PENDIENTE

---

## MOD-06 — Ventas Directas

**CP-020**
- Requisito: RF-015
- Módulo: MOD-06
- Nombre: Venta 1 producto stock suficiente
- Prioridad: 🔴 CRÍTICO
- Precondiciones: ProdA stock=10, precio=50
- Datos: productos[A_id]=2
- Pasos: 1. /ventas/create. 2. Cantidad 2. 3. Registrar. 4. Revisar /ventas, /stock
- Resultado esperado: Venta total=100. DetalleVenta creado. Stock A = 10-2 = 8.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-021**
- Requisito: RF-015, RN-06
- Módulo: MOD-06
- Nombre: Venta 1 producto OK + 1 excede stock → ROLLBACK total
- Prioridad: 🔴 CRÍTICO
- Precondiciones: A stock=5 precio=10; B stock=3 precio=20
- Datos: A=4, B=5 (B excede)
- Resultado esperado: Error "No hay suficiente stock para: B". NINGUNA venta creada. Stock A y B intactos (5 y 3).
- Resultado obtenido:
- Estado: PENDIENTE

**CP-022**
- Requisito: RF-016
- Módulo: MOD-06
- Nombre: Venta sin productos (todos 0) → error
- Prioridad: 🟠 ALTO
- Datos: todos cantidades=0
- Resultado esperado: Error "Debe seleccionar al menos un producto."
- Resultado obtenido:
- Estado: PENDIENTE

---

## MOD-07 — Pedidos

**CP-023**
- Requisito: RF-017, RF-020
- Módulo: MOD-07
- Nombre: Pedido solo con productos
- Prioridad: 🔴 CRÍTICO
- Precondiciones: Cliente existe. ProdA stock=10, precio=50
- Datos: cliente_id=1, fecha_entrega=hoy+2d, anticipo=30, productos[A_id]=3
- Resultado esperado: total=150, anticipo=30, saldo=120, estado=1 Pendiente. Stock ProdA sigue 10 (NO descuenta).
- Resultado obtenido:
- Estado: PENDIENTE

**CP-024**
- Requisito: RF-017, RF-025
- Módulo: MOD-07
- Nombre: Pedido solo con torta personalizada válida
- Prioridad: 🔴 CRÍTICO
- Datos: cliente_id=1, fecha_entrega válida, anticipo=0, productos vacíos. torta[porciones]=20, torta[sabor]="Chocolate", torta[precio]=120
- Resultado esperado: total=120. detalle_torta_personalizada creada.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-025**
- Requisito: RF-018
- Módulo: MOD-07
- Nombre: Pedido sin productos ni torta válida → error
- Prioridad: 🔴 CRÍTICO
- Datos: productos=0; torta[sabor]="", torta[precio]=0
- Resultado esperado: Error "Debe agregar al menos un producto o una torta personalizada."
- Resultado obtenido:
- Estado: PENDIENTE

**CP-026**
- Requisito: RF-019
- Módulo: MOD-07
- Nombre: Anticipo mayor que total → error
- Prioridad: 🔴 CRÍTICO
- Precondiciones: ProdA=50 stock=10
- Datos: productos[A]=1 → total=50. anticipo=75
- Resultado esperado: Error "Anticipo no puede ser mayor que el total."
- Resultado obtenido:
- Estado: PENDIENTE

**CP-027**
- Requisito: RF-021
- Módulo: MOD-07
- Nombre: Actualizar estado pedido (flujo completo)
- Prioridad: 🟠 ALTO
- Precondiciones: Pedido estado Pendiente id=1
- Datos: estado 2, luego 3, luego 4
- Resultado esperado: Estado cambia en cada paso. updated_by guarda usuario logueado.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-028**
- Requisito: RF-022
- Módulo: MOD-07
- Nombre: Registrar pago final exacto igual a saldo
- Prioridad: 🔴 CRÍTICO
- Precondiciones: Pedido total=150, anticipo=30, saldo=120
- Datos: pago=120
- Resultado esperado: pago_final=120. saldo=0.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-029**
- Requisito: RF-022
- Módulo: MOD-07
- Nombre: Registrar pago > saldo → error
- Prioridad: 🔴 CRÍTICO
- Datos: saldo=120, pago=150
- Resultado esperado: Error "Pago no puede ser mayor al saldo pendiente." Saldo permanece 120.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-030**
- Requisito: RF-020
- Módulo: MOD-07
- Nombre: Pedido no descuenta stock
- Prioridad: 🟠 ALTO
- Precondiciones: Stock inicial del producto conocido
- Pasos: Crear pedido con ese producto. Consultar stock luego.
- Resultado esperado: Stock idéntico al inicial.
- Resultado obtenido:
- Estado: PENDIENTE

---

## MOD-08 — Dashboard

**CP-031**
- Requisito: RF-023
- Módulo: MOD-08
- Nombre: Dashboard Admin muestra valores correctos
- Prioridad: 🟡 MEDIO
- Precondiciones: 1 venta hoy 100Bs; 2 pedidos activos; 3 usuarios activos; 1 producto stock ≤5; 1 pedido entrega hoy.
- Pasos: Login Admin → /dashboard. Comparar tarjetas con SQL manual.
- Resultado esperado: ventasHoy=100, pedidosActivos=2, usuariosActivos=3, stockBajo=1
- Resultado obtenido:
- Estado: PENDIENTE

**CP-032**
- Requisito: RF-024
- Módulo: MOD-08
- Nombre: Dashboard Vendedor muestra sus métricas; NO muestra Admin
- Prioridad: 🟡 MEDIO
- Pasos: Login Vendedor → /dashboard
- Resultado esperado: Solo ventasHoy, pedidosPendientes, pedidosHoy. NO "Usuarios activos" ni "Stock bajo".
- Resultado obtenido:
- Estado: PENDIENTE

---

## Complementarios Negativos, Límites, Permisos, Navegación, Persistencia

**CP-033**
- Requisito: RF-005, RF-006
- Nombre: Vendedor intenta /productos/create URL directa → 403
- Prioridad: 🔴 CRÍTICO
- Resultado esperado: 403.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-034**
- Requisito: RF-006
- Nombre: Vendedor intenta POST /usuarios (request directo)
- Prioridad: 🔴 CRÍTICO
- Resultado esperado: 403. Ningún usuario creado.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-035**
- Requisito: RF-014
- Nombre: Cliente campo nombres vacío → error obligatorio
- Prioridad: 🟠 ALTO
- Datos: nombres="" resto OK
- Resultado esperado: Error obligatorio
- Resultado obtenido:
- Estado: PENDIENTE

**CP-036**
- Requisito: RNF-001
- Nombre: Contraseña almacenada hasheada (no texto plano)
- Prioridad: 🔴 CRÍTICO
- Pasos: SELECT password FROM usuarios WHERE usuario='anagomez'
- Resultado esperado: Empieza $2y$ o $argon. NO legible.
- Resultado obtenido:
- Estado: PENDIENTE

**CP-037**
- Requisito: RNF-004
- Nombre: Subida imagen producto PNG válida <2MB
- Prioridad: 🟡 MEDIO
- Datos: archivo PNG 200KB
- Resultado esperado: Archivo guardado storage/app/public/productos
- Resultado obtenido:
- Estado: PENDIENTE

**CP-038**
- Requisito: RNF-006
- Nombre: Rechazo contraseña de 5 caracteres (mínimo 6)
- Prioridad: 🟠 ALTO
- Datos: password="12345", confirmation="12345"
- Resultado esperado: Error password mínimo 6 caracteres
- Resultado obtenido:
- Estado: PENDIENTE

---

**TOTAL ESPECIFICADOS: 38 casos**
