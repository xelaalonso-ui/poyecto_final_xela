package com.example.prioyecto_final;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import java.util.List;

import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;

public class PostAdapter extends RecyclerView.Adapter<PostAdapter.VH>{


    List<Post> lista;

    public PostAdapter(List<Post> l){ lista=l; }

    public VH onCreateViewHolder(ViewGroup g, int v){

        View view = LayoutInflater.from(g.getContext())
                .inflate(R.layout.item_post,g,false);

        return new VH(view);
    }

    public void onBindViewHolder(VH h,int i){

        Post p = lista.get(i);

        h.desc.setText(p.descripcion);

        Glide.with(h.itemView.getContext())
                .load(p.url_foto)
                .into(h.img);

        h.itemView.setAlpha(0f);
        h.itemView.animate().alpha(1f).setDuration(500);
    }

    public int getItemCount(){ return lista.size(); }

    class VH extends RecyclerView.ViewHolder{
        ImageView img;
        TextView desc;

        VH(View v){
            super(v);
            img=v.findViewById(R.id.img);
            desc=v.findViewById(R.id.desc);
        }
    }
}
