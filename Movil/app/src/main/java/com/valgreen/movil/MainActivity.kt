package com.valgreen.movil

import android.content.Intent
import android.os.Bundle
import android.widget.LinearLayout
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {

    private lateinit var btnRegistrarPedido: LinearLayout
    private lateinit var btnVerPedidos: LinearLayout

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        btnRegistrarPedido = findViewById(R.id.btnRegistrarPedido)
        btnVerPedidos = findViewById(R.id.btnVerPedidos)

        btnRegistrarPedido.setOnClickListener {
            startActivity(
                android.content.Intent(
                    this,
                    RegistrarPedidoActivity::class.java
                )
            )
        }

        btnVerPedidos.setOnClickListener {
            startActivity(
                android.content.Intent(this, PedidosActivity::class.java)
            )
        }
    }
}
