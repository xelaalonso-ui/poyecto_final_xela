package com.example.redsocial;

import android.os.Bundle;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

// Fragmento del feed: muestra todas las publicaciones y permite crear nuevas
public class FeedFragment extends Fragment {

    private RecyclerView listaPublicaciones;
    private ProgressBar barraProgreso;
    private EditText campoTextoPublicar;
    private TextView btnPublicar;

    // Lista donde guardamos los posts y el adaptador del RecyclerView
    private List<Post> listaPosts = new ArrayList<>();
    private PostAdapter adaptador;

    private SessionManager sesion;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater,
                             @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_feed, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        sesion               = new SessionManager(requireContext());
        listaPublicaciones   = view.findViewById(R.id.recycler);
        barraProgreso        = view.findViewById(R.id.progressBar);
        campoTextoPublicar   = view.findViewById(R.id.et_publicar);
        btnPublicar          = view.findViewById(R.id.btn_publicar);

        // Configuramos el RecyclerView con layout vertical
        listaPublicaciones.setLayoutManager(new LinearLayoutManager(getContext()));
        adaptador = new PostAdapter(listaPosts, requireContext());
        listaPublicaciones.setAdapter(adaptador);

        // Listener del boton publicar
        btnPublicar.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String texto = campoTextoPublicar.getText().toString().trim();
                if (TextUtils.isEmpty(texto)) {
                    Toast.makeText(getContext(), "Escribe algo antes de publicar", Toast.LENGTH_SHORT).show();
                    return;
                }
                publicarTexto(texto);
            }
        });

        cargarPublicaciones();
    }

    @Override
    public void onResume() {
        super.onResume();
        // Recargamos cada vez que volvemos al fragmento
        cargarPublicaciones();
    }

    // Carga todas las publicaciones desde la API
    private void cargarPublicaciones() {
        barraProgreso.setVisibility(View.VISIBLE);

        ApiClient.getService().getFotos().enqueue(new Callback<FotosResponse>() {
            @Override
            public void onResponse(Call<FotosResponse> call, Response<FotosResponse> response) {
                if (!isAdded()) {
                    return; // Comprobamos que el fragmento sigue activo
                }

                barraProgreso.setVisibility(View.GONE);

                if ( {
                    response.isSuccessful() {
                }
                    && response.body() != null
                }
                        && response.body().fotos != null) {

                    listaPosts.clear();

                    // Solo mostramos las publicaciones, no las fotos de perfil
                    for (Post post : response.body().fotos) {
                        if (!"perfil".equals(post.tipoFoto)) {
                            listaPosts.add(post);
                        }
                    }

                    adaptador.notifyDataSetChanged();

                    if (listaPosts.isEmpty()) {
                        Toast.makeText(getContext(), "No hay publicaciones todavia", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(getContext(), "Error al cargar publicaciones", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<FotosResponse> call, Throwable t) {
                if (!isAdded()) {
                    return;
                }
                barraProgreso.setVisibility(View.GONE);
                Toast.makeText(getContext(), "Sin conexion al servidor", Toast.LENGTH_SHORT).show();
            }
        });
    }

    // Crea una nueva publicacion de texto en la API
    private void publicarTexto(String texto) {
        btnPublicar.setEnabled(false);

        // Creamos el mapa con los datos de la publicacion
        Map<String, String> datos = new HashMap<>();
        datos.put("id_usuario", String.valueOf(sesion.getUserId()));
        datos.put("url_foto", "sin_imagen"); // No hay imagen, solo texto
        datos.put("descripcion", texto);
        datos.put("tipo_foto", "publicacion");

        ApiClient.getService().crearFoto(datos).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if (!isAdded()) {
                    return;
                }

                btnPublicar.setEnabled(true);

                if (response.isSuccessful()) {
                    // Limpiamos el campo y recargamos el feed
                    campoTextoPublicar.setText("");
                    Toast.makeText(getContext(), "Publicado correctamente", Toast.LENGTH_SHORT).show();
                    cargarPublicaciones();
                } else {
                    Toast.makeText(getContext(), "Error al publicar: " + response.code(), Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                if (!isAdded()) {
                    return;
                }
                btnPublicar.setEnabled(true);
                Toast.makeText(getContext(), "Sin conexion al servidor", Toast.LENGTH_SHORT).show();
            }
        });
    }
}
