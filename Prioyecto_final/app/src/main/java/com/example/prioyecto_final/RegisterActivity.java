package com.example.prioyecto_final;

import android.os.Bundle;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

public class RegisterActivity extends AppCompatActivity {

    protected void onCreate(Bundle b) {
        super.onCreate(b);
        setContentView(R.layout.activity_register);

        findViewById(R.id.register)
                .setOnClickListener(v ->
                        Toast.makeText(this,
                                "Usuario registrado",
                                Toast.LENGTH_SHORT).show());
    }
}
