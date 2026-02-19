package com.example.prioyecto_final;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.fragment.app.Fragment;

public class CuentaFragment extends Fragment {

    public View onCreateView(LayoutInflater i,
                             ViewGroup g,
                             Bundle b){

        View v = i.inflate(R.layout.fragment_cuenta,g,false);

        v.findViewById(R.id.logout)
                .setOnClickListener(x ->
                        startActivity(new Intent(
                                getActivity(),
                                LoginActivity.class)));

        return v;
    }
}
