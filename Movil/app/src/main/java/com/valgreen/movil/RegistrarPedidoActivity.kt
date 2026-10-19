
package com.valgreen.movil

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import org.json.JSONArray
import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale
import kotlin.concurrent.thread

class RegistrarPedidoActivity : AppCompatActivity() {

    private lateinit var contenedorProductos: LinearLayout
    private lateinit var txtTotalProductos: TextView
    private lateinit var txtTotal: TextView
    private lateinit var txtSaldo: TextView
    private lateinit var txtMensaje: TextView

    private lateinit var edtCarnet: EditText
    private lateinit var edtNombres: EditText
    private lateinit var edtPrimerApellido: EditText
    private lateinit var edtSegundoApellido: EditText
    private lateinit var edtTelefono: EditText
    private lateinit var edtFechaEntrega: EditText
    private lateinit var edtSabor: EditText
    private lateinit var edtPorciones: EditText
    private lateinit var edtRelleno: EditText
    private lateinit var edtCobertura: EditText
    private lateinit var edtDecoracion: EditText
    private lateinit var edtMensajeTorta: EditText
    private lateinit var edtPrecioTorta: EditText
    private lateinit var edtAnticipo: EditText
    private lateinit var edtObservaciones: EditText
    private lateinit var btnGuardar: Button

    private data class ProductoMovil(
        val id: Int,
        val nombre: String,
        val precio: Double,
        var cantidad: Int = 0
    )

    private val productos = mutableListOf<ProductoMovil>()
    private var enviando = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_registrar_pedido)

        contenedorProductos = findViewById(R.id.contenedorProductos)
        txtTotalProductos = findViewById(R.id.txtTotalProductos)
        txtTotal = findViewById(R.id.txtTotal)
        txtSaldo = findViewById(R.id.txtSaldo)
        txtMensaje = findViewById(R.id.txtMensaje)

        edtCarnet = findViewById(R.id.edtCarnet)
        edtNombres = findViewById(R.id.edtNombres)
        edtPrimerApellido = findViewById(R.id.edtPrimerApellido)
        edtSegundoApellido = findViewById(R.id.edtSegundoApellido)
        edtTelefono = findViewById(R.id.edtTelefono)
        edtFechaEntrega = findViewById(R.id.edtFechaEntrega)
        edtSabor = findViewById(R.id.edtSabor)
        edtPorciones = findViewById(R.id.edtPorciones)
        edtRelleno = findViewById(R.id.edtRelleno)
        edtCobertura = findViewById(R.id.edtCobertura)
        edtDecoracion = findViewById(R.id.edtDecoracion)
        edtMensajeTorta = findViewById(R.id.edtMensajeTorta)
        edtPrecioTorta = findViewById(R.id.edtPrecioTorta)
        edtAnticipo = findViewById(R.id.edtAnticipo)
        edtObservaciones = findViewById(R.id.edtObservaciones)
        btnGuardar = findViewById(R.id.btnGuardarPedido)

        edtFechaEntrega.setOnClickListener {
            val hoy = Calendar.getInstance()
            DatePickerDialog(
                this,
                { _, year, month, day ->
                    val fecha = Calendar.getInstance()
                    fecha.set(year, month, day)
                    edtFechaEntrega.setText(
                        SimpleDateFormat(
                            "yyyy-MM-dd",
                            Locale.US
                        ).format(fecha.time)
                    )
                },
                hoy.get(Calendar.YEAR),
                hoy.get(Calendar.MONTH),
                hoy.get(Calendar.DAY_OF_MONTH)
            ).show()
        }

        edtAnticipo.setOnFocusChangeListener { _, _ -> actualizarTotales() }
        edtPrecioTorta.setOnFocusChangeListener { _, _ -> actualizarTotales() }

        btnGuardar.setOnClickListener {
            guardarPedido()
        }

        cargarProductos()
    }

    private fun cargarProductos() {
        txtMensaje.text = "Cargando productos..."

        thread {
            try {
                val conexion = abrirConexion("/productos", "GET")
                val codigo = conexion.responseCode
                val cuerpo = leerRespuesta(conexion, codigo)
                conexion.disconnect()

                if (codigo !in 200..299) {
                    throw Exception("No se pudieron cargar los productos ($codigo).")
                }

                val json = JSONObject(cuerpo)
                val lista = json.getJSONArray("data")
                val cargados = mutableListOf<ProductoMovil>()

                for (i in 0 until lista.length()) {
                    val p = lista.getJSONObject(i)
                    cargados.add(
                        ProductoMovil(
                            id = p.getInt("id"),
                            nombre = p.optString("nombre", "Producto"),
                            precio = p.optDouble("precio", 0.0)
                        )
                    )
                }

                runOnUiThread {
                    productos.clear()
                    productos.addAll(cargados)
                    mostrarProductos()
                    txtMensaje.text = if (productos.isEmpty()) {
                        "No hay productos disponibles."
                    } else {
                        ""
                    }
                }
            } catch (e: Exception) {
                runOnUiThread {
                    txtMensaje.text =
                        e.message ?: "Error al cargar productos."
                }
            }
        }
    }

    private fun mostrarProductos() {
        contenedorProductos.removeAllViews()

        for (producto in productos) {
            val fila = LinearLayout(this).apply {
                orientation = LinearLayout.HORIZONTAL
                gravity = android.view.Gravity.CENTER_VERTICAL
                setPadding(8, 12, 8, 12)
            }

            val info = TextView(this).apply {
                text = "${producto.nombre}\nBs ${"%.2f".format(Locale.US, producto.precio)}"
                textSize = 15f
                setTextColor(android.graphics.Color.rgb(52, 61, 52))
            }

            fila.addView(
                info,
                LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f)
            )

            val menos = Button(this).apply {
                text = "−"
                isAllCaps = false
                setOnClickListener {
                    if (producto.cantidad > 0) producto.cantidad--
                    mostrarProductos()
                    actualizarTotales()
                }
            }

            val cantidad = TextView(this).apply {
                text = producto.cantidad.toString()
                textSize = 17f
                gravity = android.view.Gravity.CENTER
            }

            val mas = Button(this).apply {
                text = "+"
                isAllCaps = false
                setOnClickListener {
                    producto.cantidad++
                    mostrarProductos()
                    actualizarTotales()
                }
            }

            fila.addView(menos)
            fila.addView(cantidad)
            fila.addView(mas)

            contenedorProductos.addView(fila)
        }
    }

    private fun totalProductos(): Double {
        return productos.sumOf { it.precio * it.cantidad }
    }

    private fun totalPedido(): Double {
        val precioTorta = edtPrecioTorta.text.toString().toDoubleOrNull() ?: 0.0
        return totalProductos() + precioTorta
    }

    private fun actualizarTotales() {
        val subtotal = totalProductos()
        val total = totalPedido()
        val anticipo = edtAnticipo.text.toString().toDoubleOrNull() ?: 0.0

        txtTotalProductos.text =
            "Subtotal productos: Bs ${"%.2f".format(Locale.US, subtotal)}"
        txtTotal.text =
            "Total: Bs ${"%.2f".format(Locale.US, total)}"
        txtSaldo.text =
            "Saldo pendiente: Bs ${"%.2f".format(Locale.US, total - anticipo)}"
    }

    private fun abrirConexion(
        ruta: String,
        metodo: String,
        enviarToken: Boolean = true
    ): HttpURLConnection {
        val conexion = URL("${ApiConfig.BASE_URL}$ruta")
            .openConnection() as HttpURLConnection

        conexion.requestMethod = metodo
        conexion.connectTimeout = 15000
        conexion.readTimeout = 15000
        conexion.setRequestProperty("Accept", "application/json")

        if (enviarToken) {
            val token = getSharedPreferences(
                "ValgreenSesion",
                MODE_PRIVATE
            ).getString("token", null)

            if (token.isNullOrBlank()) {
                throw Exception("La sesión no es válida. Inicia sesión nuevamente.")
            }

            conexion.setRequestProperty("Authorization", "Bearer $token")
        }

        return conexion
    }

    private fun leerRespuesta(
        conexion: HttpURLConnection,
        codigo: Int
    ): String {
        val flujo = if (codigo in 200..299) {
            conexion.inputStream
        } else {
            conexion.errorStream
        }

        return flujo?.bufferedReader()?.use { it.readText() } ?: ""
    }

    private fun guardarPedido() {
        if (enviando) return

        val carnet = edtCarnet.text.toString().trim()
        val nombres = edtNombres.text.toString().trim()
        val primerApellido = edtPrimerApellido.text.toString().trim()
        val segundoApellido = edtSegundoApellido.text.toString().trim()
        val telefono = edtTelefono.text.toString().trim()
        val fecha = edtFechaEntrega.text.toString().trim()
        val anticipo = edtAnticipo.text.toString().toDoubleOrNull()
        val precioTorta = edtPrecioTorta.text.toString().toDoubleOrNull() ?: 0.0
        val sabor = edtSabor.text.toString().trim()

        if (carnet.isBlank() || fecha.isBlank() || anticipo == null) {
            txtMensaje.text = "Completa el carnet, la fecha y el anticipo."
            return
        }

        if (totalPedido() <= 0 || anticipo < 0 || anticipo > totalPedido()) {
            txtMensaje.text = "Verifica el total y el anticipo."
            return
        }

        if (productos.none { it.cantidad > 0 } &&
            !(sabor.isNotBlank() && precioTorta > 0)
        ) {
            txtMensaje.text = "Agrega productos o una torta personalizada con precio."
            return
        }

        val torta = JSONObject()

        if (sabor.isNotBlank() && precioTorta > 0) {
            torta.put("sabor", sabor)
            torta.put("porciones", edtPorciones.text.toString().toIntOrNull() ?: 1)
            torta.put("relleno", edtRelleno.text.toString().trim())
            torta.put("cobertura", edtCobertura.text.toString().trim())
            torta.put("decoracion", edtDecoracion.text.toString().trim())
            torta.put("mensaje", edtMensajeTorta.text.toString().trim())
            torta.put("precio", precioTorta)
        }

        val cliente = JSONObject().apply {
            put("carnet", carnet)
            put("nombres", nombres)
            put("primer_apellido", primerApellido)
            put("segundo_apellido", segundoApellido)
            put("telefono", telefono)
        }

        val listaProductos = JSONObject()
        productos.filter { it.cantidad > 0 }.forEach {
            listaProductos.put(it.id.toString(), it.cantidad)
        }

        val cuerpo = JSONObject().apply {
            put("cliente", cliente)
            put("fecha_entrega", fecha)
            put("anticipo", anticipo)
            put("observaciones", edtObservaciones.text.toString().trim())
            put("productos", listaProductos)
            if (torta.length() > 0) put("torta", torta)
        }

        enviando = true
        btnGuardar.isEnabled = false
        txtMensaje.text = "Guardando pedido..."

        thread {
            try {
                val conexion = abrirConexion("/pedidos", "POST")
                conexion.doOutput = true
                conexion.setRequestProperty(
                    "Content-Type",
                    "application/json; charset=UTF-8"
                )

                conexion.outputStream.use {
                    it.write(cuerpo.toString().toByteArray(Charsets.UTF_8))
                }

                val codigo = conexion.responseCode
                val respuesta = leerRespuesta(conexion, codigo)
                conexion.disconnect()

                val json = JSONObject(respuesta)

                runOnUiThread {
                    if (codigo in 200..299 && json.optBoolean("success")) {
                        val id = json.optJSONObject("data")?.optInt("id") ?: 0
                        Toast.makeText(
                            this,
                            "Pedido #$id registrado correctamente.",
                            Toast.LENGTH_LONG
                        ).show()
                        finish()
                    } else {
                        val errores = json.optJSONObject("errors")
                        val mensaje = errores?.keys()?.asSequence()
                            ?.mapNotNull { clave ->
                                errores.optJSONArray(clave)?.optString(0)
                            }?.firstOrNull()
                            ?: json.optString(
                                "message",
                                "No se pudo registrar el pedido."
                            )

                        txtMensaje.text = mensaje
                    }
                }
            } catch (e: Exception) {
                runOnUiThread {
                    txtMensaje.text =
                        e.message ?: "No se pudo conectar con el servidor."
                }
            } finally {
                runOnUiThread {
                    enviando = false
                    btnGuardar.isEnabled = true
                }
            }
        }
    }
}
