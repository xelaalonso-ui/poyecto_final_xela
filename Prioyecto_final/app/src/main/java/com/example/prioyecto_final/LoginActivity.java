package com.example.prioyecto_final;



import android.content.Intent;
import android.os.Bundle;
import android.widget.EditText;

import androidx.appcompat.app.AppCompatActivity;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;


public class LoginActivity extends AppCompatActivity {

    EditText user, pass;

    protected void onCreate(Bundle b) {
        super.onCreate(b);
        setContentView(R.layout.activity_login);

        user = findViewById(R.id.user);
        pass = findViewById(R.id.pass);

        findViewById(R.id.login).setOnClickListener(v -> login());

        findViewById(R.id.register)
                .setOnClickListener(v ->
                        startActivity(new Intent(this,
                                RegisterActivity.class)));
    }

    void login() {

        ApiClient.getService()
                .login(user.getText().toString(),
                        pass.getText().toString())
                .enqueue(new Callback<Usuario>() {

                    public void onResponse(Call<Usuario> call,
                                           Response<Usuario> r) {

                        if(r.isSuccessful()) {
                            startActivity(
                                    new Intent(LoginActivity.this,
                                            MainActivity.class));

                            overridePendingTransition(
                                    android.R.anim.fade_in,
                                    android.R.anim.fade_out);
                        }
                    }

                    public void onFailure(Call<Usuario> c, Throwable t){}
                });
    }
}
