
package com.valgreen.movil

import android.os.Bundle
import android.view.View
import android.widget.ProgressBar
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView

class MainActivity : AppCompatActivity() {

    private lateinit var progressBar: ProgressBar
    private lateinit var txtEstado: TextView
    private lateinit var recyclerProductos: RecyclerView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        progressBar = findViewById(R.id.progressBar)
        txtEstado = findViewById(R.id.txtEstado)
        recyclerProductos = findViewById(R.id.recyclerProductos)

        recyclerProductos.layoutManager =
            LinearLayoutManager(this)

        cargarProductos()
    }

    private fun cargarProductos() {
        progressBar.visibility = View.VISIBLE
        txtEstado.text = "Cargando productos..."

        Thread {
            try {
                val productos = ApiService.obtenerProductos()

                runOnUiThread {
                    progressBar.visibility = View.GONE

                    recyclerProductos.adapter =
                        ProductoAdapter(productos)

                    txtEstado.text =
                        if (productos.isEmpty()) {
                            "Todavía no hay productos disponibles."
                        } else {
                            "Productos disponibles: ${productos.size}"
                        }
                }
            } catch (e: Exception) {
                runOnUiThread {
                    progressBar.visibility = View.GONE
                    txtEstado.text ="Error: ${e.message ?: "Sin detalles"}"
                }

                android.util.Log.e(
                    "Valgreen",
                    "Error al cargar productos",
                    e
                )
            }
        }.start()
    }
}
