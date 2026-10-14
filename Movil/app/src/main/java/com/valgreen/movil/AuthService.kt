package com.valgreen.movil

import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL

data class LoginResult(
    val token: String,
    val nombres: String,
    val rol: String
)

object AuthService {

    private const val BASE_URL ="http://192.168.1.159:8000/api/login"

    fun iniciarSesion(email: String, password: String): LoginResult {
        val conexion = URL(BASE_URL).openConnection() as HttpURLConnection

        try {
            conexion.requestMethod = "POST"
            conexion.connectTimeout = 10000
            conexion.readTimeout = 10000
            conexion.doOutput = true

            conexion.setRequestProperty(
                "Accept",
                "application/json"
            )
            conexion.setRequestProperty(
                "Content-Type",
                "application/json; charset=UTF-8"
            )

            val datos = JSONObject().apply {
                put("email", email)
                put("password", password)
            }

            conexion.outputStream.use { salida ->
                salida.write(datos.toString().toByteArray(Charsets.UTF_8))
            }

            val codigo = conexion.responseCode
            val flujo = if (codigo in 200..299) {
                conexion.inputStream
            } else {
                conexion.errorStream
            }

            val respuesta = flujo?.bufferedReader()?.use { it.readText() }
                ?: throw Exception("El servidor no devolvió una respuesta.")

            val json = JSONObject(respuesta)

            if (codigo !in 200..299 || !json.optBoolean("success")) {
                throw Exception(
                    json.optString(
                        "message",
                        "No se pudo iniciar sesión."
                    )
                )
            }

            val usuario = json.getJSONObject("usuario")

            return LoginResult(
                token = json.getString("token"),
                nombres = usuario.optString("nombres"),
                rol = usuario.optString("rol")
            )
        } finally {
            conexion.disconnect()
        }
    }
}