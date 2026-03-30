package com.example.redsocial;

import android.animation.ObjectAnimator;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.DecelerateInterpolator;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.fragment.app.Fragment;

public class CuentaFragment extends Fragment {

    private SessionManager session;
    private TextView tvUsername, tvEmail, tvAvatar;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_cuenta, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        session    = new SessionManager(requireContext());
        tvUsername = view.findViewById(R.id.tv_username);
        tvEmail    = view.findViewById(R.id.tv_email);
        tvAvatar   = view.findViewById(R.id.tv_avatar);

        actualizarUI();

        view.setAlpha(0f);
        view.animate().alpha(1f).setDuration(400)
                .setInterpolator(new DecelerateInterpolator()).start();

        // Botón editar cuenta
        view.findViewById(R.id.btn_editar).setOnClickListener(v -> {
            animateClick(v);
            Intent intent = new Intent(requireContext(), EditarCuentaActivity.class);
            startActivityForResult(intent, 100);
            requireActivity().overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
        });

        // Botón logout
        view.findViewById(R.id.btn_logout).setOnClickListener(v -> {
            animateClick(v);
            new AlertDialog.Builder(requireContext())
                    .setTitle("🚪 Cerrar sesión")
                    .setMessage("¿Seguro que quieres salir?")
                    .setPositiveButton("Sí, salir", (dialog, which) -> {
                        session.logout();
                        Intent intent = new Intent(requireContext(), LoginActivity.class);
                        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                        startActivity(intent);
                        requireActivity().overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                    })
                    .setNegativeButton("Cancelar", null)
                    .show();
        });
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == 100 && resultCode == -1) {
            actualizarUI();
        }
    }

    private void actualizarUI() {
        String username = session.getUsername();
        String email    = session.getEmail();
        tvUsername.setText("@" + username);
        tvEmail.setText(email);
        if (!username.isEmpty()) {
            tvAvatar.setText(String.valueOf(username.charAt(0)).toUpperCase());
        }
    }

    private void animateClick(View v) {
        ObjectAnimator.ofFloat(v, View.SCALE_X, 1f, 0.95f, 1f).setDuration(200).start();
        ObjectAnimator.ofFloat(v, View.SCALE_Y, 1f, 0.95f, 1f).setDuration(200).start();
    }
}
