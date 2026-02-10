package com.example.proyectofinal;

import android.content.Intent;
import android.media.MediaPlayer;
import android.media.browse.MediaBrowser;
import android.net.Uri;
import android.os.Bundle;
import android.widget.Button;
import android.widget.VideoView;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class MainActivity extends AppCompatActivity {
    VideoView videos;

    private ExoPlayer player;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main); // asegúrate que el XML se llama así

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
            ExoPlayer player = new ExoPlayer.Builder(this).build();
            PlayerView playerView = findViewById(R.id.playerView);
            playerView.setPlayer(player);

            MediaBrowser.MediaItem mediaItem = MediaItem.fromUri("android.resource://" + getPackageName() + "/" + R.raw.fondo);
            player.setMediaItem(mediaItem);
            player.setRepeatMode(Player.REPEAT_MODE_ONE);
            player.prepare();
            player.play();
        });

        videos = findViewById(R.id.videoBackground);

        // URI del video en res/raw
        Uri uri = Uri.parse("android.resource://" + getPackageName() + "/" + R.raw.fondo);

        videos.setVideoURI(uri);
        videos.start();

        // Loop infinito
        videos.setOnCompletionListener(new MediaPlayer.OnCompletionListener() {
            @Override
            public void onCompletion(MediaPlayer mp) {
                mp.start();
            }
        });

        // Botón para ir a SecondActivity
        Button btnSiguiente = findViewById(R.id.btn);
        btnSiguiente.setOnClickListener(v -> {
            Intent intent = new Intent(MainActivity.this, SecondActivity.class);
            startActivity(intent);
        });
        videos.setOnPreparedListener(new MediaPlayer.OnPreparedListener() {
            @Override
            public void onPrepared(MediaPlayer mp) {
                mp.setLooping(true); // para repetir

                // Obtener dimensiones del video
                int videoWidth = mp.getVideoWidth();
                int videoHeight = mp.getVideoHeight();

                // Obtener dimensiones de la pantalla (VideoView)
                int screenWidth = videos.getWidth();
                int screenHeight = videos.getHeight();

                float videoProportion = (float) videoWidth / videoHeight;
                float screenProportion = (float) screenWidth / screenHeight;

                android.view.ViewGroup.LayoutParams lp = videos.getLayoutParams();

                if (videoProportion > screenProportion) {
                    // Video más ancho que la pantalla
                    lp.width = screenWidth;
                    lp.height = (int) (screenWidth / videoProportion);
                } else {
                    // Video más alto que la pantalla
                    lp.width = (int) (screenHeight * videoProportion);
                    lp.height = screenHeight;
                }

                videos.setLayoutParams(lp);
            }
        });

    }

}