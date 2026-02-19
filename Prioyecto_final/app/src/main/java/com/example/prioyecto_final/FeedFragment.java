package com.example.prioyecto_final;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import retrofit2.Call;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import retrofit2.Callback;
import retrofit2.Response;

import java.util.List;

public class FeedFragment extends Fragment {

    public View onCreateView(LayoutInflater i,
                             ViewGroup g,
                             Bundle b) {

        View v = i.inflate(R.layout.fragment_feed,g,false);

        RecyclerView r = v.findViewById(R.id.recycler);
        r.setLayoutManager(new LinearLayoutManager(getContext()));

        ApiClient.getService().getPosts()
                .enqueue(new Callback<List<Post>>() {

                    public void onResponse(Call<List<Post>> c,
                                           Response<List<Post>> r2) {

                        r.setAdapter(new PostAdapter(r2.body()));
                    }

                    public void onFailure(Call<List<Post>> c, Throwable t){}
                });

        return v;
    }
}
