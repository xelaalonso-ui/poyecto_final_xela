package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import jakarta.ws.rs.core.Response.Status;
import java.sql.*;
import java.util.*;

@Path("/login")
public class LoginResource {

    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response login(Map<String, String> d) {
        String email    = d.get("email");
        String password = d.get("password");
        if (email == null || email.isBlank())    return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Falta email")).build();
        if (password == null || password.isBlank()) return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Falta password")).build();
        email = email.trim().toLowerCase();
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "SELECT id_usuario, username, email, password_hash, estado_cuenta FROM Usuario WHERE email=?")) {
            ps.setString(1, email);
            ResultSet rs = ps.executeQuery();
            if (!rs.next() || !UsuariosResource.verificarPass(password.trim(), rs.getString("password_hash")))
                return Response.status(Status.UNAUTHORIZED).entity(Map.of("error", "Credenciales incorrectas")).build();
            int    uid    = rs.getInt("id_usuario");
            String uname  = rs.getString("username");
            String estado = rs.getString("estado_cuenta");
            rs.close();
            PreparedStatement upd = con.prepareStatement("UPDATE Usuario SET ultimo_login=NOW() WHERE id_usuario=?");
            upd.setInt(1, uid); upd.executeUpdate(); upd.close();
            UsuariosResource.logActividad(con, uid, "login", "Sesion iniciada");
            Map<String, Object> usuario = new HashMap<>();
            usuario.put("id_usuario", uid);
            usuario.put("username", uname);
            usuario.put("email", email);
            usuario.put("estado_cuenta", estado);
            return Response.ok(Map.of("mensaje", "Login correcto", "usuario", usuario)).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }
}
