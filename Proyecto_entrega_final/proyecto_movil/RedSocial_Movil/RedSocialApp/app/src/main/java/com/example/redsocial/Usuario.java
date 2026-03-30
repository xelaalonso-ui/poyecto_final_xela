package com.example.redsocial;

import com.google.gson.annotations.SerializedName;

// Modelo de usuario de la red social
public class Usuario {

    @SerializedName("id_usuario")
    public int idUsuario;

    @SerializedName("username")
    public String username;

    @SerializedName("email")
    public String email;

    @SerializedName("estado_cuenta")
    public String estadoCuenta;

    @SerializedName("fecha_registro")
    public String fechaRegistro;

    // Campos extra que puede devolver el login
    @SerializedName("mensaje")
    public String mensaje;

    @SerializedName("usuario")
    public Usuario usuario;
}
