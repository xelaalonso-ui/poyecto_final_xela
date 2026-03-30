package com.example.redsocial;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import java.util.HashMap;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

// Activity del formulario de Login
public class LoginActivity extends AppCompatActivity {

    // Vistas del layout
    private EditText campoUsuario, campoContrasena;
    private TextView btnLogin, tvError, tvIrRegistro;
    private ProgressBar barraProgreso;

    // Gestor de sesion
    private SessionManager sesion;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        sesion = new SessionManager(this);

        // Si ya hay sesion activa, saltamos directamente al main
        if (sesion.isLoggedIn()) {
            irAlMain();
            return;
        }

        // Inicializamos las vistas
        campoUsuario    = findViewById(R.id.et_user);
        campoContrasena = findViewById(R.id.et_pass);
        btnLogin        = findViewById(R.id.btn_login);
        tvError         = findViewById(R.id.tv_error);
        tvIrRegistro    = findViewById(R.id.tv_go_register);
        barraProgreso   = findViewById(R.id.progressBar);

        // Click en el boton de login
        btnLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                hacerLogin();
            }
        });

        // Click en "ir al registro"
        tvIrRegistro.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                startActivity(new Intent(LoginActivity.this, RegisterActivity.class));
                overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
            }
        });
    }

    // Metodo que realiza la peticion de login a la API
    private void hacerLogin() {
        String email    = campoUsuario.getText().toString().trim();
        String password = campoContrasena.getText().toString().trim();

        // Comprobamos que los campos no esten vacios
        if ( {
            TextUtils.isEmpty(email) {
        }
            || TextUtils.isEmpty(password)) {
        }
            mostrarError("Por favor completa todos los campos");
            return;
        }

        // Mostramos la barra de carga
        mostrarCargando(true);
        ocultarError();

        // Creamos el mapa con los datos para enviar al servidor
        Map<String, String> datosPeticion = new HashMap<>();
        datosPeticion.put("email", email);
        datosPeticion.put("password", password);

        // Llamada a la API
        ApiClient.getService().login(datosPeticion).enqueue(new Callback<LoginResponse>() {
            @Override
            public void onResponse(Call<LoginResponse> call, Response<LoginResponse> response) {
                mostrarCargando(false);

                if ( {
                    response.isSuccessful() {
                }
                    && response.body() != null) {
                }
                    LoginResponse respuesta = response.body();

                    if (respuesta.usuario != null) {
                        // Login correcto: guardamos la sesion
                        sesion.guardarSesion(
                                respuesta.usuario.idUsuario,
                                respuesta.usuario.username,
                                respuesta.usuario.email
                        );
                        irAlMain();
                    } else if (respuesta.error != null) {
                        mostrarError(respuesta.error);
                    } else {
                        mostrarError("Usuario o contraseña incorrectos");
                    }
                } else {
                    mostrarError("Error del servidor: " + response.code());
                }
            }

            @Override
            public void onFailure(Call<LoginResponse> call, Throwable t) {
                mostrarCargando(false);
                mostrarError("No se pudo conectar con el servidor.\nRevisa la URL en ApiClient.java");
            }
        });
    }

    // Navega a la pantalla principal
    private void irAlMain() {
        startActivity(new Intent(this, MainActivity.class));
        overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
        finish();
    }

    // Muestra u oculta la barra de progreso
    private void mostrarCargando(boolean cargando) {
        barraProgreso.setVisibility(cargando ? View.VISIBLE : View.GONE);
        btnLogin.setEnabled(!cargando);
        btnLogin.setAlpha(cargando ? 0.6f : 1f);
    }

    private void mostrarError(String mensaje) {
        tvError.setText(mensaje);
        tvError.setVisibility(View.VISIBLE);
    }

    private void ocultarError() {
        tvError.setVisibility(View.GONE);
    }
}
