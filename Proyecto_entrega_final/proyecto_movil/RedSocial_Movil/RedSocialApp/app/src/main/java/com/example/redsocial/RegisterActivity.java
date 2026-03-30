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

import androidx.appcompat.app.AppCompatActivity;

import java.util.HashMap;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class RegisterActivity extends AppCompatActivity {

    private EditText etUser, etEmail, etPass;
    private TextView btnRegister, tvError, tvGoLogin;
    private ProgressBar progressBar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        etUser     = findViewById(R.id.et_user);
        etEmail    = findViewById(R.id.et_email);
        etPass     = findViewById(R.id.et_pass);
        btnRegister= findViewById(R.id.btn_register);
        tvError    = findViewById(R.id.tv_error);
        tvGoLogin  = findViewById(R.id.tv_go_login);
        progressBar= findViewById(R.id.progressBar);

        btnRegister.setOnClickListener(v -> {
            animateButtonClick(v);
            doRegister();
        });

        tvGoLogin.setOnClickListener(v -> {
            finish(); // Volver al login
            overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
        });
    }

    private void doRegister() {
        String user  = etUser.getText().toString().trim();
        String email = etEmail.getText().toString().trim();
        String pass  = etPass.getText().toString().trim();

        // Validaciones en cliente
        if (TextUtils.isEmpty(user) || TextUtils.isEmpty(email) || TextUtils.isEmpty(pass)) {
            showError("Por favor completa todos los campos");
            return;
        }
        if (user.length() < 3) {
            showError("El usuario debe tener al menos 3 caracteres");
            shakeView(etUser);
            return;
        }
        if (!Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            showError("Introduce un email válido");
            shakeView(etEmail);
            return;
        }
        if (pass.length() < 6) {
            showError("La contraseña debe tener al menos 6 caracteres");
            shakeView(etPass);
            return;
        }

        showLoading(true);
        hideError();

        Map<String, String> body = new HashMap<>();
        body.put("username", user);
        body.put("email", email);
        body.put("password", pass);  // La API espera "password" y hashea internamente

        ApiClient.getService().register(body).enqueue(new Callback<RegisterResponse>() {
            @Override
            public void onResponse(Call<RegisterResponse> call, Response<RegisterResponse> response) {
                showLoading(false);

                if (response.isSuccessful() && response.body() != null) {
                    RegisterResponse r = response.body();
                    if (r.error == null || r.error.isEmpty()) {
                        Toast.makeText(RegisterActivity.this,
                                "🌸 ¡Cuenta creada! Ahora inicia sesión",
                                Toast.LENGTH_LONG).show();
                        startActivity(new Intent(RegisterActivity.this, LoginActivity.class));
                        overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                        finish();
                    } else {
                        showError(r.error);
                    }
                } else {
                    showError("Error del servidor (" + response.code() + ")");
                }
            }

            @Override
            public void onFailure(Call<RegisterResponse> call, Throwable t) {
                showLoading(false);
                showError("Sin conexión con el servidor");
            }
        });
    }

    private void animateButtonClick(View v) {
        ObjectAnimator.ofFloat(v, View.SCALE_X, 1f, 0.95f, 1f).setDuration(200).start();
        ObjectAnimator.ofFloat(v, View.SCALE_Y, 1f, 0.95f, 1f).setDuration(200).start();
    }

    private void shakeView(View v) {
        ObjectAnimator shake = ObjectAnimator.ofFloat(v, View.TRANSLATION_X,
                0f, 14f, -14f, 10f, -10f, 4f, -4f, 0f);
        shake.setDuration(450);
        shake.start();
    }

    private void showLoading(boolean show) {
        progressBar.setVisibility(show ? View.VISIBLE : View.GONE);
        btnRegister.setEnabled(!show);
        btnRegister.setAlpha(show ? 0.6f : 1f);
    }

    private void showError(String msg) {
        tvError.setText(msg);
        tvError.setVisibility(View.VISIBLE);
    }

    private void hideError() {
        tvError.setVisibility(View.GONE);
    }
}
