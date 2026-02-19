package com.example.prioyecto_final;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.fragment.app.Fragment;

public class PerfilFragment extends Fragment {

    public View onCreateView(LayoutInflater i,
                             ViewGroup g,
                             Bundle b){

        return i.inflate(R.layout.fragment_perfil,g,false);
    }
}

