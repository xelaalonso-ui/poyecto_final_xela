package com.example.redsocial;

import android.animation.AnimatorSet;
import android.animation.ObjectAnimator;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.view.View;
import android.view.animation.DecelerateInterpolator;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

// Pantalla de inicio (splash) que se muestra al abrir la app
// Dura 2 segundos y luego redirige al login o al main segun si ya hay sesion
public class SplashActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);

        TextView tvTitulo = findViewById(R.id.tv_title);

        // Animacion de entrada: el titulo aparece con fade y escala
        tvTitulo.setAlpha(0f);
        tvTitulo.setScaleX(0.7f);
        tvTitulo.setScaleY(0.7f);

        AnimatorSet animacion = new AnimatorSet();
        animacion.playTogether(
            ObjectAnimator.ofFloat(tvTitulo, View.ALPHA, 0f, 1f),
            ObjectAnimator.ofFloat(tvTitulo, View.SCALE_X, 0.7f, 1f),
            ObjectAnimator.ofFloat(tvTitulo, View.SCALE_Y, 0.7f, 1f)
        );
        animacion.setDuration(700);
        animacion.setInterpolator(new DecelerateInterpolator());
        animacion.start();

        // Esperamos 2 segundos y luego navegamos
        new Handler(Looper.getMainLooper()).postDelayed(new Runnable() {
            @Override
            public void run() {
                SessionManager sesion = new SessionManager(SplashActivity.this);

                Intent intent;
                // Si ya esta logueado vamos al main, si no al login
                if (sesion.isLoggedIn()) {
                    intent = new Intent(SplashActivity.this, MainActivity.class);
                } else {
                    intent = new Intent(SplashActivity.this, LoginActivity.class);
                }

                startActivity(intent);
                overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                finish(); // Cerramos el splash para que no vuelva al pulsar atras
            }
        }, 2000);
    }
}
