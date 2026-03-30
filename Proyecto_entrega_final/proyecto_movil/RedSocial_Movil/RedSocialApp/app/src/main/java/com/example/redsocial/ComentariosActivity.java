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

// Activity que muestra los comentarios de una publicacion
// y permite escribir nuevos comentarios
public class ComentariosActivity extends AppCompatActivity {

    // Constantes para los extras del Intent
    public static final String EXTRA_ID_FOTO = "id_foto";
    public static final String EXTRA_TITULO  = "titulo_post";

    private RecyclerView listaComentarios;
    private ProgressBar barraProgreso;
    private EditText campoComentario;
    private TextView btnEnviar, tvContador;

    private List<Comentario> listaData = new ArrayList<>();
    private AdaptadorComentarios adaptador;
    private SessionManager sesion;
    private int idFoto;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_comentarios);

        sesion        = new SessionManager(this);
        idFoto        = getIntent().getIntExtra(EXTRA_ID_FOTO, -1);
        String titulo = getIntent().getStringExtra(EXTRA_TITULO);

        // Enlazamos vistas
        listaComentarios = findViewById(R.id.recycler_comentarios);
        barraProgreso    = findViewById(R.id.progressBar);
        campoComentario  = findViewById(R.id.et_comentario);
        btnEnviar        = findViewById(R.id.btn_enviar);
        tvContador       = findViewById(R.id.tv_contador);

        // Mostramos el titulo del post
        TextView tvTitulo = findViewById(R.id.tv_titulo);
        if (titulo != null) {
            tvTitulo.setText(titulo);
        }

        // Boton para volver atras
        findViewById(R.id.btn_back).setOnClickListener(v -> finish());

        // Configuramos el RecyclerView
        listaComentarios.setLayoutManager(new LinearLayoutManager(this));
        adaptador = new AdaptadorComentarios(listaData, sesion.getUserId(), idComentario -> {
            eliminarComentario(idComentario);
        });
        listaComentarios.setAdapter(adaptador);

        // Boton enviar comentario
        btnEnviar.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String texto = campoComentario.getText().toString().trim();
                if (TextUtils.isEmpty(texto)) {
                    Toast.makeText(ComentariosActivity.this, "Escribe algo primero", Toast.LENGTH_SHORT).show();
                    return;
                }
                enviarComentario(texto);
            }
        });

        // Cargamos los comentarios si tenemos un ID valido
        if (idFoto > 0) {
            cargarComentarios();
        }
    }

    // Actualiza el contador de comentarios en la cabecera
    private void actualizarContador() {
        if (tvContador != null) {
            int numComentarios = listaData.size();
            String texto = numComentarios + " comentario" + (numComentarios != 1 ? "s" : "");
            tvContador.setText(texto);
        }
    }

    // Carga los comentarios de la publicacion desde la API
    private void cargarComentarios() {
        barraProgreso.setVisibility(View.VISIBLE);

        ApiClient.getService().getComentariosDeFoto(idFoto).enqueue(new Callback<ComentariosResponse>() {
            @Override
            public void onResponse(Call<ComentariosResponse> call, Response<ComentariosResponse> response) {
                barraProgreso.setVisibility(View.GONE);

                if ( {
                    response.isSuccessful() {
                }
                    && response.body() != null
                }
                        && response.body().comentarios != null) {
                    listaData.clear();
                    listaData.addAll(response.body().comentarios);
                    adaptador.notifyDataSetChanged();
                    actualizarContador();
                }
            }

            @Override
            public void onFailure(Call<ComentariosResponse> call, Throwable t) {
                barraProgreso.setVisibility(View.GONE);
                Toast.makeText(ComentariosActivity.this, "Sin conexion", Toast.LENGTH_SHORT).show();
            }
        });
    }

    // Envia un nuevo comentario a la API
    private void enviarComentario(String texto) {
        btnEnviar.setEnabled(false);

        Map<String, String> datos = new HashMap<>();
        datos.put("id_foto",    String.valueOf(idFoto));
        datos.put("id_usuario", String.valueOf(sesion.getUserId()));
        datos.put("contenido",  texto);

        ApiClient.getService().crearComentario(datos).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                btnEnviar.setEnabled(true);

                if (response.isSuccessful()) {
                    campoComentario.setText("");

                    // Creamos el objeto comentario y lo aniadimos localmente
                    Comentario nuevo = new Comentario();
                    nuevo.texto     = texto;
                    nuevo.username  = sesion.getUsername();
                    nuevo.idUsuario = sesion.getUserId();

                    listaData.add(nuevo);
                    adaptador.notifyItemInserted(listaData.size() - 1);
                    listaComentarios.scrollToPosition(listaData.size() - 1);
                    actualizarContador();

                    Toast.makeText(ComentariosActivity.this, "Comentario enviado", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(ComentariosActivity.this, "Error al enviar: " + response.code(), Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                btnEnviar.setEnabled(true);
                Toast.makeText(ComentariosActivity.this, "Sin conexion", Toast.LENGTH_SHORT).show();
            }
        });
    }

    // Elimina un comentario de la API y de la lista
    private void eliminarComentario(int idComentario) {
        ApiClient.getService().eliminarComentario(idComentario).enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if (response.isSuccessful()) {
                    // Buscamos y eliminamos el comentario de la lista
                    for (int i = 0; i < listaData.size(); i++) {
                        if (listaData.get(i).idComentario == idComentario) {
                            listaData.remove(i);
                            adaptador.notifyItemRemoved(i);
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
                Toast.makeText(ComentariosActivity.this, "Sin conexion", Toast.LENGTH_SHORT).show();
            }
        });
    }

    // Adaptador para la lista de comentarios
    static class AdaptadorComentarios extends RecyclerView.Adapter<AdaptadorComentarios.ViewHolder> {

        // Interface para manejar el evento de eliminar
        interface ListenerEliminar {
            void alEliminar(int idComentario);
        }

        private List<Comentario> lista;
        private int miIdUsuario;
        private ListenerEliminar listener;

        AdaptadorComentarios(List<Comentario> lista, int miIdUsuario, ListenerEliminar listener) {
            this.lista       = lista;
            this.miIdUsuario = miIdUsuario;
            this.listener    = listener;
        }

        @Override
        public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
            View v = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.item_comentario, parent, false);
            return new ViewHolder(v);
        }

        @Override
        public void onBindViewHolder(ViewHolder holder, int position) {
            Comentario comentario = lista.get(position);

            String usuario = comentario.username != null ? comentario.username : "usuario";
            holder.tvNombreUsuario.setText("@" + usuario);
            holder.tvTexto.setText(comentario.texto);

            // Mostramos solo la parte de la fecha (YYYY-MM-DD)
            if ( {
                comentario.fecha != null && comentario.fecha.length() {
            }
                >= 10) {
            }
                holder.tvFecha.setText(comentario.fecha.substring(0, 10));
            } else {
                holder.tvFecha.setText("");
            }

            // Solo mostramos el boton eliminar si es el autor del comentario
            if (comentario.idUsuario == miIdUsuario && comentario.idComentario > 0) {
                holder.btnEliminar.setVisibility(View.VISIBLE);
                holder.btnEliminar.setOnClickListener(v -> listener.alEliminar(comentario.idComentario));
            } else {
                holder.btnEliminar.setVisibility(View.GONE);
            }
        }

        @Override
        public int getItemCount() {
            return lista.size();
        }

        // ViewHolder del comentario
        static class ViewHolder extends RecyclerView.ViewHolder {
            TextView tvNombreUsuario, tvTexto, tvFecha, btnEliminar;

            ViewHolder(View v) {
                super(v);
                tvNombreUsuario = v.findViewById(R.id.tv_username);
                tvTexto         = v.findViewById(R.id.tv_texto);
                tvFecha         = v.findViewById(R.id.tv_fecha);
                btnEliminar     = v.findViewById(R.id.btn_eliminar);
            }
        }
    }
}
