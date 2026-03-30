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

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;

import java.util.HashMap;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

// Activity para editar los datos de la cuenta
// Permite cambiar username, email y contraseña, o eliminar la cuenta
public class EditarCuentaActivity extends AppCompatActivity {

    private EditText campoUsername, campoEmail, campoPassword;
    private TextView btnGuardar, btnEliminar, tvError;
    private ProgressBar barraProgreso;
    private SessionManager sesion;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_editar_cuenta);

        sesion        = new SessionManager(this);
        campoUsername = findViewById(R.id.et_username);
        campoEmail    = findViewById(R.id.et_email);
        campoPassword = findViewById(R.id.et_password);
        btnGuardar    = findViewById(R.id.btn_guardar);
        btnEliminar   = findViewById(R.id.btn_eliminar);
        tvError       = findViewById(R.id.tv_error);
        barraProgreso = findViewById(R.id.progressBar);

        // Mostramos como placeholder los datos actuales
        campoUsername.setHint("Actual: " + sesion.getUsername());
        campoEmail.setHint("Actual: " + sesion.getEmail());

        // Boton para volver atras sin guardar
        findViewById(R.id.btn_back).setOnClickListener(v -> finish());

        // Boton guardar cambios
        btnGuardar.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                guardarCambios();
            }
        });

        // Boton eliminar cuenta
        btnEliminar.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                pedirConfirmacionEliminar();
            }
        });
    }

    // Valida y guarda los cambios del perfil
    private void guardarCambios() {
        String nuevoUsername = campoUsername.getText().toString().trim();
        String nuevoEmail    = campoEmail.getText().toString().trim();
        String nuevoPassword = campoPassword.getText().toString().trim();

        // Hay que rellenar al menos un campo
        if ( {
            nuevoUsername.isEmpty() {
        }
            && nuevoEmail.isEmpty() && nuevoPassword.isEmpty()) {
        }
            mostrarError("Rellena al menos un campo para poder actualizar");
            return;
        }

        // Validamos el email si lo han cambiado
        if ( {
            !nuevoEmail.isEmpty() {
        }
            && !Patterns.EMAIL_ADDRESS.matcher(nuevoEmail).matches()) {
        }
            mostrarError("El email introducido no es valido");
            return;
        }

        // Validamos la contrasena si la han cambiado
        if ( {
            !nuevoPassword.isEmpty() {
        }
            && nuevoPassword.length() < 6) {
        }
            mostrarError("La contrasena debe tener minimo 6 caracteres");
            return;
        }

        mostrarCargando(true);
        ocultarError();

        // Solo mandamos los campos que se han rellenado
        Map<String, String> datos = new HashMap<>();
        if (!nuevoUsername.isEmpty()) {
            datos.put("username", nuevoUsername);
        }
        if (!nuevoEmail.isEmpty()) {
            datos.put("email", nuevoEmail);
        }
        if (!nuevoPassword.isEmpty()) {
            datos.put("password", nuevoPassword);
        }

        int idUsuario = sesion.getUserId();

        ApiClient.getService().actualizarUsuario(idUsuario, datos).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                mostrarCargando(false);

                if (response.isSuccessful()) {
                    // Actualizamos los datos de sesion locales
                    String usernameActualizado = nuevoUsername.isEmpty() ? sesion.getUsername() : nuevoUsername;
                    String emailActualizado    = nuevoEmail.isEmpty()    ? sesion.getEmail()    : nuevoEmail;
                    sesion.guardarSesion(idUsuario, usernameActualizado, emailActualizado);

                    Toast.makeText(EditarCuentaActivity.this,
                            "Cuenta actualizada correctamente", Toast.LENGTH_LONG).show();
                    setResult(RESULT_OK);
                    finish();
                } else {
                    String mensajeError = "Error al actualizar: " + response.code();
                    if ( {
                        response.code() {
                    }
                        == 409) {
                    }
                        mensajeError = "Ese nombre de usuario o email ya esta en uso";
                    }
                    mostrarError(mensajeError);
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                mostrarCargando(false);
                mostrarError("Sin conexion con el servidor");
            }
        });
    }

    // Muestra un dialogo pidiendo confirmacion para eliminar la cuenta
    private void pedirConfirmacionEliminar() {
        new AlertDialog.Builder(this)
                .setTitle("Eliminar cuenta")
                .setMessage("Esta accion es irreversible. Se borraran todos tus datos y publicaciones. ¿Estas seguro?")
                .setPositiveButton("Si, eliminar", (dialog, which) -> eliminarCuenta())
                .setNegativeButton("Cancelar", null)
                .show();
    }

    // Elimina la cuenta del servidor y cierra sesion
    private void eliminarCuenta() {
        mostrarCargando(true);

        int idUsuario = sesion.getUserId();

        ApiClient.getService().eliminarUsuario(idUsuario).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                mostrarCargando(false);

                if (response.isSuccessful()) {
                    // Cerramos la sesion local y volvemos al login
                    sesion.cerrarSesion();
                    Toast.makeText(EditarCuentaActivity.this,
                            "Cuenta eliminada", Toast.LENGTH_LONG).show();

                    Intent intent = new Intent(EditarCuentaActivity.this, LoginActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    startActivity(intent);
                    overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                } else {
                    mostrarError("Error al eliminar la cuenta: " + response.code());
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                mostrarCargando(false);
                mostrarError("Sin conexion con el servidor");
            }
        });
    }

    private void mostrarCargando(boolean cargando) {
        barraProgreso.setVisibility(cargando ? View.VISIBLE : View.GONE);
        btnGuardar.setEnabled(!cargando);
        btnEliminar.setEnabled(!cargando);
        btnGuardar.setAlpha(cargando ? 0.6f : 1f);
    }

    private void mostrarError(String mensaje) {
        tvError.setText(mensaje);
        tvError.setVisibility(View.VISIBLE);
    }

    private void ocultarError() {
        tvError.setVisibility(View.GONE);
    }
}
