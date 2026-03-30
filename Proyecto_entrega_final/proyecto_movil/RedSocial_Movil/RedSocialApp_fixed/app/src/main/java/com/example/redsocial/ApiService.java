package com.example.redsocial;

import java.util.Map;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;

public interface ApiService {

    @POST("login")
    Call<LoginResponse> login(@Body Map<String, String> body);

    @POST("usuarios")
    Call<RegisterResponse> register(@Body Map<String, String> body);

    @GET("usuarios")
    Call<UsuariosListResponse> getUsuarios();

    @GET("usuarios/{id}")
    Call<UsuarioResponse> getUsuario(@Path("id") int id);

    @PUT("usuarios/{id}")
    Call<ApiResponse> updateUsuario(@Path("id") int id, @Body Map<String, String> body);

    @DELETE("usuarios/{id}")
    Call<ApiResponse> deleteUsuario(@Path("id") int id);

    @GET("datos_personales/{id}")
    Call<DatosPersonalesResponse> getDatosPersonales(@Path("id") int id);

    @POST("datos_personales")
    Call<ApiResponse> saveDatosPersonales(@Body Map<String, String> body);

    @PUT("datos_personales/{id}")
    Call<ApiResponse> updateDatosPersonales(@Path("id") int id, @Body Map<String, String> body);

    @GET("fotos")
    Call<FotosResponse> getFotos();

    @GET("fotos/{id}")
    Call<FotoResponse> getFoto(@Path("id") int id);

    @GET("fotos/usuario/{id}")
    Call<FotosResponse> getFotosUsuario(@Path("id") int idUsuario);

    @POST("fotos")
    Call<ApiResponse> createFoto(@Body Map<String, String> body);

    @PUT("fotos/{id}")
    Call<ApiResponse> updateFoto(@Path("id") int id, @Body Map<String, String> body);

    @DELETE("fotos/{id}")
    Call<ApiResponse> deleteFoto(@Path("id") int id);

    @GET("comentarios/foto/{id}")
    Call<ComentariosResponse> getComentarios(@Path("id") int idFoto);

    @GET("comentarios/{id}")
    Call<ComentarioResponse> getComentario(@Path("id") int id);

    @POST("comentarios")
    Call<ApiResponse> createComentario(@Body Map<String, String> body);

    @PUT("comentarios/{id}")
    Call<ApiResponse> updateComentario(@Path("id") int id, @Body Map<String, String> body);

    @DELETE("comentarios/{id}")
    Call<ApiResponse> deleteComentario(@Path("id") int id);

    @GET("actividad/{id}")
    Call<ActividadResponse> getActividad(@Path("id") int idUsuario);

    @POST("actividad")
    Call<ApiResponse> createActividad(@Body Map<String, String> body);

    @DELETE("actividad/{id}")
    Call<ApiResponse> deleteActividad(@Path("id") int id);
}
