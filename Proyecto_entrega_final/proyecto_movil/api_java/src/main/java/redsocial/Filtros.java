package redsocial;

import jakarta.ws.rs.container.ContainerRequestContext;
import jakarta.ws.rs.container.ContainerResponseContext;
import jakarta.ws.rs.container.ContainerResponseFilter;
import jakarta.ws.rs.ext.ExceptionMapper;
import jakarta.ws.rs.ext.Provider;
import jakarta.ws.rs.core.Response;
import java.io.IOException;
import java.util.Map;



@Provider
public class ErrorMapper implements ExceptionMapper<Exception> {
    @Override
    public Response toResponse(Exception e) {
        String mensaje = e.getMessage() != null ? e.getMessage() : "Error interno";
        String json = "{\"error\":\"" + mensaje + "\"}";
        return Response.status(Status.INTERNAL_SERVER_ERROR)
                       .entity(json)
                       .header("Access-Control-Allow-Origin", "*")
                       .header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
                       .header("Access-Control-Allow-Headers", "Content-Type, Authorization")
                       .build();
    }
}
@Provider
class ErrorMapper implements ExceptionMapper<Exception> {
    @Override
    public Response toResponse(Exception e) {
        e.printStackTrace();
        return Response.serverError()
                .entity(Map.of("error", e.getMessage()))
                .build();
    }
}
