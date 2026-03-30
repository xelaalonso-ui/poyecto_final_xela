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

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ComentariosActivity extends AppCompatActivity {

    public static final String EXTRA_ID_FOTO = "id_foto";
    public static final String EXTRA_TITULO  = "titulo_post";

    private RecyclerView recycler;
    private ProgressBar progressBar;
    private EditText etComentario;
    private TextView btnEnviar;
    private TextView tvContador;
    private final List<Comentario> lista = new ArrayList<>();
    private ComentarioAdapter adapter;
    private SessionManager session;
    private int idFoto;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_comentarios);

        session      = new SessionManager(this);
        idFoto       = getIntent().getIntExtra(EXTRA_ID_FOTO, -1);
        String titulo = getIntent().getStringExtra(EXTRA_TITULO);

        recycler     = findViewById(R.id.recycler_comentarios);
        progressBar  = findViewById(R.id.progressBar);
        etComentario = findViewById(R.id.et_comentario);
        btnEnviar    = findViewById(R.id.btn_enviar);
        tvContador   = findViewById(R.id.tv_contador);

        TextView tvTitulo = findViewById(R.id.tv_titulo);
        if (titulo != null) tvTitulo.setText(titulo);

        findViewById(R.id.btn_back).setOnClickListener(v -> finish());

        recycler.setLayoutManager(new LinearLayoutManager(this));
        adapter = new ComentarioAdapter(lista, session.getUserId(), idComentario -> eliminarComentario(idComentario));
        recycler.setAdapter(adapter);

        btnEnviar.setOnClickListener(v -> {
            String texto = etComentario.getText().toString().trim();
            if (TextUtils.isEmpty(texto)) {
                Toast.makeText(this, "Escribe un comentario", Toast.LENGTH_SHORT).show();
                return;
            }
            enviarComentario(texto);
        });

        if (idFoto > 0) cargarComentarios();
    }

    private void actualizarContador() {
        if (tvContador != null) {
            int n = lista.size();
            tvContador.setText(n + " comentario" + (n != 1 ? "s" : ""));
        }
    }

    private void cargarComentarios() {
        progressBar.setVisibility(View.VISIBLE);
        ApiClient.getService().getComentarios(idFoto).enqueue(new Callback<ComentariosResponse>() {
            @Override
            public void onResponse(Call<ComentariosResponse> call, Response<ComentariosResponse> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null
                        && response.body().comentarios != null) {
                    lista.clear();
                    lista.addAll(response.body().comentarios);
                    adapter.notifyDataSetChanged();
                    actualizarContador();
                }
            }
            @Override
            public void onFailure(Call<ComentariosResponse> call, Throwable t) {
                progressBar.setVisibility(View.GONE);
                Toast.makeText(ComentariosActivity.this, "Sin conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void enviarComentario(String texto) {
        btnEnviar.setEnabled(false);
        Map<String, String> body = new HashMap<>();
        body.put("id_foto",    String.valueOf(idFoto));
        body.put("id_usuario", String.valueOf(session.getUserId()));
        body.put("contenido",  texto);

        ApiClient.getService().createComentario(body).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                btnEnviar.setEnabled(true);
                if (response.isSuccessful()) {
                    etComentario.setText("");
                    Comentario c = new Comentario();
                    c.texto     = texto;
                    c.username  = session.getUsername();
                    c.idUsuario = session.getUserId();
                    lista.add(c);
                    adapter.notifyItemInserted(lista.size() - 1);
                    recycler.scrollToPosition(lista.size() - 1);
                    actualizarContador();
                    Toast.makeText(ComentariosActivity.this, "Comentario enviado 💬", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(ComentariosActivity.this, "Error al comentar (" + response.code() + ")", Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                btnEnviar.setEnabled(true);
                Toast.makeText(ComentariosActivity.this, "Sin conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void eliminarComentario(int idComentario) {
        ApiClient.getService().deleteComentario(idComentario).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if (response.isSuccessful()) {
                    for (int i = 0; i < lista.size(); i++) {
                        if (lista.get(i).idComentario == idComentario) {
                            lista.remove(i);
                            adapter.notifyItemRemoved(i);
                            break;
                        }
                    }
                    actualizarContador();
                    Toast.makeText(ComentariosActivity.this, "Comentario eliminado", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(ComentariosActivity.this, "No se pudo eliminar", Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                Toast.makeText(ComentariosActivity.this, "Sin conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    // Adapter interno
    static class ComentarioAdapter extends RecyclerView.Adapter<ComentarioAdapter.VH> {
        interface OnDeleteListener { void onDelete(int idComentario); }

        private final List<Comentario> lista;
        private final int miId;
        private final OnDeleteListener listener;

        ComentarioAdapter(List<Comentario> lista, int miId, OnDeleteListener listener) {
            this.lista    = lista;
            this.miId     = miId;
            this.listener = listener;
        }

        @Override
        public VH onCreateViewHolder(ViewGroup parent, int viewType) {
            View v = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.item_comentario, parent, false);
            return new VH(v);
        }

        @Override
        public void onBindViewHolder(VH h, int position) {
            Comentario c = lista.get(position);
            h.tvUsername.setText("@" + (c.username != null ? c.username : "usuario"));
            h.tvTexto.setText(c.texto);
            h.tvFecha.setText(c.fecha != null ? c.fecha.substring(0, Math.min(10, c.fecha.length())) : "");

            if (c.idUsuario == miId && c.idComentario > 0) {
                h.btnEliminar.setVisibility(View.VISIBLE);
                h.btnEliminar.setOnClickListener(v -> listener.onDelete(c.idComentario));
            } else {
                h.btnEliminar.setVisibility(View.GONE);
            }
        }

        @Override
        public int getItemCount() { return lista.size(); }

        static class VH extends RecyclerView.ViewHolder {
            TextView tvUsername, tvTexto, tvFecha, btnEliminar;
            VH(View v) {
                super(v);
                tvUsername  = v.findViewById(R.id.tv_username);
                tvTexto     = v.findViewById(R.id.tv_texto);
                tvFecha     = v.findViewById(R.id.tv_fecha);
                btnEliminar = v.findViewById(R.id.btn_eliminar);
            }
        }
    }
}
