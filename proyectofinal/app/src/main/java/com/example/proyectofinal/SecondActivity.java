package com.example.proyectofinal;

import android.animation.ArgbEvaluator;
import android.animation.ObjectAnimator;
import android.animation.ValueAnimator;
import android.media.MediaPlayer;
import android.net.Uri;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuInflater;
import android.widget.TextView;
import android.widget.VideoView;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import java.util.Random;

public class SecondActivity extends AppCompatActivity {

    Toolbar toolbar;
    TextView tituloAnimado;

    Random random = new Random();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.secundario_activity);


        // Ajuste Edge-to-Edge
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        // Toolbar
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowTitleEnabled(false);
        toolbar.setTitle("");



        // TextView con animación
        tituloAnimado = findViewById(R.id.tituloAnimado);
        tituloAnimado.setShadowLayer(12f, 0f, 0f, 0xFFFF69B4);
        startHandwritingGlow(tituloAnimado, "Resulta, pasa y acontece...");
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater menuInflater = getMenuInflater();
        menuInflater.inflate(R.menu.ativity_menu, menu);
        return true;
    }

    // Animación handwriting + glow
    private void startHandwritingGlow(TextView textView, String text) {
        textView.setText("");
        final int[] index = {0};


// Iniciamos el brillo UNA SOLA VEZ
        startGlowAnimation(textView);


        Runnable characterAdder = new Runnable() {
            @Override
            public void run() {
                if (index[0] < text.length()) {
                    textView.append(String.valueOf(text.charAt(index[0])));
                    index[0]++;


                    long delay = 60 + random.nextInt(140); // efecto escritura real
                    textView.postDelayed(this, delay);
                }
            }
        };


        textView.postDelayed(characterAdder, 200);
    }



    private void startGlowAnimation(TextView textView) {
        ObjectAnimator animator = ObjectAnimator.ofInt(
                textView,
                "textColor",
                0xFFFEAC5E, // rosa barbie
                0xFFC779D0, // rosa claro
                0xFF4BC0C8, // blanco brillo
                0xFFFF69B4
        );
        animator.setEvaluator(new ArgbEvaluator());
        animator.setRepeatMode(ValueAnimator.REVERSE);
        animator.setRepeatCount(ValueAnimator.INFINITE);
        animator.setDuration(8000);
        animator.start();
    }
}