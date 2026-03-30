package com.example.redsocial;

import java.util.Map;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;

// Interface con todos los endpoints de la API
// Retrofit se encarga de hacer las peticiones HTTP automaticamente
public interface ApiService {

    // --- AUTENTICACION ---
    @POST("login")
    Call<LoginResponse> login(@Body Map<String, String> datos);

    // --- USUARIOS ---
    @POST("usuarios")
    Call<RegisterResponse> registrarUsuario(@Body Map<String, String> datos);

    @GET("usuarios")
    Call<UsuariosListResponse> getTodosUsuarios();

    @GET("usuarios/{id}")
    Call<UsuarioResponse> getUsuarioPorId(@Path("id") int id);

    @PUT("usuarios/{id}")
    Call<ApiResponse> actualizarUsuario(@Path("id") int id, @Body Map<String, String> datos);

    @DELETE("usuarios/{id}")
    Call<ApiResponse> eliminarUsuario(@Path("id") int id);

    // --- DATOS PERSONALES ---
    @GET("datos_personales/{id}")
    Call<DatosPersonalesResponse> getDatosPersonales(@Path("id") int id);

    @POST("datos_personales")
    Call<ApiResponse> guardarDatosPersonales(@Body Map<String, String> datos);

    @PUT("datos_personales/{id}")
    Call<ApiResponse> actualizarDatosPersonales(@Path("id") int id, @Body Map<String, String> datos);

    // --- FOTOS / PUBLICACIONES ---
    @GET("fotos")
    Call<FotosResponse> getFotos();

    @GET("fotos/{id}")
    Call<FotoResponse> getFotoPorId(@Path("id") int id);

    @GET("fotos/usuario/{id}")
    Call<FotosResponse> getFotosDeUsuario(@Path("id") int idUsuario);

    @POST("fotos")
    Call<ApiResponse> crearFoto(@Body Map<String, String> datos);

    @PUT("fotos/{id}")
    Call<ApiResponse> actualizarFoto(@Path("id") int id, @Body Map<String, String> datos);

    @DELETE("fotos/{id}")
    Call<ApiResponse> eliminarFoto(@Path("id") int id);

    // --- COMENTARIOS ---
    @GET("comentarios/foto/{id}")
    Call<ComentariosResponse> getComentariosDeFoto(@Path("id") int idFoto);

    @GET("comentarios/{id}")
    Call<ComentarioResponse> getComentarioPorId(@Path("id") int id);

    @POST("comentarios")
    Call<ApiResponse> crearComentario(@Body Map<String, String> datos);

    @PUT("comentarios/{id}")
    Call<ApiResponse> actualizarComentario(@Path("id") int id, @Body Map<String, String> datos);

    @DELETE("comentarios/{id}")
    Call<ApiResponse> eliminarComentario(@Path("id") int id);

    // --- ACTIVIDAD ---
    @GET("actividad/{id}")
    Call<ActividadResponse> getActividadDeUsuario(@Path("id") int idUsuario);

    @POST("actividad")
    Call<ApiResponse> registrarActividad(@Body Map<String, String> datos);

    @DELETE("actividad/{id}")
    Call<ApiResponse> eliminarActividad(@Path("id") int id);
}
