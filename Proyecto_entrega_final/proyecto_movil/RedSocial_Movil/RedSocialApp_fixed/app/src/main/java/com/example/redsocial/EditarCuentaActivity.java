package com.example.redsocial;

import android.animation.ObjectAnimator;
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

public class EditarCuentaActivity extends AppCompatActivity {

    private EditText etUsername, etEmail, etPassword;
    private TextView btnGuardar, btnEliminar, tvError;
    private ProgressBar progressBar;
    private SessionManager session;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_editar_cuenta);

        session      = new SessionManager(this);
        etUsername   = findViewById(R.id.et_username);
        etEmail      = findViewById(R.id.et_email);
        etPassword   = findViewById(R.id.et_password);
        btnGuardar   = findViewById(R.id.btn_guardar);
        btnEliminar  = findViewById(R.id.btn_eliminar);
        tvError      = findViewById(R.id.tv_error);
        progressBar  = findViewById(R.id.progressBar);

        // Rellenar con datos actuales como placeholder
        etUsername.setHint("Actual: " + session.getUsername());
        etEmail.setHint("Actual: " + session.getEmail());

        findViewById(R.id.btn_back).setOnClickListener(v -> finish());

        btnGuardar.setOnClickListener(v -> {
            animateClick(v);
            guardarCambios();
        });

        btnEliminar.setOnClickListener(v -> {
            animateClick(v);
            confirmarEliminar();
        });
    }

    private void guardarCambios() {
        String username = etUsername.getText().toString().trim();
        String email    = etEmail.getText().toString().trim();
        String password = etPassword.getText().toString().trim();

        // Validar que al menos un campo esté relleno
        if (username.isEmpty() && email.isEmpty() && password.isEmpty()) {
            showError("Rellena al menos un campo para actualizar");
            return;
        }

        // Validar email si se proporcionó
        if (!email.isEmpty() && !Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            showError("El email no es válido");
            return;
        }

        // Validar password si se proporcionó
        if (!password.isEmpty() && password.length() < 6) {
            showError("La contraseña debe tener al menos 6 caracteres");
            return;
        }

        showLoading(true);
        hideError();

        Map<String, String> body = new HashMap<>();
        if (!username.isEmpty()) body.put("username", username);
        if (!email.isEmpty())    body.put("email", email);
        if (!password.isEmpty()) body.put("password", password);

        int idUsuario = session.getUserId();

        ApiClient.getService().updateUsuario(idUsuario, body).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                showLoading(false);

                if (response.isSuccessful()) {
                    // Actualizar sesión local con los nuevos datos
                    String newUsername = username.isEmpty() ? session.getUsername() : username;
                    String newEmail    = email.isEmpty()    ? session.getEmail()    : email;
                    session.saveSession(idUsuario, newUsername, newEmail);

                    Toast.makeText(EditarCuentaActivity.this,
                            " Cuenta actualizada correctamente", Toast.LENGTH_LONG).show();
                    setResult(RESULT_OK);
                    finish();
                } else {
                    String msg = "Error al actualizar (" + response.code() + ")";
                    if (response.code() == 409) msg = "Ese usuario o email ya está en uso";
                    showError(msg);
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                showLoading(false);
                showError("Sin conexión con el servidor");
            }
        });
    }

    private void confirmarEliminar() {
        new AlertDialog.Builder(this)
                .setTitle("⚠️ Eliminar cuenta")
                .setMessage("¿Estás seguro? Esta acción es irreversible y perderás todos tus datos.")
                .setPositiveButton("Sí, eliminar", (dialog, which) -> eliminarCuenta())
                .setNegativeButton("Cancelar", null)
                .show();
    }

    private void eliminarCuenta() {
        showLoading(true);
        hideError();

        int idUsuario = session.getUserId();

        ApiClient.getService().deleteUsuario(idUsuario).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                showLoading(false);

                if (response.isSuccessful()) {
                    session.logout();
                    Toast.makeText(EditarCuentaActivity.this,
                            "Cuenta eliminada", Toast.LENGTH_LONG).show();
                    Intent intent = new Intent(EditarCuentaActivity.this, LoginActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    startActivity(intent);
                    overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                } else {
                    showError("Error al eliminar cuenta (" + response.code() + ")");
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                showLoading(false);
                showError("Sin conexión con el servidor");
            }
        });
    }

    private void animateClick(View v) {
        ObjectAnimator.ofFloat(v, View.SCALE_X, 1f, 0.95f, 1f).setDuration(200).start();
        ObjectAnimator.ofFloat(v, View.SCALE_Y, 1f, 0.95f, 1f).setDuration(200).start();
    }

    private void showLoading(boolean show) {
        progressBar.setVisibility(show ? View.VISIBLE : View.GONE);
        btnGuardar.setEnabled(!show);
        btnEliminar.setEnabled(!show);
        btnGuardar.setAlpha(show ? 0.6f : 1f);
    }

    private void showError(String msg) {
        tvError.setText(msg);
        tvError.setVisibility(View.VISIBLE);
    }

    private void hideError() {
        tvError.setVisibility(View.GONE);
    }
}
