package com.example.prioyecto_final;

import android.os.Bundle;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.fragment.app.Fragment;

import com.google.android.material.bottomnavigation.BottomNavigationView;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_main);

        BottomNavigationView nav = findViewById(R.id.menu);

        nav.setOnItemSelectedListener(item -> {

            Fragment f;

            if(item.getItemId()==R.id.feed)
                f = new FeedFragment();
            else if(item.getItemId()==R.id.perfil)
                f = new PerfilFragment();
            else
                f = new CuentaFragment();

            getSupportFragmentManager()
                    .beginTransaction()
                    .replace(R.id.container, f)
                    .commit();

            return true;
        });
    }
}
