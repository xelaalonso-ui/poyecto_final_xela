package com.example.redsocial;

import android.animation.ObjectAnimator;
import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.DecelerateInterpolator;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.resource.drawable.DrawableTransitionOptions;
import de.hdodenhof.circleimageview.CircleImageView;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class PostAdapter extends RecyclerView.Adapter<PostAdapter.VH> {

    private final List<Post> lista;
    private final Context context;

    public PostAdapter(List<Post> lista, Context context) {
        this.lista   = lista;
        this.context = context;
    }

    @NonNull
    @Override
    public VH onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_post, parent, false);
        return new VH(view);
    }

    @Override
    public void onBindViewHolder(@NonNull VH h, int position) {
        Post p = lista.get(position);

        h.tvUsername.setText("@" + (p.username != null ? p.username : "usuario"));
        h.tvFecha.setText(formatFecha(p.fechaSubida));

        if (p.descripcion != null && !p.descripcion.isEmpty()) {
            h.tvDescripcion.setText(p.descripcion);
            h.tvDescripcion.setVisibility(View.VISIBLE);
        } else {
            h.tvDescripcion.setVisibility(View.GONE);
        }

        // Ocultar imagen si es "sin_imagen" o vacío
        if (p.urlFoto != null && !p.urlFoto.isEmpty() && !p.urlFoto.equals("sin_imagen")) {
            h.ivFoto.setVisibility(View.VISIBLE);
            Glide.with(context)
                    .load(p.urlFoto)
                    .transition(DrawableTransitionOptions.withCrossFade())
                    .centerCrop()
                    .into(h.ivFoto);
        } else {
            h.ivFoto.setVisibility(View.GONE);
        }

        if (p.fotoPerfil != null && !p.fotoPerfil.isEmpty()) {
            h.ivAvatar.setVisibility(View.VISIBLE);
            h.tvAvatarInitial.setVisibility(View.GONE);
            Glide.with(context).load(p.fotoPerfil).circleCrop().into(h.ivAvatar);
        } else {
            h.ivAvatar.setVisibility(View.GONE);
            h.tvAvatarInitial.setVisibility(View.VISIBLE);
            String inicial = (p.username != null && !p.username.isEmpty())
                    ? String.valueOf(p.username.charAt(0)).toUpperCase() : "?";
            h.tvAvatarInitial.setText(inicial);
        }

        h.itemView.setAlpha(0f);
        h.itemView.setTranslationY(40f);
        h.itemView.animate().alpha(1f).translationY(0f).setDuration(400)
                .setStartDelay(position * 60L)
                .setInterpolator(new DecelerateInterpolator()).start();

        // Cargar contador de comentarios
        cargarContadorComentarios(p.idFoto, h.tvCommentCount);

        // Like
        h.btnLike.setOnClickListener(v -> {
            boolean liked = (boolean) (h.btnLike.getTag() != null ? h.btnLike.getTag() : false);
            liked = !liked;
            h.btnLike.setTag(liked);
            h.tvLikeIcon.setText(liked ? "❤️" : "🤍");
            ObjectAnimator scaleX = ObjectAnimator.ofFloat(h.tvLikeIcon, View.SCALE_X, 1f, 1.5f, 1f);
            ObjectAnimator scaleY = ObjectAnimator.ofFloat(h.tvLikeIcon, View.SCALE_Y, 1f, 1.5f, 1f);
            scaleX.setDuration(300); scaleY.setDuration(300);
            scaleX.start(); scaleY.start();
        });

        // Comentar
        h.btnComment.setOnClickListener(v -> {
            ObjectAnimator.ofFloat(v, View.SCALE_X, 1f, 0.92f, 1f).setDuration(200).start();
            ObjectAnimator.ofFloat(v, View.SCALE_Y, 1f, 0.92f, 1f).setDuration(200).start();
            Intent intent = new Intent(context, ComentariosActivity.class);
            intent.putExtra(ComentariosActivity.EXTRA_ID_FOTO, p.idFoto);
            String titulo = (p.descripcion != null && p.descripcion.length() > 30)
                    ? p.descripcion.substring(0, 30) + "…"
                    : (p.descripcion != null ? p.descripcion : "Publicación");
            intent.putExtra(ComentariosActivity.EXTRA_TITULO, titulo);
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            context.startActivity(intent);
        });
    }

    private void cargarContadorComentarios(int idFoto, TextView tvCount) {
        if (idFoto <= 0) { tvCount.setText("Comentar"); return; }
        ApiClient.getService().getComentarios(idFoto).enqueue(new Callback<ComentariosResponse>() {
            @Override
            public void onResponse(Call<ComentariosResponse> call, Response<ComentariosResponse> response) {
                if (response.isSuccessful() && response.body() != null
                        && response.body().comentarios != null) {
                    int total = response.body().comentarios.size();
                    tvCount.setText(total > 0 ? total + " comentario" + (total != 1 ? "s" : "") : "Comentar");
                }
            }
            @Override
            public void onFailure(Call<ComentariosResponse> call, Throwable t) {
                tvCount.setText("Comentar");
            }
        });
    }

    @Override
    public int getItemCount() { return lista != null ? lista.size() : 0; }

    private String formatFecha(String fecha) {
        if (fecha == null) return "hace un momento";
        try {
            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());
            Date date = sdf.parse(fecha);
            long diff = (System.currentTimeMillis() - date.getTime()) / 1000;
            if (diff < 60)    return "hace unos segundos";
            if (diff < 3600)  return "hace " + (diff / 60) + " min";
            if (diff < 86400) return "hace " + (diff / 3600) + " h";
            return "hace " + (diff / 86400) + " días";
        } catch (ParseException e) { return fecha; }
    }

    static class VH extends RecyclerView.ViewHolder {
        CircleImageView ivAvatar;
        TextView tvAvatarInitial, tvUsername, tvFecha, tvDescripcion, tvLikeIcon, tvCommentCount;
        ImageView ivFoto;
        LinearLayout btnLike, btnComment;

        VH(@NonNull View v) {
            super(v);
            ivAvatar        = v.findViewById(R.id.iv_avatar);
            tvAvatarInitial = v.findViewById(R.id.tv_avatar_initial);
            tvUsername      = v.findViewById(R.id.tv_username);
            tvFecha         = v.findViewById(R.id.tv_fecha);
            tvDescripcion   = v.findViewById(R.id.tv_descripcion);
            tvLikeIcon      = v.findViewById(R.id.tv_like_icon);
            tvCommentCount  = v.findViewById(R.id.tv_comment_count);
            ivFoto          = v.findViewById(R.id.iv_foto);
            btnLike         = v.findViewById(R.id.btn_like);
            btnComment      = v.findViewById(R.id.btn_comment);
        }
    }
}
