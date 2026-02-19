package com.example.prioyecto_final;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class ApiClient {
    static Retrofit retrofit;

    public static ApiService getService(){

        if(retrofit==null){

            retrofit = new Retrofit.Builder()
                    .baseUrl("http://TU_SERVIDOR/api/")
                    .addConverterFactory(
                            GsonConverterFactory.create())
                    .build();
        }

        return retrofit.create(ApiService.class);
    }
}
