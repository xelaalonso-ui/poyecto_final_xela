package com.example.redsocial;

import android.content.Context;
import android.content.SharedPreferences;

public class SessionManager {

    private static final String PREFS_NAME = "RedSocialPrefs";
    private static final String KEY_ID       = "id_usuario";
    private static final String KEY_USERNAME = "username";
    private static final String KEY_EMAIL    = "email";
    private static final String KEY_LOGGED   = "is_logged_in";

    private final SharedPreferences prefs;
    private final SharedPreferences.Editor editor;

    public SessionManager(Context context) {
        prefs  = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
        editor = prefs.edit();
    }

    public void saveSession(int id, String username, String email) {
        editor.putBoolean(KEY_LOGGED,   true);
        editor.putInt(KEY_ID,           id);
        editor.putString(KEY_USERNAME,  username);
        editor.putString(KEY_EMAIL,     email);
        editor.apply();
    }

    public boolean isLoggedIn() {
        return prefs.getBoolean(KEY_LOGGED, false);
    }

    public int getUserId() {
        return prefs.getInt(KEY_ID, -1);
    }

    public String getUsername() {
        return prefs.getString(KEY_USERNAME, "");
    }

    public String getEmail() {
        return prefs.getString(KEY_EMAIL, "");
    }

    public void logout() {
        editor.clear();
        editor.apply();
    }
}
