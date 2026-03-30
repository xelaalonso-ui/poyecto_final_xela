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

public class FeedFragment extends Fragment {

    private RecyclerView recycler;
    private ProgressBar progressBar;
    private EditText etPublicar;
    private TextView btnPublicar;
    private final List<Post> posts = new ArrayList<>();
    private PostAdapter adapter;
    private SessionManager session;

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

        session     = new SessionManager(requireContext());
        recycler    = view.findViewById(R.id.recycler);
        progressBar = view.findViewById(R.id.progressBar);
        etPublicar  = view.findViewById(R.id.et_publicar);
        btnPublicar = view.findViewById(R.id.btn_publicar);

        recycler.setLayoutManager(new LinearLayoutManager(getContext()));
        adapter = new PostAdapter(posts, requireContext());
        recycler.setAdapter(adapter);

        btnPublicar.setOnClickListener(v -> {
            String texto = etPublicar.getText().toString().trim();
            if (TextUtils.isEmpty(texto)) {
                Toast.makeText(getContext(), "Escribe algo primero 💬", Toast.LENGTH_SHORT).show();
                return;
            }
            publicar(texto);
        });

        loadPosts();
    }


    @Override
    public void onResume() {
        super.onResume();
        loadPosts();
    }

    private void loadPosts() {
        progressBar.setVisibility(View.VISIBLE);

        ApiClient.getService().getFotos().enqueue(new Callback<FotosResponse>() {
            @Override
            public void onResponse(Call<FotosResponse> call, Response<FotosResponse> response) {
                if (!isAdded()) return;
                progressBar.setVisibility(View.GONE);

                if (response.isSuccessful() && response.body() != null
                        && response.body().fotos != null) {
                    posts.clear();
                    for (Post p : response.body().fotos) {
                        if (!"perfil".equals(p.tipoFoto)) {
                            posts.add(p);
                        }
                    }
                    adapter.notifyDataSetChanged();

                    if (posts.isEmpty()) {
                        Toast.makeText(getContext(),
                                "Sé el primero en publicar algo 🌸",
                                Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(getContext(),
                            "Error cargando publicaciones: " + response.code(),
                            Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<FotosResponse> call, Throwable t) {
                if (!isAdded()) {
                    return;
                }
                progressBar.setVisibility(View.GONE);
                Toast.makeText(getContext(), "Sin conexión al servidor", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void publicar(String texto) {
        btnPublicar.setEnabled(false);
        btnPublicar.setAlpha(0.6f);

        Map<String, String> body = new HashMap<>();
        body.put("id_usuario", String.valueOf(session.getUserId()));
        body.put("url_foto",   "sin_imagen");
        body.put("descripcion", texto);
        body.put("tipo_foto",  "publicacion");

        ApiClient.getService().createFoto(body).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if (!isAdded()) {
                    return;
                }
                btnPublicar.setEnabled(true);
                btnPublicar.setAlpha(1f);

                if (response.isSuccessful()) {
                    etPublicar.setText("");
                    Toast.makeText(getContext(), "Publicado", Toast.LENGTH_SHORT).show();

                    loadPosts();
                } else {
                    Toast.makeText(getContext(),
                            "Error al publicar (" + response.code() + ")",
                            Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                if (!isAdded()) {
                    return;
                }
                btnPublicar.setEnabled(true);
                btnPublicar.setAlpha(1f);
                Toast.makeText(getContext(), "Sin conexión al servidor", Toast.LENGTH_SHORT).show();
            }
        });

    }
}
