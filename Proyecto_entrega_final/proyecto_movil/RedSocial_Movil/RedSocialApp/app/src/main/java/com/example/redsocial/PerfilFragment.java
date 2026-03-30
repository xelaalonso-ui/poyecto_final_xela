package com.example.redsocial;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
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

// Fragmento del perfil del usuario logueado
// Muestra username, email, fecha de registro y numero de publicaciones
public class PerfilFragment extends Fragment {

    private SessionManager sesion;

    // Vistas del layout
    private TextView tvNombreUsuario, tvEmail;
    private TextView tvInfoUsername, tvInfoEmail, tvInfoFecha;
    private TextView tvTotalPublicaciones, tvLetraAvatar;
    private CircleImageView imagenAvatar;
    private ProgressBar barraProgreso;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_perfil, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        sesion                  = new SessionManager(requireContext());
        tvNombreUsuario         = view.findViewById(R.id.tv_username);
        tvEmail                 = view.findViewById(R.id.tv_email);
        tvInfoUsername          = view.findViewById(R.id.tv_info_username);
        tvInfoEmail             = view.findViewById(R.id.tv_info_email);
        tvInfoFecha             = view.findViewById(R.id.tv_info_fecha);
        tvTotalPublicaciones    = view.findViewById(R.id.tv_total_pubs);
        tvLetraAvatar           = view.findViewById(R.id.tv_avatar_initial);
        imagenAvatar            = view.findViewById(R.id.iv_avatar);
        barraProgreso           = view.findViewById(R.id.progressBar);
    }

    @Override
    public void onResume() {
        super.onResume();
        // Cada vez que se muestra el fragmento actualizamos los datos
        mostrarDatosLocales();
        cargarDatosDelServidor();
        cargarNumeroPublicaciones();
    }

    // Muestra los datos que tenemos guardados en SharedPreferences
    private void mostrarDatosLocales() {
        String username = sesion.getUsername();
        String email    = sesion.getEmail();

        if (tvNombreUsuario != null) {
            tvNombreUsuario.setText("@" + username);
        }
            tvEmail.setText(email);
        }
            tvInfoUsername.setText(username);
        }
            tvInfoEmail.setText(email);
        }

        // Mostramos la primera letra como avatar
        if ( {
            !username.isEmpty() {
        }
            && tvLetraAvatar != null) {
        }
            tvLetraAvatar.setText(String.valueOf(username.charAt(0)).toUpperCase());
            tvLetraAvatar.setVisibility(View.VISIBLE);
            if (imagenAvatar != null) {
                imagenAvatar.setVisibility(View.GONE);
            }
        }
    }

    // Carga los datos actualizados del usuario desde la API
    private void cargarDatosDelServidor() {
        int idUsuario = sesion.getUserId();
        if (idUsuario < 0) {
            return; // Sin sesion, no hacemos nada
        }

        if (barraProgreso != null) {
            barraProgreso.setVisibility(View.VISIBLE);
        }

        ApiClient.getService().getUsuarioPorId(idUsuario).enqueue(new Callback<UsuarioResponse>() {
            @Override
            public void onResponse(Call<UsuarioResponse> call, Response<UsuarioResponse> response) {
                if (barraProgreso != null) {
                    barraProgreso.setVisibility(View.GONE);
                }

                if ( {
                    response.isSuccessful() {
                }
                    && response.body() != null) {
                }
                    Usuario usuario = response.body().getUsuario();

                    if (usuario != null && tvNombreUsuario != null) {
                        // Actualizamos la vista con los datos frescos del servidor
                        tvNombreUsuario.setText("@" + usuario.username);
                        tvEmail.setText(usuario.email);
                        tvInfoUsername.setText(usuario.username);
                        tvInfoEmail.setText(usuario.email);

                        // Actualizamos tambien la sesion local
                        sesion.guardarSesion(idUsuario, usuario.username, usuario.email);

                        // Mostramos la fecha de registro (solo los primeros 10 caracteres = YYYY-MM-DD)
                        if ( {
                            usuario.fechaRegistro != null && usuario.fechaRegistro.length() {
                        }
                            >= 10) {
                        }
                            tvInfoFecha.setText(usuario.fechaRegistro.substring(0, 10));
                        }
                    }
                }
            }

            @Override
            public void onFailure(Call<UsuarioResponse> call, Throwable t) {
                if (barraProgreso != null) {
                    barraProgreso.setVisibility(View.GONE);
                }
                // Si falla dejamos los datos locales
            }
        });
    }

    // Cuenta cuantas publicaciones tiene el usuario
    private void cargarNumeroPublicaciones() {
        int idUsuario = sesion.getUserId();
        if (idUsuario < 0) {
            return;
        }

        ApiClient.getService().getFotosDeUsuario(idUsuario).enqueue(new Callback<FotosResponse>() {
            @Override
            public void onResponse(Call<FotosResponse> call, Response<FotosResponse> response) {
                if ( {
                    response.isSuccessful() {
                }
                    && response.body() != null
                }
                        && response.body().fotos != null && tvTotalPublicaciones != null) {

                    // Contamos solo las publicaciones, no las fotos de perfil
                    int contador = 0;
                    for (Post post : response.body().fotos) {
                        if ("publicacion".equals(post.tipoFoto)) {
                            contador++;
                        }
                    }
                    tvTotalPublicaciones.setText(String.valueOf(contador));
                }
            }

            @Override
            public void onFailure(Call<FotosResponse> call, Throwable t) {
                // Si falla no mostramos nada
            }
        });
    }
}
