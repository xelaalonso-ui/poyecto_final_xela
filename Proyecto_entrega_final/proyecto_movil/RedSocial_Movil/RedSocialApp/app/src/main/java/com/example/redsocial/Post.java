package com.example.redsocial;

import com.google.gson.annotations.SerializedName;

// Modelo que representa una publicacion (foto) de la red social
public class Post {

    @SerializedName("id_foto")
    public int idFoto;

    @SerializedName("id_usuario")
    public int idUsuario;

    @SerializedName("url_foto")
    public String urlFoto;

    @SerializedName("descripcion")
    public String descripcion;

    @SerializedName("fecha_subida")
    public String fechaSubida;

    // Puede ser "publicacion" o "perfil"
    @SerializedName("tipo_foto")
    public String tipoFoto;

    // Estos campos vienen del JOIN con la tabla usuarios
    @SerializedName("username")
    public String username;

    @SerializedName("foto_perfil")
    public String fotoPerfil;
}
