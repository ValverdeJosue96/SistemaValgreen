package com.valgreen.movil

import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity

class LoginActivity : AppCompatActivity() {

    private lateinit var edtEmail: EditText
    private lateinit var edtPassword: EditText
    private lateinit var btnLogin: Button
    private lateinit var txtMensajeLogin: TextView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)

        edtEmail = findViewById(R.id.edtEmail)
        edtPassword = findViewById(R.id.edtPassword)
        btnLogin = findViewById(R.id.btnLogin)
        txtMensajeLogin = findViewById(R.id.txtMensajeLogin)

        btnLogin.setOnClickListener {
            validarEIngresar()
        }
    }

    private fun validarEIngresar() {
        val email = edtEmail.text.toString().trim()
        val password = edtPassword.text.toString()

        if (email.isBlank() || password.isBlank()) {
            txtMensajeLogin.text = "Ingresa tu correo y contraseña."
            return
        }

        btnLogin.isEnabled = false
        btnLogin.text = "VALIDANDO..."
        txtMensajeLogin.text = ""

        Thread {
            try {
                val resultado = AuthService.iniciarSesion(email, password)

                runOnUiThread {
                    getSharedPreferences("ValgreenSesion", MODE_PRIVATE)
                        .edit()
                        .putString("token", resultado.token)
                        .putString("nombres", resultado.nombres)
                        .putString("rol", resultado.rol)
                        .apply()

                    btnLogin.isEnabled = true
                    btnLogin.text = "INGRESAR"

                    startActivity(
                        Intent(this, MainActivity::class.java)
                    )
                    finish()
                }
            } catch (e: Exception) {
                runOnUiThread {
                    btnLogin.isEnabled = true
                    btnLogin.text = "INGRESAR"
                    txtMensajeLogin.text =
                        e.message ?: "No se pudo conectar con el servidor."
                }

                Log.e("Valgreen", "Error de inicio de sesión", e)
            }
        }.start()
    }
}
