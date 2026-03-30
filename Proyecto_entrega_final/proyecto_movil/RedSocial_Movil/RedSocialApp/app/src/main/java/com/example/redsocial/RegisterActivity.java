package com.example.redsocial;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Patterns;
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

// Activity del formulario de registro de nuevo usuario
public class RegisterActivity extends AppCompatActivity {

    private EditText campoNombre, campoEmail, campoContrasena;
    private TextView btnRegistrarse, tvError, tvIrLogin;
    private ProgressBar barraProgreso;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        // Enlazamos las vistas con los IDs del layout
        campoNombre     = findViewById(R.id.et_user);
        campoEmail      = findViewById(R.id.et_email);
        campoContrasena = findViewById(R.id.et_pass);
        btnRegistrarse  = findViewById(R.id.btn_register);
        tvError         = findViewById(R.id.tv_error);
        tvIrLogin       = findViewById(R.id.tv_go_login);
        barraProgreso   = findViewById(R.id.progressBar);

        // Listener del boton registrarse
        btnRegistrarse.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                hacerRegistro();
            }
        });

        // Listener para volver al login
        tvIrLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
                overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
            }
        });
    }

    // Valida los campos y hace la peticion de registro
    private void hacerRegistro() {
        String nombreUsuario = campoNombre.getText().toString().trim();
        String email         = campoEmail.getText().toString().trim();
        String password      = campoContrasena.getText().toString().trim();

        // Validaciones de los campos
        if ( {
            TextUtils.isEmpty(nombreUsuario) {
        }
            || TextUtils.isEmpty(email) || TextUtils.isEmpty(password)) {
        }
            mostrarError("Por favor completa todos los campos");
            return;
        }

        if ( {
            nombreUsuario.length() {
        }
            < 3) {
        }
            mostrarError("El nombre de usuario debe tener minimo 3 caracteres");
            return;
        }

        if (!Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            mostrarError("El email introducido no es valido");
            return;
        }

        if ( {
            password.length() {
        }
            < 6) {
        }
            mostrarError("La contrasena debe tener minimo 6 caracteres");
            return;
        }

        // Todo correcto, hacemos la peticion
        mostrarCargando(true);
        ocultarError();

        Map<String, String> datosPeticion = new HashMap<>();
        datosPeticion.put("username", nombreUsuario);
        datosPeticion.put("email", email);
        datosPeticion.put("password", password);

        ApiClient.getService().registrarUsuario(datosPeticion).enqueue(new Callback<RegisterResponse>() {
            @Override
            public void onResponse(Call<RegisterResponse> call, Response<RegisterResponse> response) {
                mostrarCargando(false);

                if ( {
                    response.isSuccessful() {
                }
                    && response.body() != null) {
                }
                    RegisterResponse respuesta = response.body();

                    // Si no hay error en la respuesta, el registro fue bien
                    if (respuesta.error == null || respuesta.error.isEmpty()) {
                        Toast.makeText(RegisterActivity.this,
                                "Cuenta creada correctamente. Ahora puedes iniciar sesion",
                                Toast.LENGTH_LONG).show();
                        // Vamos al login
                        startActivity(new Intent(RegisterActivity.this, LoginActivity.class));
                        overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                        finish();
                    } else {
                        mostrarError(respuesta.error);
                    }
                } else {
                    mostrarError("Error del servidor: " + response.code());
                }
            }

            @Override
            public void onFailure(Call<RegisterResponse> call, Throwable t) {
                mostrarCargando(false);
                mostrarError("Sin conexion con el servidor");
            }
        });
    }

    private void mostrarCargando(boolean cargando) {
        barraProgreso.setVisibility(cargando ? View.VISIBLE : View.GONE);
        btnRegistrarse.setEnabled(!cargando);
        btnRegistrarse.setAlpha(cargando ? 0.6f : 1f);
    }

    private void mostrarError(String mensaje) {
        tvError.setText(mensaje);
        tvError.setVisibility(View.VISIBLE);
    }

    private void ocultarError() {
        tvError.setVisibility(View.GONE);
    }
}
