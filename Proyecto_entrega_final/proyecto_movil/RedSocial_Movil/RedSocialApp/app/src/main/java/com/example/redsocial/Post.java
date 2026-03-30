package com.example.redsocial;

import com.google.gson.annotations.SerializedName;

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

    @SerializedName("tipo_foto")
    public String tipoFoto;

    // De JOIN con Usuario
    @SerializedName("username")
    public String username;

    @SerializedName("foto_perfil")
    public String fotoPerfil;
}
