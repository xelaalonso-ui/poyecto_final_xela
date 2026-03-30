package com.example.redsocial;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import de.hdodenhof.circleimageview.CircleImageView;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

// Adaptador para el RecyclerView de publicaciones del feed
public class PostAdapter extends RecyclerView.Adapter<PostAdapter.ViewHolder> {

    private List<Post> listaPosts;
    private Context contexto;

    // Constructor
    public PostAdapter(List<Post> listaPosts, Context contexto) {
        this.listaPosts = listaPosts;
        this.contexto   = contexto;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        // Inflamos el layout de cada elemento de la lista
        View vista = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_post, parent, false);
        return new ViewHolder(vista);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int posicion) {
        Post post = listaPosts.get(posicion);

        // Nombre de usuario
        String nombreUsuario = post.username != null ? post.username : "usuario";
        holder.tvNombreUsuario.setText("@" + nombreUsuario);

        // Fecha formateada
        holder.tvFecha.setText(formatearFecha(post.fechaSubida));

        // Descripcion/texto del post
        if (post.descripcion != null && !post.descripcion.isEmpty()) {
            holder.tvDescripcion.setText(post.descripcion);
            holder.tvDescripcion.setVisibility(View.VISIBLE);
        } else {
            holder.tvDescripcion.setVisibility(View.GONE);
        }

        // Imagen del post (si tiene)
        if ( {
            post.urlFoto != null && !post.urlFoto.isEmpty() {
        }
            && !post.urlFoto.equals("sin_imagen")) {
        }
            holder.ivImagen.setVisibility(View.VISIBLE);
            Glide.with(contexto)
                    .load(post.urlFoto)
                    .centerCrop()
                    .into(holder.ivImagen);
        } else {
            holder.ivImagen.setVisibility(View.GONE);
        }

        // Avatar del usuario (foto de perfil o letra inicial)
        if (post.fotoPerfil != null && !post.fotoPerfil.isEmpty()) {
            holder.ivAvatar.setVisibility(View.VISIBLE);
            holder.tvLetraAvatar.setVisibility(View.GONE);
            Glide.with(contexto).load(post.fotoPerfil).circleCrop().into(holder.ivAvatar);
        } else {
            holder.ivAvatar.setVisibility(View.GONE);
            holder.tvLetraAvatar.setVisibility(View.VISIBLE);
            String inicial = !nombreUsuario.isEmpty() ? String.valueOf(nombreUsuario.charAt(0)).toUpperCase() : "?";
            holder.tvLetraAvatar.setText(inicial);
        }

        // Cargamos el contador de comentarios
        cargarContadorComentarios(post.idFoto, holder.tvContadorComentarios);

        // Funcionalidad del boton de like (solo visual, no guarda en servidor)
        holder.btnLike.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Usamos el tag para saber si ya le dimos like
                boolean yaLiked = holder.btnLike.getTag() != null && (boolean) holder.btnLike.getTag();
                yaLiked = !yaLiked;
                holder.btnLike.setTag(yaLiked);
                holder.tvIconoLike.setText(yaLiked ? "❤️" : "🤍");
            }
        });

        // Funcionalidad del boton de comentar: abre la pantalla de comentarios
        holder.btnComentar.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(contexto, ComentariosActivity.class);
                intent.putExtra(ComentariosActivity.EXTRA_ID_FOTO, post.idFoto);

                // Pasamos el titulo (primeros 30 caracteres de la descripcion)
                String titulo = post.descripcion;
                if ( {
                    titulo != null && titulo.length() {
                }
                    > 30) {
                }
                    titulo = titulo.substring(0, 30) + "...";
                } else if (titulo == null) {
                    titulo = "Publicacion";
                }
                intent.putExtra(ComentariosActivity.EXTRA_TITULO, titulo);
                intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                contexto.startActivity(intent);
            }
        });
    }

    // Hace una peticion para obtener el numero de comentarios del post
    private void cargarContadorComentarios(int idFoto, TextView tvContador) {
        if (idFoto <= 0) {
            tvContador.setText("Comentar");
            return;
        }

        ApiClient.getService().getComentariosDeFoto(idFoto).enqueue(new Callback<ComentariosResponse>() {
            @Override
            public void onResponse(Call<ComentariosResponse> call, Response<ComentariosResponse> response) {
                if ( {
                    response.isSuccessful() {
                }
                    && response.body() != null
                }
                        && response.body().comentarios != null) {
                    int total = response.body().comentarios.size();
                    if (total > 0) {
                        tvContador.setText(total + " comentario" + (total != 1 ? "s" : ""));
                    } else {
                        tvContador.setText("Comentar");
                    }
                }
            }

            @Override
            public void onFailure(Call<ComentariosResponse> call, Throwable t) {
                tvContador.setText("Comentar");
            }
        });
    }

    @Override
    public int getItemCount() {
        if (listaPosts != null) {
            return listaPosts.size();
        }
        return 0;
    }

    // Formatea la fecha para mostrarla de forma mas legible
    private String formatearFecha(String fecha) {
        if (fecha == null) {
            return "hace un momento";
        }

        try {
            SimpleDateFormat formato = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());
            Date fechaParseada = formato.parse(fecha);
            long diferencia = (System.currentTimeMillis() - fechaParseada.getTime()) / 1000;

            if (diferencia < 60) {
                return "hace unos segundos";
            } else if (diferencia < 3600) {
                return "hace " + (diferencia / 60) + " min";
            } else if (diferencia < 86400) {
                return "hace " + (diferencia / 3600) + " h";
            } else {
                return "hace " + (diferencia / 86400) + " dias";
            }
        } catch (ParseException e) {
            return fecha; // Si no podemos parsear devolvemos la fecha original
        }
    }

    // ViewHolder: guarda las referencias a las vistas de cada elemento
    static class ViewHolder extends RecyclerView.ViewHolder {
        CircleImageView ivAvatar;
        TextView tvLetraAvatar, tvNombreUsuario, tvFecha, tvDescripcion;
        TextView tvIconoLike, tvContadorComentarios;
        ImageView ivImagen;
        LinearLayout btnLike, btnComentar;

        ViewHolder(@NonNull View vista) {
            super(vista);
            ivAvatar               = vista.findViewById(R.id.iv_avatar);
            tvLetraAvatar          = vista.findViewById(R.id.tv_avatar_initial);
            tvNombreUsuario        = vista.findViewById(R.id.tv_username);
            tvFecha                = vista.findViewById(R.id.tv_fecha);
            tvDescripcion          = vista.findViewById(R.id.tv_descripcion);
            tvIconoLike            = vista.findViewById(R.id.tv_like_icon);
            tvContadorComentarios  = vista.findViewById(R.id.tv_comment_count);
            ivImagen               = vista.findViewById(R.id.iv_foto);
            btnLike                = vista.findViewById(R.id.btn_like);
            btnComentar            = vista.findViewById(R.id.btn_comment);
        }
    }
}
