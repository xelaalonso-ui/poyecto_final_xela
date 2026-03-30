package com.example.redsocial;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.fragment.app.Fragment;

// Fragmento de la cuenta del usuario
// Muestra informacion basica y botones de editar y cerrar sesion
public class CuentaFragment extends Fragment {

    private SessionManager sesion;
    private TextView tvNombreUsuario, tvEmail, tvAvatar;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_cuenta, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        sesion          = new SessionManager(requireContext());
        tvNombreUsuario = view.findViewById(R.id.tv_username);
        tvEmail         = view.findViewById(R.id.tv_email);
        tvAvatar        = view.findViewById(R.id.tv_avatar);

        // Mostramos los datos actuales del usuario
        actualizarVista();

        // Boton para ir a la pantalla de editar cuenta
        view.findViewById(R.id.btn_editar).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(requireContext(), EditarCuentaActivity.class);
                startActivityForResult(intent, 100);
                requireActivity().overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
            }
        });

        // Boton para cerrar sesion con dialogo de confirmacion
        view.findViewById(R.id.btn_logout).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mostrarDialogoCerrarSesion();
            }
        });
    }

    // Se llama cuando volvemos de EditarCuentaActivity
    @Override
    public void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        // Si volvemos de editar con resultado OK actualizamos la vista
        if (requestCode == 100 && resultCode == -1) {
            actualizarVista();
        }
    }

    // Muestra los datos del usuario en la pantalla
    private void actualizarVista() {
        String nombreUsuario = sesion.getUsername();
        String email         = sesion.getEmail();

        tvNombreUsuario.setText("@" + nombreUsuario);
        tvEmail.setText(email);

        // Ponemos la primera letra del nombre como avatar
        if (!nombreUsuario.isEmpty()) {
            tvAvatar.setText(String.valueOf(nombreUsuario.charAt(0)).toUpperCase());
        }
    }

    // Dialogo para confirmar el cierre de sesion
    private void mostrarDialogoCerrarSesion() {
        new AlertDialog.Builder(requireContext())
                .setTitle("Cerrar sesion")
                .setMessage("¿Estas seguro de que quieres salir?")
                .setPositiveButton("Si, salir", (dialog, which) -> {
                    // Cerramos sesion y volvemos al login
                    sesion.cerrarSesion();
                    Intent intent = new Intent(requireContext(), LoginActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    startActivity(intent);
                    requireActivity().overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                })
                .setNegativeButton("Cancelar", null)
                .show();
    }
}
