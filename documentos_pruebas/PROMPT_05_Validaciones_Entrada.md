# PROMPT 5 — Pruebas de validación de entrada de datos

**Proyecto:** VALGREEN — Sistema de Gestión de Repostería
**Tecnologías:** Laravel 11 + MySQL 8

Las reglas se obtienen directamente de los $request->validate() de cada controlador. Si no hay regla explícita, marca [requiere verificación].

---

## FORMULARIO 1: Inicio de sesión (/login)
Controlador: AuthController.php login

| Campo | Obligatorio | Tipo | Long mín | Long máx | Formato | Permitidos | No permitidos | Duplicidad | Relaciones |
|-------|-------------|------|-----------|-----------|---------|------------|---------------|------------|------------|
| usuario | ✅ Sí | Texto | - [req ver] | - [req ver] | Cualq texto | Texto cualquiera | - | N/A | FK a usuarios.usuario |
| password | ✅ Sí | Texto | - [req ver] | - [req ver] | Cualq texto | Texto cualquiera | - | N/A | Hash::check vs usuarios.password |

### Casos F1:
| ID | Escenario | Entrada | Esperado |
|----|-----------|---------|----------|
| F1-01 ✅ | Credenciales correctas | admin / Admin123 | Redirect dashboard |
| F1-02 ❌ | Usuario vacío | "" + pass correcta | Error obligatorio |
| F1-03 ❌ | Password vacío | user OK + "" | Error obligatorio |
| F1-04 ❌ | Ambos vacíos | "" / "" | 2 errores |
| F1-05 ❌ | Solo espacios | "   " / "   " | Error validación |
| F1-06 ❌ | Caracteres especiales SQLi | "';--" / pass | Rechazo |
| F1-07 ❌ | Password 5000 chars | user OK + "a"*5000 | Rechazo |
| F1-08 ❌ | Usuario inexistente | noexiste / x | Error credenciales |
| F1-09 ❌ | Pass incorrecta | admin / MalPass | Error |
| F1-10 ❌ | Usuario inactivo pass correcta | inactivo / correcta | Error credenciales (RN-02) |

---

## FORMULARIO 2: Registro de Usuario (/usuarios/create)
Controlador: UsuarioController store

| Campo | Obligatorio | Tipo | Mín | Máx | Formato | Duplicidad |
|-------|-------------|------|-----|-----|---------|------------|
| rol_id | ✅ | Entero FK | 1 | - | Integer | N/A |
| nombres | ✅ | String | - | 100 | Texto | No |
| primer_apellido | ✅ | String | - | 50 | Texto | No |
| segundo_apellido | ✅ | String | - | 50 | Texto | No |
| carnet | ✅ | String | - | 20 | Texto | **Unique usuarios.carnet** |
| telefono | ❌ No | String | - | 20 | Texto | No |
| usuario | ✅ | String | - | 50 | Texto | **Unique usuarios.usuario** |
| password | ✅ | String | 6 | - | Texto | confirmed |
| password_confirmation | ✅ | String | 6 | - | Texto | N/A |

### Casos F2:
| ID | Escenario | Entrada | Esperado |
|----|-----------|---------|----------|
| F2-01 ✅ | Todos campos válidos | Datos nominales | OK |
| F2-02 ✅ | nombres 100 chars (límite) | "a"*100 | Aceptado |
| F2-03 ❌ | nombres 101 chars | "a"*101 | Error max 100 |
| F2-04 ❌ | nombres vacío | "" | Error obligatorio |
| F2-05 ❌ | Múltiples obligatorios vacíos | varios "" | Múltiples errores |
| F2-06 ❌ | nombres solo espacios | "    " | Error |
| F2-07 ✅ | telefono vacío (opcional) | "" | OK |
| F2-08 ❌ | password 5 chars (falta 1) | "12345" | Error min 6 |
| F2-09 ✅ | password límite 6 chars | "123456" | Aceptado |
| F2-10 ❌ | Password confirmation distinta | Abc123 / Abc124 | Error confirmación |
| F2-11 ❌ | carnet duplicado | Mismo CP-018 | Error unique |
| F2-12 ❌ | usuario duplicado | Mismo usuario | Error unique |
| F2-13 ❌ | rol_id 999 (inexistente) | 999 | Error exists |
| F2-14 ❌ | rol vacío | "" | Error obligatorio |
| F2-15 ❌ | XSS en nombres | "<script>alert(1)</script>" | No ejecuta XSS al listar |
| F2-16 ❌ | carnet 21 chars | "1"*21 | Error max 20 |
| F2-17 ❌ | telefono 21 chars | "1"*21 | Error max 20 |

---

## FORMULARIO 3: Registro de Cliente (/clientes/create)
Controlador: ClienteController store

| Campo | Obligatorio | Tipo | Mín | Máx | Duplicidad |
|-------|-------------|------|-----|-----|------------|
| nombres | ✅ | String | - | 100 | No |
| primer_apellido | ✅ | String | - | 50 | No |
| segundo_apellido | ✅ | String | - | 50 | No |
| carnet | ✅ | String | - | 20 | **Unique clientes.carnet** |
| telefono | ✅ | String | - | 20 | No [req ver] |

### Casos F3:
| ID | Escenario | Entrada | Esperado |
|----|-----------|---------|----------|
| F3-01 ✅ | Datos válidos | Como CP-018 | OK |
| F3-02 ❌ | nombres vacío | "" | Error |
| F3-03 ❌ | primer_apellido vacío | "" | Error |
| F3-04 ❌ | segundo_apellido vacío | "" | Error |
| F3-05 ❌ | carnet vacío | "" | Error |
| F3-06 ❌ | telefono vacío | "" | Error |
| F3-07 ❌ | carnet duplicado | CP-018 mismo | Error unique |
| F3-08 ❌ | nombres 101 | "a"*101 | Error |
| F3-09 ❌ | primer_apellido 51 | "a"*51 | Error |
| F3-10 ❌ | segundo_apellido 51 | "a"*51 | Error |
| F3-11 ❌ | carnet 21 | "a"*21 | Error |
| F3-12 ❌ | telefono 21 | "1"*21 | Error |
| F3-13 ❌ | nombres solo espacios | "     " | Error [req ver TrimStrings] |
| F3-14 ✅ | Carnet formato boliviano | "1234567 LP" | OK |
| F3-15 ❌ | XSS en nombres | "<img src=x onerror=alert(1)>" | No ejecuta JS al listar |

---

## FORMULARIO 4: Registro de Producto (/productos/create)
Controlador: ProductoController store

| Campo | Obligatorio | Tipo | Mín | Máx | Formato |
|-------|-------------|------|-----|-----|---------|
| categoria_id | ✅ | Entero FK | 1 | - | exists:categorias,id |
| nombre | ✅ | String | - | 100 | Texto |
| descripcion | ❌ No | String | - | - [req ver] | Texto |
| precio | ✅ | Numérico | 0 | - [req ver] | ≥0 decimal |
| imagen | ❌ No | Archivo | - | 2048 KB | image/* jpeg/png/jpg/webp |

### Casos F4:
| ID | Escenario | Entrada | Esperado |
|----|-----------|---------|----------|
| F4-01 ✅ | Completo sin imagen | categoría, nombre, precio 50 | OK |
| F4-02 ✅ | PNG válido 100KB | + archivo | Archivo guardado |
| F4-03 ❌ | categoría vacía | "" | Error |
| F4-04 ❌ | categoría 999 no existe | 999 | Error exists |
| F4-05 ❌ | nombre vacío | "" | Error |
| F4-06 ❌ | nombre 101 chars | "a"*101 | Error |
| F4-07 ✅ | precio = 0 (límite) | 0.00 | Aceptado |
| F4-08 ❌ | precio negativo | -1.00 | Error min 0 |
| F4-09 ❌ | precio texto | "hola" | Error numérico |
| F4-10 ✅ | descripción vacía | "" | OK |
| F4-11 ✅ | descripción 500 chars | "a"*500 | Aceptado [req ver límite] |
| F4-12 ❌ | imagen PDF | file.pdf | Error debe ser imagen |
| F4-13 ❌ | imagen EXE | file.exe | Error |
| F4-14 ❌ | PNG 3MB | archivo grande | Error máx 2MB |
| F4-15 ✅ | JPG 2047KB límite | jpg ~2MB | Guardado OK |
| F4-16 ❌ | precio vacío | "" | Error obligatorio |

---

## FORMULARIO 5: Movimiento de Stock (/stock/create)
Controlador: StockController store

| Campo | Obligatorio | Tipo | Mín | Máx |
|-------|-------------|------|-----|-----|
| producto_id | ✅ | FK | 1 | - |
| tipo_movimiento_id | ✅ | FK | 1 | - |
| cantidad | ✅ | Entero | 1 | - [req ver] |
| motivo | ❌ No | String | - | 255 |

### Casos F5:
| ID | Escenario | Entrada | Esperado |
|----|-----------|---------|----------|
| F5-01 ✅ | Entrada válida | prod, Entrada, 10 | OK, stock suma |
| F5-02 ✅ | Salida válida stock ≥ cant | stock 15, Salida 5 | OK, stock resta |
| F5-03 ❌ | Salida > stock | stock=3, cant=5 | 422 No hay stock suficiente |
| F5-04 ✅ | Ajuste | Ajuste, 100 | stock=100 exacto |
| F5-05 ❌ | producto vacío | "" | Error |
| F5-06 ❌ | tipo vacío | "" | Error |
| F5-07 ❌ | cantidad 0 | 0 | Error min 1 |
| F5-08 ❌ | cantidad negativa | -5 | Error min 1 |
| F5-09 ❌ | cantidad decimal | 5.5 | Error integer |
| F5-10 ❌ | cantidad string | "cinco" | Error → (int)"cinco"=0 → min 1 |
| F5-11 ✅ | motivo vacío | "" | OK |
| F5-12 ❌ | motivo 256 chars | "a"*256 | Error max 255 |
| F5-13 ❌ | producto 9999 inexistente | 9999 | Error exists |
| F5-14 ❌ | tipo 888 inexistente | 888 | Error exists |

---

## FORMULARIO 6: Registrar Venta (/ventas/create)
Controlador: VentaController store

| Campo | Obligatorio | Tipo | Formato |
|-------|-------------|------|---------|
| productos (array) | ✅ Sí | Array [producto_id => cantidad] | Al menos 1 producto con int > 0 |

### Casos F6:
| ID | Escenario | Entrada | Esperado |
|----|-----------|---------|----------|
| F6-01 ✅ | 1 producto cant ≤ stock | Prod stock=10, cant=2 | OK, stock=8 |
| F6-02 ✅ | Múltiples productos OK | ProdA=1, ProdB=3 | OK total = suma subtotales |
| F6-03 ❌ | Todas cantidades 0 | prod[1]=0, prod[2]=0 | Error "Debe seleccionar al menos un producto" |
| F6-04 ❌ | Array productos vacío | productos=[] | Error obligatorio |
| F6-05 ❌ | Un producto excede stock → ROLLBACK | ProdB stock=3, cant=10 + ProdA OK | Error por B. Nada guardado, stock intacto |
| F6-06 ❌ | producto_id inexistente | productos[9999]=2 | 404 o error |
| F6-07 ❌ | Cantidad negativa | prod[1]=-3 | Se filtra (>0 false). Si todas: error al menos 1 |
| F6-08 ❌ | Cantidad string | prod[1]="dos" | (int)"dos"=0 → error al menos 1 |
| F6-09 ✅ | Límite cant exacta = stock | Prod stock=5, cant=5 | OK venta, stock=0 |
| F6-10 ✅ | Producto sin registro stock | Prod sin stock, cant=1 | stockActual = 0 → error |

---

## FORMULARIO 7: Registrar Pedido (/pedidos/create)
Controlador: PedidoController store

| Campo | Obligatorio | Tipo | Mín | Máx | Regla |
|-------|-------------|------|-----|-----|-------|
| cliente_id | ✅ | FK | - | - | exists:clientes |
| fecha_entrega | ✅ | Fecha | - | - | date válida |
| anticipo | ✅ | Numérico | 0 | - | ≥0 y ≤ total (RN-02) |
| productos[] | ❌ No | Array | - | - | cant>0 válidos |
| torta.porciones | ❌ No | Entero | 1 | - | min:1 |
| torta.sabor | ❌ No* | String | - | 100 | *Requerido si torta válida |
| torta.relleno | ❌ No | String | - | 100 | - |
| torta.cobertura | ❌ No | String | - | 100 | - |
| torta.decoracion | ❌ No | String | - | 255 | - |
| torta.mensaje | ❌ No | String | - | 255 | - |
| torta.observaciones | ❌ No | String | - | - | - |
| torta.precio | ❌ No* | Numérico | 0 | - | *>0 si torta |
| observaciones | ❌ No | String | - | - | - |

### Casos F7:
| ID | Escenario | Entrada | Esperado |
|----|-----------|---------|----------|
| F7-01 ✅ | Solo productos OK | cliente, fecha, anticipo=50, prod[A]=2 | OK, stock NO descuenta |
| F7-02 ✅ | Solo torta OK | sabor Chocolate, precio 80 | OK total 80 |
| F7-03 ✅ | Productos + Torta OK | Ambos | total = suma + torta |
| F7-04 ❌ | Sin productos ni torta (total=0) | Ambos vacíos | Error total=0 |
| F7-05 ❌ | Productos todos 0 + torta sin precio | prod=0, torta precio=0 | Error total=0 |
| F7-06 ❌ | Torta sabor sí, precio 0 | sabor Vainilla, precio=0 | $tieneTorta=false → total=0 |
| F7-07 ❌ | Torta precio sí, sabor vacío | precio=50, sabor="" | $tieneTorta=false → total=0 |
| F7-08 ❌ | cliente_id vacío | "" | Error obligatorio |
| F7-09 ❌ | cliente_id 9999 | 9999 | Error exists |
| F7-10 ❌ | fecha_entrega vacía | "" | Error obligatorio |
| F7-11 ❌ | fecha texto inválido | "no-es-fecha" | Error no es fecha |
| F7-12 ✅ | anticipo 0 | 0 | OK |
| F7-13 ❌ | anticipo negativo | -10 | Error min 0 |
| F7-14 ❌ | anticipo > total | total=100, anticipo=150 | Error anticipo > total |
| F7-15 ✅ | Anticipo = total exacto | total=100, anticipo=100 | OK |
| F7-16 ❌ | torta porciones 0 | porciones=0 | Error min 1 |
| F7-17 ❌ | torta sabor 101 chars | "a"*101 | Error max 100 |
| F7-18 ❌ | torta precio -1 | -1 | Error min 0 |
| F7-19 ❌ | Producto 999 | productos[999]=1 | findOrFail 404 |
| F7-20 ❌ | Productos negativos | productos[A]=-3 | (int)-3 >0 = false; total=0 → error |

---

## FORMULARIO 8: Registrar Pago Final Pedido (/pedidos/{id}/pago)
Controlador: PedidoController registrarPago

| Campo | Obligatorio | Tipo | Mín | Máx | Regla |
|-------|-------------|------|-----|-----|-------|
| pago | ✅ | Numérico | 0.01 | ≤ saldo | required + numeric + min 0.01 + RN-03 ≤ saldo |

### Casos F8:
| ID | Escenario | Entrada | Esperado |
|----|-----------|---------|----------|
| F8-01 ✅ | Pago exacto igual a saldo | saldo=120, pago=120 | OK saldo=0 |
| F8-02 ✅ | Pago parcial | saldo=120, pago=50 | OK saldo=70 |
| F8-03 ❌ | Pago 0 | 0 | Error min 0.01 |
| F8-04 ❌ | Pago negativo | -10 | Error min |
| F8-05 ❌ | Pago > saldo | saldo=70, pago=80 | Error pago > saldo |
| F8-06 ❌ | Pago string texto | "veinte" | Error numérico |
| F8-07 ❌ | Pago vacío | "" | Error obligatorio |
| F8-08 ✅ | Segundo pago hasta completar | saldo=70 → pago 50 (saldo=20) → pago 20 (saldo=0) | OK, pago_final acumula |

---

### 📊 TOTAL GENERAL: 120 escenarios de validación

> **Campos que requieren verificación adicional (sin regla explícita):**
> 1. Manejo de `TrimStrings` middleware (espacios se eliminan?)
> 2. Longitud máxima `descripcion` producto
> 3. Longitud máxima `observaciones` pedido y torta
> 4. Formato teléfono (solo números, guiones?)
> 5. Formato carnet (regex?)
> 6. Fecha entrega mayor a hoy (¿se permite fecha pasada?)
> 7. Cantidad máxima permitida en ventas/pedidos
> 8. Tamaño máximo archivos no imágenes
