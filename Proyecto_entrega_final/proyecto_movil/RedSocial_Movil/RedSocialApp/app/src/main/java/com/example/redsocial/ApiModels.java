package com.example.redsocial;

import com.google.gson.annotations.SerializedName;
import java.util.List;

class ApiResponse {
    @SerializedName("mensaje")
    public String mensaje;
    @SerializedName("error")
    public String error;
}

class LoginResponse {
    @SerializedName("mensaje")
    public String mensaje;
    @SerializedName("error")
    public String error;
    @SerializedName("usuario")
    public Usuario usuario;
}

class RegisterResponse {
    @SerializedName("mensaje")
    public String mensaje;
    @SerializedName("error")
    public String error;
    @SerializedName("id_usuario")
    public int idUsuario;
}

// getUsuario() porque la api a veces devuelve "usuario" y a veces "datos" segun el endpoint
class UsuarioResponse {
    @SerializedName("usuario")
    public Usuario usuario;
    @SerializedName("datos")
    public Usuario datos;
    @SerializedName("error")
    public String error;

    public Usuario getUsuario() {
        return usuario != null ? usuario : datos;
    }
}

class UsuariosListResponse {
    @SerializedName("total")
    public int total;
    @SerializedName("usuarios")
    public List<Usuario> usuarios;
    @SerializedName("error")
    public String error;
}

class FotosResponse {
    @SerializedName("total")
    public int total;
    @SerializedName("fotos")
    public List<Post> fotos;
    @SerializedName("error")
    public String error;
}

class FotoResponse {
    @SerializedName("foto")
    public Post foto;
    @SerializedName("error")
    public String error;
}

class ComentariosResponse {
    @SerializedName("total")
    public int total;
    @SerializedName("comentarios")
    public List<Comentario> comentarios;
    @SerializedName("error")
    public String error;
}

class ComentarioResponse {
    @SerializedName("comentario")
    public Comentario comentario;
    @SerializedName("error")
    public String error;
}

class DatosPersonalesResponse {
    @SerializedName("datos_personales")
    public DatosPersonales datos;
    @SerializedName("error")
    public String error;
}

class DatosPersonales {
    @SerializedName("id_usuario")
    public int idUsuario;
    @SerializedName("nombre")
    public String nombre;
    @SerializedName("apellido")
    public String apellido;
    @SerializedName("fecha_nacimiento")
    public String fechaNacimiento;
    @SerializedName("genero")
    public String genero;
    @SerializedName("direccion")
    public String direccion;
    @SerializedName("telefono")
    public String telefono;
}

class ActividadResponse {
    @SerializedName("total")
    public int total;
    @SerializedName("actividad")
    public List<Actividad> actividad;
    @SerializedName("error")
    public String error;
}

class Actividad {
    @SerializedName("id_actividad")
    public int idActividad;
    @SerializedName("id_usuario")
    public int idUsuario;
    @SerializedName("tipo_actividad")
    public String tipoActividad;
    @SerializedName("descripcion")
    public String descripcion;
    @SerializedName("fecha_actividad")
    public String fechaActividad;
}
