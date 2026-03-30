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

public class SplashActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);

        TextView tvTitle = findViewById(R.id.tv_title);

        // Animación de entrada: fade + scale
        tvTitle.setAlpha(0f);
        tvTitle.setScaleX(0.7f);
        tvTitle.setScaleY(0.7f);

        AnimatorSet set = new AnimatorSet();
        set.playTogether(
            ObjectAnimator.ofFloat(tvTitle, View.ALPHA, 0f, 1f),
            ObjectAnimator.ofFloat(tvTitle, View.SCALE_X, 0.7f, 1f),
            ObjectAnimator.ofFloat(tvTitle, View.SCALE_Y, 0.7f, 1f)
        );
        set.setDuration(700);
        set.setInterpolator(new DecelerateInterpolator());
        set.start();


        new Handler(Looper.getMainLooper()).postDelayed(() -> {
            SessionManager session = new SessionManager(this);
            Class<?> destino = session.isLoggedIn()
                    ? MainActivity.class
                    : LoginActivity.class;

            Intent intent = new Intent(this, destino);
            startActivity(intent);
            overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
            finish();
        }, 2000);
    }
}
