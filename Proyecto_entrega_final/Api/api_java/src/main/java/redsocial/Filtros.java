package redsocial;

import jakarta.ws.rs.ext.Provider;
import jakarta.ws.rs.ext.ExceptionMapper;
import jakarta.ws.rs.core.Response;


@Provider
public class ErrorMapper implements ExceptionMapper<Exception> {

    @Override
    public Response toResponse(Exception e) {
        // Mensaje de error simple
        String mensaje = e.getMessage() != null ? e.getMessage() : "Error interno";
        String json = "{\"error\":\"" + mensaje + "\"}";

        // Devuelve la respuesta con CORS incluido
        return Response.status(Response.Status.INTERNAL_SERVER_ERROR)
                .entity(json)
                .header("Access-Control-Allow-Origin", "*")
                .header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
                .header("Access-Control-Allow-Headers", "Content-Type, Authorization")
                .build();
    }
}