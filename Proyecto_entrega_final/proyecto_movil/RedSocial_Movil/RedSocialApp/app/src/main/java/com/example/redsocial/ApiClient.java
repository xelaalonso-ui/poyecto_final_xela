package com.example.redsocial;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;
import java.util.concurrent.TimeUnit;

// Clase para configurar Retrofit y conectar con la API
// Autor: Alumno DAM - Proyecto Red Social
public class ApiClient {

    // URL base del servidor - cambiar por la IP real si usas movil fisico
    // En el emulador de Android la IP del PC es 10.0.2.2
    private static final String URL_BASE = "http://10.0.2.2:8080/red-social/api/";

    // Instancia de Retrofit (se crea una sola vez)
    private static Retrofit miRetrofit = null;

    // Metodo para obtener el servicio de la API
    public static ApiService getService() {
        // Si ya existe la instancia no la creamos de nuevo (patron singleton)
        if (miRetrofit == null) {

            // Interceptor para ver los logs de las peticiones HTTP
            HttpLoggingInterceptor interceptorLog = new HttpLoggingInterceptor();
            interceptorLog.setLevel(HttpLoggingInterceptor.Level.BODY);

            // Configuramos el cliente HTTP con timeouts de 30 segundos
            OkHttpClient clienteHttp = new OkHttpClient.Builder()
                    .addInterceptor(interceptorLog)
                    .connectTimeout(30, TimeUnit.SECONDS)
                    .readTimeout(30, TimeUnit.SECONDS)
                    .build();

            // Construimos Retrofit con la URL base y el cliente
            miRetrofit = new Retrofit.Builder()
                    .baseUrl(URL_BASE)
                    .client(clienteHttp)
                    .addConverterFactory(GsonConverterFactory.create())
                    .build();
        }

        return miRetrofit.create(ApiService.class);
    }
}
