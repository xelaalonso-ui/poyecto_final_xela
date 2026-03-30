package com.example.redsocial;

import com.google.gson.annotations.SerializedName;
import java.util.List;

// Modelos de respuesta de la API REST
// Cada clase representa un JSON que devuelve el servidor

// Respuesta generica (mensaje o error)
class ApiResponse {
    @SerializedName("mensaje")
    public String mensaje;

    @SerializedName("error")
    public String error;
}

// Respuesta del login
class LoginResponse {
    @SerializedName("mensaje")
    public String mensaje;

    @SerializedName("error")
    public String error;

    @SerializedName("usuario")
    public Usuario usuario;
}

// Respuesta del registro de nuevo usuario
class RegisterResponse {
    @SerializedName("mensaje")
    public String mensaje;

    @SerializedName("error")
    public String error;

    @SerializedName("id_usuario")
    public int idUsuario;
}

// Respuesta al pedir datos de un usuario
// A veces la API devuelve "usuario" y a veces "datos", por eso comprobamos los dos
class UsuarioResponse {
    @SerializedName("usuario")
    public Usuario usuario;

    @SerializedName("datos")
    public Usuario datos;

    @SerializedName("error")
    public String error;

    // Devuelve el usuario sea cual sea el campo que use la API
    public Usuario getUsuario() {
        if (usuario != null) {
            return usuario;
        } else {
            return datos;
        }
    }
}

// Lista de usuarios
class UsuariosListResponse {
    @SerializedName("total")
    public int total;

    @SerializedName("usuarios")
    public List<Usuario> usuarios;

    @SerializedName("error")
    public String error;
}

// Lista de fotos/publicaciones
class FotosResponse {
    @SerializedName("total")
    public int total;

    @SerializedName("fotos")
    public List<Post> fotos;

    @SerializedName("error")
    public String error;
}

// Una sola foto/publicacion
class FotoResponse {
    @SerializedName("foto")
    public Post foto;

    @SerializedName("error")
    public String error;
}

// Lista de comentarios
class ComentariosResponse {
    @SerializedName("total")
    public int total;

    @SerializedName("comentarios")
    public List<Comentario> comentarios;

    @SerializedName("error")
    public String error;
}

// Un solo comentario
class ComentarioResponse {
    @SerializedName("comentario")
    public Comentario comentario;

    @SerializedName("error")
    public String error;
}

// Datos personales del usuario
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

// Actividad del usuario
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
