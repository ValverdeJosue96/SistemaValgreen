package com.valgreen.movil

import android.os.Bundle
import android.view.Gravity
import android.view.ViewGroup
import android.widget.LinearLayout
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL
import kotlin.concurrent.thread

class PedidosActivity : AppCompatActivity() {

    private lateinit var contenedor: LinearLayout

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val scroll = android.widget.ScrollView(this).apply {
            isFillViewport = true
            setBackgroundColor(
                android.graphics.Color.rgb(255, 248, 237)
            )
        }

        val raiz = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setPadding(24, 24, 24, 24)
        }

        val titulo = TextView(this).apply {
            text = "Mis pedidos"
            textSize = 26f
            setTextColor(android.graphics.Color.rgb(82, 111, 85))
        }

        val subtitulo = TextView(this).apply {
            text = "Consulta los pedidos registrados."
            textSize = 15f
            setPadding(0, 8, 0, 20)
        }

        contenedor = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
        }

        raiz.addView(titulo)
        raiz.addView(subtitulo)
        raiz.addView(contenedor)

        scroll.addView(raiz)
        setContentView(scroll)

        cargarPedidos()
    }

    private fun cargarPedidos() {
        contenedor.removeAllViews()

        val progreso = ProgressBar(this)
        contenedor.addView(progreso)

        thread {
            var resultado: String? = null
            var error: String? = null

            try {
                val preferencias = getSharedPreferences(
                    "ValgreenSesion",
                    MODE_PRIVATE
                )
                val token = preferencias.getString("token", null)

                if (token.isNullOrBlank()) {
                    throw Exception("La sesión ha caducado. Inicia sesión nuevamente.")
                }

                val conexion = URL(
                    "${ApiConfig.BASE_URL}/pedidos"
                ).openConnection() as HttpURLConnection

                conexion.requestMethod = "GET"
                conexion.connectTimeout = 10000
                conexion.readTimeout = 10000
                conexion.setRequestProperty(
                    "Authorization",
                    "Bearer $token"
                )
                conexion.setRequestProperty(
                    "Accept",
                    "application/json"
                )

                val codigo = conexion.responseCode
                val flujo = if (codigo in 200..299) {
                    conexion.inputStream
                } else {
                    conexion.errorStream
                }

                val cuerpo = flujo.bufferedReader().use { it.readText() }
                conexion.disconnect()

                if (codigo !in 200..299) {
                    throw Exception("Error del servidor ($codigo).")
                }

                resultado = cuerpo

            } catch (e: Exception) {
                error = e.message ?: "No se pudieron cargar los pedidos."
            }

            runOnUiThread {
                contenedor.removeAllViews()

                if (error != null) {
                    mostrarMensaje(error!!)
                    return@runOnUiThread
                }

                try {
                    val json = JSONObject(resultado!!)
                    val pedidos = json.getJSONArray("data")

                    if (pedidos.length() == 0) {
                        mostrarMensaje("Todavía no hay pedidos registrados.")
                        return@runOnUiThread
                    }

                    for (i in 0 until pedidos.length()) {
                        val pedido = pedidos.getJSONObject(i)

                        val cliente = pedido.optJSONObject("cliente")
                        val estado = pedido.optJSONObject("estado_pedido")

                        val nombre = listOf(
                            cliente?.optString("nombres", "").orEmpty(),
                            cliente?.optString("primer_apellido", "").orEmpty()
                        ).filter { it.isNotBlank() && it != "null" }
                            .joinToString(" ")

                        val tarjeta = TextView(this).apply {
                            text = buildString {
                                append("Pedido #${pedido.optInt("id")}\n")
                                append("Cliente: ${nombre.ifBlank { "Sin nombre" }}\n")
                                append("Estado: ${estado?.optString("nombre", "Pendiente") ?: "Pendiente"}\n")
                                append("Total: Bs ${pedido.optString("total", "0")}\n")
                                append("Anticipo: Bs ${pedido.optString("anticipo", "0")}\n")
                                append("Saldo: Bs ${pedido.optString("saldo", "0")}\n")
                                append("Entrega: ${pedido.optString("fecha_entrega", "Sin fecha")}")
                            }
                            textSize = 16f
                            setTextColor(android.graphics.Color.rgb(52, 61, 52))
                            setPadding(20, 20, 20, 20)
                            setBackgroundColor(
                                android.graphics.Color.WHITE
                            )
                        }

                        val parametros = LinearLayout.LayoutParams(
                            ViewGroup.LayoutParams.MATCH_PARENT,
                            ViewGroup.LayoutParams.WRAP_CONTENT
                        ).apply {
                            bottomMargin = 16
                        }

                        contenedor.addView(tarjeta, parametros)
                    }

                } catch (e: Exception) {
                    mostrarMensaje(
                        "No se pudo interpretar la respuesta de la API."
                    )
                }
            }
        }
    }

    private fun mostrarMensaje(mensaje: String) {
        contenedor.removeAllViews()

        val texto = TextView(this).apply {
            text = mensaje
            textSize = 16f
            setPadding(8, 16, 8, 16)
        }

        contenedor.addView(texto)
    }
}
