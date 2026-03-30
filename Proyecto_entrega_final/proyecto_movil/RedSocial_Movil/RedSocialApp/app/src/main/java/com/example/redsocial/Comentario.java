package com.example.redsocial;

import com.google.gson.annotations.SerializedName;

public class Comentario {

    @SerializedName("id_comentario")
    public int idComentario;

    @SerializedName("id_foto")
    public int idFoto;

    @SerializedName("id_usuario")
    public int idUsuario;

    @SerializedName("contenido")
    public String texto;

    @SerializedName("fecha_publicacion")
    public String fecha;

    @SerializedName("username")
    public String username;
}
