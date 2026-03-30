package com.example.redsocial;

import android.content.Context;
import android.content.SharedPreferences;

// Clase que gestiona la sesion del usuario con SharedPreferences
// Guarda el id, nombre y email del usuario logueado
public class SessionManager {

    // Nombre del archivo de preferencias
    private static final String NOMBRE_PREFS = "SesionRedSocial";

    // Claves para guardar los datos
    private static final String CLAVE_ID       = "id_usuario";
    private static final String CLAVE_USERNAME  = "username";
    private static final String CLAVE_EMAIL     = "email";
    private static final String CLAVE_LOGUEADO  = "esta_logueado";

    private SharedPreferences preferencias;
    private SharedPreferences.Editor editor;

    // Constructor: recibe el contexto para acceder a SharedPreferences
    public SessionManager(Context contexto) {
        preferencias = contexto.getSharedPreferences(NOMBRE_PREFS, Context.MODE_PRIVATE);
        editor = preferencias.edit();
    }

    // Guarda los datos de sesion cuando el usuario inicia sesion
    public void guardarSesion(int id, String username, String email) {
        editor.putBoolean(CLAVE_LOGUEADO, true);
        editor.putInt(CLAVE_ID, id);
        editor.putString(CLAVE_USERNAME, username);
        editor.putString(CLAVE_EMAIL, email);
        editor.apply(); // apply() es asincrono (mejor que commit())
    }

    // Alias para mantener compatibilidad
    public void saveSession(int id, String username, String email) {
        guardarSesion(id, username, email);
    }

    // Comprueba si hay un usuario logueado
    public boolean isLoggedIn() {
        return preferencias.getBoolean(CLAVE_LOGUEADO, false);
    }

    // Devuelve el id del usuario logueado (-1 si no hay sesion)
    public int getUserId() {
        return preferencias.getInt(CLAVE_ID, -1);
    }

    // Devuelve el nombre de usuario
    public String getUsername() {
        return preferencias.getString(CLAVE_USERNAME, "");
    }

    // Devuelve el email
    public String getEmail() {
        return preferencias.getString(CLAVE_EMAIL, "");
    }

    // Cierra la sesion borrando todos los datos guardados
    public void cerrarSesion() {
        editor.clear();
        editor.apply();
    }

    // Alias en ingles
    public void logout() {
        cerrarSesion();
    }
}
