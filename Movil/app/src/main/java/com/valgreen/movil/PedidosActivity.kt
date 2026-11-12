package com.valgreen.movil

import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.GradientDrawable
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.widget.LinearLayout
import android.widget.ProgressBar
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL
import kotlin.concurrent.thread

class PedidosActivity : AppCompatActivity() {

    private lateinit var contenedor: LinearLayout
    private lateinit var contenedorFiltros: LinearLayout
    private lateinit var txtCantidad: TextView
    private lateinit var progreso: ProgressBar

    private val listaPedidos = mutableListOf<JSONObject>()
    private var filtroSeleccionado = "Todos"

    private val estados = listOf(
        "Todos", "Pendiente", "En preparación",
        "Listo", "Entregado", "Cancelado"
    )

    private val verde = Color.rgb(82, 111, 85)
    private val blanco = Color.WHITE
    private val borde = Color.rgb(230, 227, 216)

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_pedidos)

        contenedor = findViewById(R.id.contenedorPedidos)
        contenedorFiltros = findViewById(R.id.contenedorFiltros)
        txtCantidad = findViewById(R.id.txtCantidadPedidos)
        progreso = findViewById(R.id.progresoPedidos)

        crearFiltros()
        cargarPedidos()
    }

    private fun crearFiltros() {
        contenedorFiltros.removeAllViews()

        estados.forEach { estado ->

            val seleccionado = estado == filtroSeleccionado

            val boton = TextView(this).apply {
                text = estado
                textSize = 13f
                setPadding(dp(15), dp(10), dp(15), dp(10))
                setTextColor(if (seleccionado) blanco else verde)
                background = GradientDrawable().apply {
                    setColor(if (seleccionado) verde else blanco)
                    cornerRadius = dp(22).toFloat()
                    if (!seleccionado) setStroke(dp(1), borde)
                }
                isClickable = true
                isFocusable = true

                setOnClickListener {
                    filtroSeleccionado = estado
                    crearFiltros()
                    mostrarPedidos()
                }
            }

            val parametros = LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.WRAP_CONTENT,
                LinearLayout.LayoutParams.WRAP_CONTENT
            )
            parametros.marginEnd = dp(8)

            contenedorFiltros.addView(boton, parametros)
        }
    }

    private fun cargarPedidos() {
        contenedor.removeAllViews()
        progreso.visibility = View.VISIBLE
        txtCantidad.text = "Cargando pedidos..."

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
                    "${ApiConfig.BASE_URL}/pedidos"
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
                        throw Exception("Error del servidor ($codigo).")
                    }

                    resultado = cuerpo
                } finally {
                    conexion.disconnect()
                }

            } catch (e: Exception) {
                error = e.message ?: "No se pudieron cargar los pedidos."
            }

            runOnUiThread {
                progreso.visibility = View.GONE

                if (error != null) {
                    mostrarMensaje(error!!)
                    return@runOnUiThread
                }

                try {
                    val json = JSONObject(resultado!!)
                    val pedidos = json.getJSONArray("data")

                    listaPedidos.clear()

                    for (i in 0 until pedidos.length()) {
                        listaPedidos.add(pedidos.getJSONObject(i))
                    }

                    mostrarPedidos()

                } catch (e: Exception) {
                    mostrarMensaje("No se pudo interpretar la respuesta de la API.")
                }
            }
        }
    }

    private fun mostrarPedidos() {
        contenedor.removeAllViews()

        val filtrados = listaPedidos.filter { pedido ->
            val estado = pedido.optJSONObject("estado_pedido")
                ?.optString("nombre", "Pendiente") ?: "Pendiente"

            filtroSeleccionado == "Todos" ||
                estado.equals(filtroSeleccionado, ignoreCase = true)
        }

        txtCantidad.text = if (filtrados.size == 1) {
            "1 pedido encontrado"
        } else {
            "${filtrados.size} pedidos encontrados"
        }

        if (filtrados.isEmpty()) {
            mostrarMensaje(
                if (listaPedidos.isEmpty()) {
                    "Todavía no hay pedidos registrados."
                } else {
                    "No hay pedidos con el estado seleccionado."
                }
            )
            return
        }

        filtrados.forEach { pedido ->

            val tarjeta = LayoutInflater.from(this).inflate(
                R.layout.item_pedido,
                contenedor,
                false
            )

            val cliente = pedido.optJSONObject("cliente")
            val nombre = listOf(
                cliente?.optString("nombres", "").orEmpty(),
                cliente?.optString("primer_apellido", "").orEmpty()
            ).filter { it.isNotBlank() && it != "null" }
                .joinToString(" ")
                .ifBlank { "Sin nombre" }

            val estado = pedido.optJSONObject("estado_pedido")
                ?.optString("nombre", "Pendiente") ?: "Pendiente"

            tarjeta.findViewById<TextView>(R.id.txtNumeroPedido).text =
                "PEDIDO #${pedido.optInt("id")}"

            tarjeta.findViewById<TextView>(R.id.txtEstadoPedido).apply {
                text = estado
                val color = when (estado.lowercase()) {
                    "entregado" -> Color.rgb(222, 239, 222)
                    "cancelado" -> Color.rgb(250, 225, 220)
                    "listo" -> Color.rgb(225, 235, 250)
                    "en preparación", "en preparacion" ->
                        Color.rgb(250, 239, 215)
                    else -> Color.rgb(255, 240, 215)
                }
                val texto = when (estado.lowercase()) {
                    "entregado" -> Color.rgb(55, 115, 65)
                    "cancelado" -> Color.rgb(170, 65, 55)
                    "listo" -> Color.rgb(55, 90, 145)
                    else -> Color.rgb(145, 105, 35)
                }
                setTextColor(texto)
                background = GradientDrawable().apply {
                    setColor(color)
                    cornerRadius = dp(20).toFloat()
                }
            }

            tarjeta.findViewById<TextView>(R.id.txtClientePedido).text = nombre

            tarjeta.findViewById<TextView>(R.id.txtFechaEntregaPedido).text =
                "Fecha de entrega: ${pedido.optString("fecha_entrega", "Sin fecha").take(10)}"

            tarjeta.findViewById<TextView>(R.id.txtTotalPedido).text =
                "Total: Bs ${pedido.optString("total", "0")}"

            tarjeta.findViewById<TextView>(R.id.txtAnticipoPedido).text =
                "Anticipo: Bs ${pedido.optString("anticipo", "0")}"

            val saldo = pedido.optDouble("saldo", 0.0)

            tarjeta.findViewById<TextView>(R.id.txtSaldoPedido).apply {
                text = "Saldo pendiente: Bs ${String.format(java.util.Locale.US, "%.2f", saldo)}"
                setTextColor(
                    if (saldo > 0) Color.rgb(180, 90, 65)
                    else Color.rgb(55, 125, 70)
                )
            }

            tarjeta.setOnClickListener {
                val intent = Intent(this, DetallePedidoActivity::class.java)
                intent.putExtra("pedido_id", pedido.optInt("id"))
                startActivity(intent)
            }

            contenedor.addView(tarjeta)
        }
    }

    private fun mostrarMensaje(mensaje: String) {
        contenedor.removeAllViews()

        val texto = TextView(this).apply {
            text = mensaje
            textSize = 15f
            gravity = android.view.Gravity.CENTER
            setTextColor(Color.rgb(120, 128, 120))
            setPadding(dp(12), dp(25), dp(12), dp(25))
        }

        contenedor.addView(texto)
        txtCantidad.text = ""
    }

    private fun dp(valor: Int): Int =
        (valor * resources.displayMetrics.density).toInt()
}
