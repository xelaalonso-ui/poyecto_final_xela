package com.example.redsocial;

import android.os.Bundle;

import androidx.appcompat.app.AppCompatActivity;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;

import com.google.android.material.bottomnavigation.BottomNavigationView;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        BottomNavigationView nav = findViewById(R.id.bottom_nav);


        if (savedInstanceState == null) {
            loadFragment(new FeedFragment(), false);
        }

        nav.setOnItemSelectedListener(item -> {
            Fragment fragment;
            int id = item.getItemId();

            if (id == R.id.nav_feed) {
                fragment = new FeedFragment();
            } else if (id == R.id.nav_perfil) {
                fragment = new PerfilFragment();
            } else {
                fragment = new CuentaFragment();
            }

            loadFragment(fragment, true);
            return true;
        });
    }

    private void loadFragment(Fragment fragment, boolean animate) {
        FragmentTransaction tx = getSupportFragmentManager()
                .beginTransaction()
                .replace(R.id.container, fragment);

        if (animate) {
            tx.setCustomAnimations(R.anim.fade_in, R.anim.fade_out);
        }

        tx.commit();
    }
}
