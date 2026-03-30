package com.example.redsocial;

import android.animation.ObjectAnimator;
import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.view.animation.DecelerateInterpolator;
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

public class LoginActivity extends AppCompatActivity {

    private EditText etUser, etPass;
    private TextView btnLogin, tvError, tvGoRegister;
    private ProgressBar progressBar;
    private SessionManager session;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        session = new SessionManager(this);

        // Si ya está logueado, ir directamente al main
        if (session.isLoggedIn()) {
            goToMain();
            return;
        }

        etUser      = findViewById(R.id.et_user);
        etPass      = findViewById(R.id.et_pass);
        btnLogin    = findViewById(R.id.btn_login);
        tvError     = findViewById(R.id.tv_error);
        tvGoRegister= findViewById(R.id.tv_go_register);
        progressBar = findViewById(R.id.progressBar);

        // Animación entrada del card
        View card = btnLogin.getRootView().findViewWithTag("login_card");
        animateEntrance();

        btnLogin.setOnClickListener(v -> {
            // Animación de click
            animateButtonClick(btnLogin);
            doLogin();
        });

        tvGoRegister.setOnClickListener(v -> {
            startActivity(new Intent(this, RegisterActivity.class));
            overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
        });
    }

    private void animateEntrance() {
        View root = getWindow().getDecorView();
        root.setAlpha(0f);
        ObjectAnimator anim = ObjectAnimator.ofFloat(root, View.ALPHA, 0f, 1f);
        anim.setDuration(500);
        anim.setInterpolator(new DecelerateInterpolator());
        anim.start();
    }

    private void animateButtonClick(View v) {
        ObjectAnimator scaleX = ObjectAnimator.ofFloat(v, View.SCALE_X, 1f, 0.95f, 1f);
        ObjectAnimator scaleY = ObjectAnimator.ofFloat(v, View.SCALE_Y, 1f, 0.95f, 1f);
        scaleX.setDuration(200);
        scaleY.setDuration(200);
        scaleX.start();
        scaleY.start();
    }

    private void doLogin() {
        String user = etUser.getText().toString().trim();
        String pass = etPass.getText().toString().trim();

        if (TextUtils.isEmpty(user) || TextUtils.isEmpty(pass)) {
            showError("Por favor completa todos los campos");
            shakeView(user.isEmpty() ? etUser : etPass);
            return;
        }

        showLoading(true);
        hideError();

        Map<String, String> body = new HashMap<>();
        body.put("email", user);
        body.put("password", pass);

        ApiClient.getService().login(body).enqueue(new Callback<LoginResponse>() {
            @Override
            public void onResponse(Call<LoginResponse> call, Response<LoginResponse> response) {
                showLoading(false);

                if (response.isSuccessful() && response.body() != null) {
                    LoginResponse r = response.body();
                    if (r.usuario != null) {

                        session.saveSession(
                                r.usuario.idUsuario,
                                r.usuario.username,
                                r.usuario.email
                        );
                        goToMain();
                    } else if (r.error != null) {
                        showError(r.error);
                    } else {
                        showError("Usuario o contraseña incorrectos");
                    }
                } else {
                    showError("Error de servidor (" + response.code() + ")");
                }
            }

            @Override
            public void onFailure(Call<LoginResponse> call, Throwable t) {
                showLoading(false);
                showError("Sin conexión con el servidor.\nVerifica la URL en ApiClient.java");
            }
        });
    }

    private void shakeView(View v) {
        ObjectAnimator shake = ObjectAnimator.ofFloat(v, View.TRANSLATION_X,
                0f, 15f, -15f, 10f, -10f, 5f, -5f, 0f);
        shake.setDuration(500);
        shake.start();
    }

    private void showLoading(boolean show) {
        progressBar.setVisibility(show ? View.VISIBLE : View.GONE);
        btnLogin.setEnabled(!show);
        btnLogin.setAlpha(show ? 0.6f : 1f);
    }

    private void showError(String msg) {
        tvError.setText(msg);
        tvError.setVisibility(View.VISIBLE);
        ObjectAnimator anim = ObjectAnimator.ofFloat(tvError, View.ALPHA, 0f, 1f);
        anim.setDuration(300);
        anim.start();
    }

    private void hideError() {
        tvError.setVisibility(View.GONE);
    }

    private void goToMain() {
        startActivity(new Intent(this, MainActivity.class));
        overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
        finish();
    }
}
