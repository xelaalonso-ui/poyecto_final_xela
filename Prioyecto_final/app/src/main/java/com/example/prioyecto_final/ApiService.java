package com.example.prioyecto_final;



import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Query;

public class ApiService {
    @POST("login.php")
    Call<Usuario> login(
            @Query("user") String user,
            @Query("pass") String pass) {
        return null;
    }

    @GET("posts.php")
    Call<List<Post>> getPosts() {
        return null;
    }

}
