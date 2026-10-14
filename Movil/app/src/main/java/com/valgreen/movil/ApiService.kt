
package com.valgreen.movil

import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL

object ApiService {

    // Dirección para pruebas con el emulador Android.
    // Cuando usemos el teléfono físico, cambiaremos esta dirección.
    private const val BASE_URL =
        "http://192.168.1.159:8000/api/productos"

    fun obtenerProductos(): List<Producto> {
        val conexion = URL(BASE_URL)
            .openConnection() as HttpURLConnection

        try {
            conexion.requestMethod = "GET"
            conexion.connectTimeout = 10000
            conexion.readTimeout = 10000
            conexion.setRequestProperty(
                "Accept",
                "application/json"
            )

            if (conexion.responseCode !in 200..299) {
                throw Exception(
                    "Error HTTP: ${conexion.responseCode}"
                )
            }

            val respuesta = conexion.inputStream
                .bufferedReader()
                .use { it.readText() }

            val json = JSONObject(respuesta)
            val listaJson = json.getJSONArray("data")
            val productos = mutableListOf<Producto>()

            for (i in 0 until listaJson.length()) {
                val item = listaJson.getJSONObject(i)

                productos.add(
                    Producto(
                        id = item.getInt("id"),
                        nombre = item.getString("nombre"),
                        descripcion =
                            item.optString("descripcion")
                                .takeUnless { it == "null" || it.isBlank() },
                        precio = item.get("precio").toString(),
                        imagen =
                            item.optString("imagen")
                                .takeUnless { it == "null" || it.isBlank() },
                        categoria =
                            item.optString("categoria")
                                .takeUnless { it == "null" || it.isBlank() }
                    )
                )
            }

            return productos
        } finally {
            conexion.disconnect()
        }
    }
}
