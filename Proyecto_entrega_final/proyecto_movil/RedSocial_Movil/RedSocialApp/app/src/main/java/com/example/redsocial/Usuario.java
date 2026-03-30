package com.example.redsocial;

import com.google.gson.annotations.SerializedName;

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

    // Para login: la API devuelve {mensaje, usuario:{...}}
    @SerializedName("mensaje")
    public String mensaje;

    // Anidado para login response
    @SerializedName("usuario")
    public Usuario usuario;
}
