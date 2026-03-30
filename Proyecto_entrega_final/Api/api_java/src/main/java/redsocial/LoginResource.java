package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import java.sql.*;

@Path("/login")
public class LoginResource {

    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response login(Map<String, String> d) {
        if (d == null) {
            return Response.status(Response.Status.BAD_REQUEST)
                    .entity("{\"error\":\"Datos vacíos\"}")
                    .build();
        }

        String email = d.get("email");
        String password = d.get("password");

        if (email == null || email.isBlank()) {
            return Response.status(Response.Status.BAD_REQUEST)
                    .entity("{\"error\":\"Falta email\"}")
                    .build();
        }

        if (password == null || password.isBlank()) {
            return Response.status(Response.Status.BAD_REQUEST)
                    .entity("{\"error\":\"Falta password\"}")
                    .build();
        }

        email = email.trim().toLowerCase();

        Connection con = null;
        PreparedStatement ps = null;
        ResultSet rs = null;

        try {
            con = Conexion.conectar();
            ps = con.prepareStatement(
                    "SELECT id_usuario, username, email, password_hash, estado_cuenta FROM Usuario WHERE email=?");
            ps.setString(1, email);
            rs = ps.executeQuery();

            if (!rs.next() || !UsuariosResource.verificarPass(password.trim(), rs.getString("password_hash"))) {
                return Response.status(Response.Status.UNAUTHORIZED)
                        .entity("{\"error\":\"Credenciales incorrectas\"}")
                        .build();
            }

            int uid = rs.getInt("id_usuario");
            String uname = rs.getString("username");
            String estado = rs.getString("estado_cuenta");

            rs.close();
            ps.close();

            ps = con.prepareStatement("UPDATE Usuario SET ultimo_login=NOW() WHERE id_usuario=?");
            ps.setInt(1, uid);
            ps.executeUpdate();
            ps.close();

            UsuariosResource.logActividad(con, uid, "login", "Sesion iniciada");

            String jsonUsuario = "{"
                    + "\"id_usuario\":" + uid + ","
                    + "\"username\":\"" + escapar(uname) + "\","
                    + "\"email\":\"" + escapar(email) + "\","
                    + "\"estado_cuenta\":\"" + escapar(estado) + "\""
                    + "}";

            return Response.ok("{\"mensaje\":\"Login correcto\",\"usuario\":" + jsonUsuario + "}")
                    .build();

        } catch (SQLException e) {
            return Response.status(Response.Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        } finally {
            try { if (rs != null) rs.close(); } catch (SQLException ex) {}
            try { if (ps != null) ps.close(); } catch (SQLException ex) {}
            try { if (con != null) con.close(); } catch (SQLException ex) {}
        }
    }

    private String escapar(String s) {
        if (s == null) return "";
        return s.replace("\"", "\\\"");
    }
}