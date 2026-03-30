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
class CorsFilter implements ContainerResponseFilter {
    @Override
    public void filter(ContainerRequestContext req, ContainerResponseContext res) throws IOException {
        res.getHeaders().add("Access-Control-Allow-Origin",  "*");
        res.getHeaders().add("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
        res.getHeaders().add("Access-Control-Allow-Headers", "Content-Type, Authorization");
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
