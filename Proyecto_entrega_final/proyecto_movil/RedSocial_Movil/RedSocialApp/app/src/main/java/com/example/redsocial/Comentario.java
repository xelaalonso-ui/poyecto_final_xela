package com.example.redsocial;

import com.google.gson.annotations.SerializedName;

// Modelo de comentario en una publicacion
public class Comentario {

    @SerializedName("id_comentario")
    public int idComentario;

    @SerializedName("id_foto")
    public int idFoto;

    @SerializedName("id_usuario")
    public int idUsuario;

    // El campo en la BD se llama "contenido"
    @SerializedName("contenido")
    public String texto;

    @SerializedName("fecha_publicacion")
    public String fecha;

    // Viene del JOIN con usuarios
    @SerializedName("username")
    public String username;
}
