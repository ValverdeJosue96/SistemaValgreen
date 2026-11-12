package com.valgreen.movil

import android.graphics.Color
import android.os.Bundle
import android.view.View
import android.widget.LinearLayout
import android.widget.ProgressBar
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL
import kotlin.concurrent.thread

class DetallePedidoActivity : AppCompatActivity() {

    private lateinit var txtNumero: TextView
    private lateinit var txtCliente: TextView
    private lateinit var contenedorProductos: LinearLayout
    private lateinit var txtTorta: TextView
    private lateinit var txtPago: TextView
    private lateinit var txtAdicional: TextView
    private lateinit var progreso: ProgressBar

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_detalle_pedido)

        txtNumero = findViewById(R.id.txtNumeroDetalle)
        txtCliente = findViewById(R.id.txtDatosCliente)
        contenedorProductos = findViewById(R.id.contenedorDetalleProductos)
        txtTorta = findViewById(R.id.txtDetalleTorta)
        txtPago = findViewById(R.id.txtDatosPago)
        txtAdicional = findViewById(R.id.txtInformacionAdicional)
        progreso = findViewById(R.id.progresoDetalle)

        val pedidoId = intent.getIntExtra("pedido_id", -1)

        if (pedidoId <= 0) {
            mostrarError("No se recibió un número de pedido válido.")
            return
        }

        cargarDetalle(pedidoId)
    }

    private fun cargarDetalle(id: Int) {
        progreso.visibility = View.VISIBLE

        thread {
            var resultado: String? = null
            var error: String? = null

            try {
                val preferencias = getSharedPreferences(
                    "ValgreenSesion", MODE_PRIVATE
                )
                val token = preferencias.getString("token", null)

                if (token.isNullOrBlank()) {
                    throw Exception("La sesión ha caducado. Inicia sesión nuevamente.")
                }

                val conexion = URL(
                    "${ApiConfig.BASE_URL}/pedidos/$id"
                ).openConnection() as HttpURLConnection

                try {
                    conexion.requestMethod = "GET"
                    conexion.connectTimeout = 20000
                    conexion.readTimeout = 30000
                    conexion.setRequestProperty("Authorization", "Bearer $token")
                    conexion.setRequestProperty("Accept", "application/json")

                    val codigo = conexion.responseCode
                    val flujo = if (codigo in 200..299) {
                        conexion.inputStream
                    } else {
                        conexion.errorStream
                    }

                    val cuerpo = flujo.bufferedReader().use { it.readText() }

                    if (codigo !in 200..299) {
                        throw Exception("No se pudo consultar el pedido ($codigo).")
                    }

                    resultado = cuerpo
                } finally {
                    conexion.disconnect()
                }

            } catch (e: Exception) {
                error = e.message ?: "No se pudo cargar el pedido."
            }

            runOnUiThread {
                progreso.visibility = View.GONE

                if (error != null) {
                    mostrarError(error!!)
                    return@runOnUiThread
                }

                try {
                    val respuesta = JSONObject(resultado!!)
                    val pedido = respuesta.getJSONObject("data")
                    mostrarDatos(pedido)
                } catch (e: Exception) {
                    mostrarError("No se pudieron interpretar los datos del pedido.")
                }
            }
        }
    }

    private fun mostrarDatos(pedido: JSONObject) {

        txtNumero.text = "Pedido #${pedido.optInt("id")}"

        val cliente = pedido.optJSONObject("cliente")

        val nombre = listOf(
            cliente?.optString("nombres", "").orEmpty(),
            cliente?.optString("primer_apellido", "").orEmpty(),
            cliente?.optString("segundo_apellido", "").orEmpty()
        ).filter { it.isNotBlank() && it != "null" }
            .joinToString(" ")
            .ifBlank { "Sin nombre" }

        txtCliente.text = """
            Nombre: $nombre
            Carnet: ${cliente?.optString("carnet", "No registrado") ?: "No registrado"}
            Teléfono: ${cliente?.optString("telefono", "No registrado") ?: "No registrado"}
        """.trimIndent()

        contenedorProductos.removeAllViews()

        val detalles = pedido.optJSONArray("detalles")

        if (detalles == null || detalles.length() == 0) {
            agregarLinea(contenedorProductos, "No hay productos registrados.")
        } else {
            for (i in 0 until detalles.length()) {
                val detalle = detalles.getJSONObject(i)
                val producto = detalle.optJSONObject("producto")

                val nombreProducto = producto?.optString(
                    "nombre", "Producto"
                ) ?: "Producto"

                val cantidad = detalle.optInt("cantidad", 0)
                val precio = detalle.optString("precio_unitario", "0")
                val subtotal = detalle.optString("subtotal", "0")

                agregarLinea(
                    contenedorProductos,
                    "$nombreProducto\nCantidad: $cantidad\nPrecio unitario: Bs $precio\nSubtotal: Bs $subtotal"
                )
            }
        }

        val tortas = pedido.optJSONArray("tortas_personalizadas")

        if (tortas == null || tortas.length() == 0) {
            txtTorta.text = "Este pedido no incluye una torta personalizada."
        } else {
            val texto = StringBuilder()

            for (i in 0 until tortas.length()) {
                val torta = tortas.getJSONObject(i)

                texto.append("Torta ${i + 1}\n")
                texto.append("Porciones: ${torta.optString("porciones", "No especificado")}\n")
                texto.append("Sabor: ${torta.optString("sabor", "No especificado")}\n")
                texto.append("Relleno: ${torta.optString("relleno", "No especificado")}\n")
                texto.append("Cobertura: ${torta.optString("cobertura", "No especificado")}\n")
                texto.append("Decoración: ${torta.optString("decoracion", "No especificada")}\n")
                texto.append("Mensaje: ${torta.optString("mensaje", "Sin mensaje")}\n")
                texto.append("Precio: Bs ${torta.optString("precio", "0")}\n")

                val observacionesTorta = torta.optString("observaciones", "")
                if (observacionesTorta.isNotBlank() && observacionesTorta != "null") {
                    texto.append("Observaciones: $observacionesTorta\n")
                }

                if (i < tortas.length() - 1) texto.append("\n")
            }

            txtTorta.text = texto.toString().trim()
        }

        val total = pedido.optString("total", "0")
        val anticipo = pedido.optString("anticipo", "0")
        val pagoFinal = pedido.optString("pago_final", "0")
        val saldo = pedido.optString("saldo", "0")

        txtPago.text = """
            Total: Bs $total
            Anticipo: Bs $anticipo
            Pago final: Bs $pagoFinal
            Saldo pendiente: Bs $saldo
        """.trimIndent()

        val estado = pedido.optJSONObject("estado_pedido")
            ?.optString("nombre", "No especificado") ?: "No especificado"

        val observaciones = pedido.optString("observaciones", "")
            .takeIf { it.isNotBlank() && it != "null" }
            ?: "Sin observaciones"

        txtAdicional.text = """
            Estado: $estado
            Fecha del pedido: ${pedido.optString("fecha_pedido", "No registrada").take(10)}
            Fecha de entrega: ${pedido.optString("fecha_entrega", "No registrada").take(10)}
            Observaciones: $observaciones
        """.trimIndent()
    }

    private fun agregarLinea(contenedor: LinearLayout, texto: String) {
        val vista = TextView(this).apply {
            text = texto
            textSize = 14f
            setTextColor(Color.rgb(52, 61, 52))
            setPadding(0, dp(7), 0, dp(7))
        }

        contenedor.addView(vista)
    }

    private fun mostrarError(mensaje: String) {
        txtNumero.text = "No se pudo cargar el pedido"
        txtAdicional.text = mensaje
        txtAdicional.setTextColor(Color.rgb(170, 65, 55))
    }

    private fun dp(valor: Int): Int =
        (valor * resources.displayMetrics.density).toInt()
}
