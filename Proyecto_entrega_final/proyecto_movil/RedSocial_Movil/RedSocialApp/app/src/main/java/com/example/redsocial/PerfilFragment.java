package com.example.redsocial;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.DecelerateInterpolator;
import android.widget.ProgressBar;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;

import com.bumptech.glide.Glide;

import de.hdodenhof.circleimageview.CircleImageView;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class PerfilFragment extends Fragment {

    private SessionManager session;
    private TextView tvUsername, tvEmail, tvInfoUsername, tvInfoEmail, tvInfoFecha;
    private TextView tvTotalPubs, tvAvatarInitial;
    private CircleImageView ivAvatar;
    private ProgressBar progressBar;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_perfil, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        session         = new SessionManager(requireContext());
        tvUsername      = view.findViewById(R.id.tv_username);
        tvEmail         = view.findViewById(R.id.tv_email);
        tvInfoUsername  = view.findViewById(R.id.tv_info_username);
        tvInfoEmail     = view.findViewById(R.id.tv_info_email);
        tvInfoFecha     = view.findViewById(R.id.tv_info_fecha);
        tvTotalPubs     = view.findViewById(R.id.tv_total_pubs);
        tvAvatarInitial = view.findViewById(R.id.tv_avatar_initial);
        ivAvatar        = view.findViewById(R.id.iv_avatar);
        progressBar     = view.findViewById(R.id.progressBar);

        view.setAlpha(0f);
        view.animate().alpha(1f).setDuration(400)
                .setInterpolator(new DecelerateInterpolator()).start();
    }


    @Override
    public void onResume() {
        super.onResume();
        actualizarDatosLocales();
        cargarPerfil();
        cargarPublicaciones();
    }

    private void actualizarDatosLocales() {
        String username = session.getUsername();
        String email    = session.getEmail();
        if (tvUsername != null) tvUsername.setText("@" + username);
        if (tvEmail != null) tvEmail.setText(email);
        if (tvInfoUsername != null) tvInfoUsername.setText(username);
        if (tvInfoEmail != null) tvInfoEmail.setText(email);

        if (!username.isEmpty() && tvAvatarInitial != null) {
            tvAvatarInitial.setText(String.valueOf(username.charAt(0)).toUpperCase());
            tvAvatarInitial.setVisibility(View.VISIBLE);
            if (ivAvatar != null) ivAvatar.setVisibility(View.GONE);
        }
    }

    private void cargarPerfil() {
        int id = session.getUserId();
        if (id < 0 || progressBar == null) return;

        progressBar.setVisibility(View.VISIBLE);

        ApiClient.getService().getUsuario(id).enqueue(new Callback<UsuarioResponse>() {
            @Override
            public void onResponse(Call<UsuarioResponse> call, Response<UsuarioResponse> response) {
                if (progressBar != null) progressBar.setVisibility(View.GONE);

                if (response.isSuccessful() && response.body() != null) {
                    Usuario u = response.body().getUsuario();

                    if (u != null && tvUsername != null) {
                        tvUsername.setText("@" + u.username);
                        tvEmail.setText(u.email);
                        tvInfoUsername.setText(u.username);
                        tvInfoEmail.setText(u.email);


                        session.saveSession(id, u.username, u.email);

                        if (u.fechaRegistro != null && u.fechaRegistro.length() >= 10) {
                            tvInfoFecha.setText(u.fechaRegistro.substring(0, 10));
                        }
                    }
                }
            }

            @Override
            public void onFailure(Call<UsuarioResponse> call, Throwable t) {
                if (progressBar != null) progressBar.setVisibility(View.GONE);
            }
        });
    }

    private void cargarPublicaciones() {
        int id = session.getUserId();
        if (id < 0) return;

        ApiClient.getService().getFotosUsuario(id).enqueue(new Callback<FotosResponse>() {
            @Override
            public void onResponse(Call<FotosResponse> call, Response<FotosResponse> response) {
                if (response.isSuccessful() && response.body() != null
                        && response.body().fotos != null && tvTotalPubs != null) {

                    long count = response.body().fotos.stream()
                            .filter(p -> "publicacion".equals(p.tipoFoto))
                            .count();
                    tvTotalPubs.setText(String.valueOf(count));
                }
            }

            @Override
            public void onFailure(Call<FotosResponse> call, Throwable t) { }
        });
    }
}
