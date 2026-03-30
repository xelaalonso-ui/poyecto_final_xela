package com.example.redsocial;

import android.os.Bundle;

import androidx.appcompat.app.AppCompatActivity;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;

import com.google.android.material.bottomnavigation.BottomNavigationView;

// Activity principal con la barra de navegacion inferior
// Contiene tres fragmentos: Feed, Perfil y Cuenta
public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        BottomNavigationView navegacion = findViewById(R.id.bottom_nav);

        // Cargamos el feed por defecto al abrir la app
        if (savedInstanceState == null) {
            cargarFragmento(new FeedFragment(), false);
        }

        // Cuando el usuario pulsa en la barra de navegacion
        navegacion.setOnItemSelectedListener(item -> {
            Fragment fragmentoSeleccionado;
            int idItem = item.getItemId();

            if (idItem == R.id.nav_feed) {
                fragmentoSeleccionado = new FeedFragment();
            } else if (idItem == R.id.nav_perfil) {
                fragmentoSeleccionado = new PerfilFragment();
            } else {
                // El tercer item es la cuenta
                fragmentoSeleccionado = new CuentaFragment();
            }

            cargarFragmento(fragmentoSeleccionado, true);
            return true;
        });
    }

    // Metodo auxiliar para mostrar un fragmento en el contenedor
    private void cargarFragmento(Fragment fragmento, boolean conAnimacion) {
        FragmentTransaction transaccion = getSupportFragmentManager()
                .beginTransaction()
                .replace(R.id.container, fragmento);

        // Solo animamos si no es la carga inicial
        if (conAnimacion) {
            transaccion.setCustomAnimations(R.anim.fade_in, R.anim.fade_out);
        }

        transaccion.commit();
    }
}
