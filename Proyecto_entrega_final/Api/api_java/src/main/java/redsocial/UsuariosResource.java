package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import java.sql.*;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

@Path("/usuarios")
public class UsuariosResource {

    @GET
    @Produces(MediaType.APPLICATION_JSON)
    public Response getUsuarios() {
        StringBuilder json = new StringBuilder();
        json.append("{\"usuarios\":[");
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                 "SELECT id_usuario, username, email, fecha_registro, estado_cuenta, ultimo_login FROM Usuario ORDER BY fecha_registro DESC");
             ResultSet rs = ps.executeQuery()) {

            boolean primero = true;
            while (rs.next()) {
                if (!primero) {
                    json.append(",");
                }
                json.append("{")
                    .append("\"id_usuario\":").append(rs.getInt("id_usuario")).append(",")
                    .append("\"username\":\"").append(escapar(rs.getString("username"))).append("\",")
                    .append("\"email\":\"").append(escapar(rs.getString("email"))).append("\",")
                    .append("\"fecha_registro\":\"").append(escapar(rs.getString("fecha_registro"))).append("\",")
                    .append("\"estado_cuenta\":\"").append(escapar(rs.getString("estado_cuenta"))).append("\",")
                    .append("\"ultimo_login\":\"").append(escapar(rs.getString("ultimo_login"))).append("\"")
                    .append("}");
                primero = false;
            }

        } catch (SQLException e) {
            return Response.status(500).entity("{\"error\":\"" + escapar(e.getMessage()) + "\"}").build();
        }

        json.append("]}");
        return Response.ok(json.toString()).build();
    }

    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response getUsuario(@PathParam("id") int id) {
        String sql = "SELECT u.id_usuario, u.username, u.email, u.fecha_registro, u.estado_cuenta, u.ultimo_login," +
                     " d.nombre, d.apellido, d.fecha_nacimiento, d.genero, d.direccion, d.telefono" +
                     " FROM Usuario u LEFT JOIN Datos_personales d ON u.id_usuario = d.id_usuario" +
                     " WHERE u.id_usuario = ?";
        try (Connection con = Conexion.conectar(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();

            if (!rs.next()) {
                return Response.status(404).entity("{\"error\":\"Usuario " + id + " no encontrado\"}").build();
            }

            StringBuilder json = new StringBuilder();
            json.append("{\"usuario\":{")
                .append("\"id_usuario\":").append(rs.getInt("id_usuario")).append(",")
                .append("\"username\":\"").append(escapar(rs.getString("username"))).append("\",")
                .append("\"email\":\"").append(escapar(rs.getString("email"))).append("\",")
                .append("\"fecha_registro\":\"").append(escapar(rs.getString("fecha_registro"))).append("\",")
                .append("\"estado_cuenta\":\"").append(escapar(rs.getString("estado_cuenta"))).append("\",")
                .append("\"ultimo_login\":\"").append(escapar(rs.getString("ultimo_login"))).append("\",")
                .append("\"nombre\":\"").append(escapar(rs.getString("nombre"))).append("\",")
                .append("\"apellido\":\"").append(escapar(rs.getString("apellido"))).append("\",")
                .append("\"fecha_nacimiento\":\"").append(escapar(rs.getString("fecha_nacimiento"))).append("\",")
                .append("\"genero\":\"").append(escapar(rs.getString("genero"))).append("\",")
                .append("\"direccion\":\"").append(escapar(rs.getString("direccion"))).append("\",")
                .append("\"telefono\":\"").append(escapar(rs.getString("telefono"))).append("\"")
                .append("}}");

            return Response.ok(json.toString()).build();

        } catch (SQLException e) {
            return Response.status(500).entity("{\"error\":\"" + escapar(e.getMessage()) + "\"}").build();
        }
    }

    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response crearUsuario(Map<String, String> d) {
        String username = d.get("username");
        String email = d.get("email");
        String password = d.get("password");

        if (username == null || username.isBlank()) {
            return Response.status(400).entity("{\"error\":\"Falta username\"}").build();
        }

        if (email == null || email.isBlank()) {
            return Response.status(400).entity("{\"error\":\"Falta email\"}").build();
        }

        if (password == null || password.isBlank()) {
            return Response.status(400).entity("{\"error\":\"Falta password\"}").build();
        }

        username = username.trim();
        email = email.trim().toLowerCase();
        String hash = hashPass(password.trim());

        try (Connection con = Conexion.conectar()) {
            PreparedStatement chk = con.prepareStatement("SELECT id_usuario FROM Usuario WHERE email=? OR username=?");
            chk.setString(1, email);
            chk.setString(2, username);

            if (chk.executeQuery().next()) {
                return Response.status(409).entity("{\"error\":\"El username o email ya existe\"}").build();
            }
            chk.close();

            PreparedStatement ps = con.prepareStatement(
                "INSERT INTO Usuario (username, email, password_hash, estado_cuenta) VALUES (?,?,?,?)",
                Statement.RETURN_GENERATED_KEYS);
            ps.setString(1, username);
            ps.setString(2, email);
            ps.setString(3, hash);
            ps.setString(4, "activo");
            ps.executeUpdate();

            ResultSet keys = ps.getGeneratedKeys();
            int nuevo = keys.next() ? keys.getInt(1) : -1;
            keys.close();
            ps.close();

            logActividad(con, nuevo, "registro", "Nuevo usuario: " + username);

            return Response.status(201).entity("{\"mensaje\":\"Usuario creado\",\"id_usuario\":" + nuevo + "}").build();
        } catch (SQLException e) {
            return Response.status(500).entity("{\"error\":\"" + escapar(e.getMessage()) + "\"}").build();
        }
    }

    @DELETE
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response borrarUsuario(@PathParam("id") int id) {
        try (Connection con = Conexion.conectar()) {
            PreparedStatement chk = con.prepareStatement("SELECT username FROM Usuario WHERE id_usuario=?");
            chk.setInt(1, id);
            ResultSet rs = chk.executeQuery();

            if (!rs.next()) {
                return Response.status(404).entity("{\"error\":\"Usuario " + id + " no encontrado\"}").build();
            }

            String username = rs.getString("username");
            rs.close();
            chk.close();

            PreparedStatement ps = con.prepareStatement("DELETE FROM Usuario WHERE id_usuario=?");
            ps.setInt(1, id);
            ps.executeUpdate();
            ps.close();

            return Response.ok("{\"mensaje\":\"Usuario '" + username + "' eliminado\",\"id\":" + id + "}").build();
        } catch (SQLException e) {
            return Response.status(500).entity("{\"error\":\"" + escapar(e.getMessage()) + "\"}").build();
        }
    }

    static void logActividad(Connection con, int uid, String tipo, String desc) {
        try (PreparedStatement ps = con.prepareStatement(
                "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) VALUES (?,?,?)")) {
            ps.setInt(1, uid);
            ps.setString(2, tipo);
            ps.setString(3, desc);
            ps.executeUpdate();
        } catch (SQLException ignored) {
        }
    }

    static String hashPass(String p) {
        try {
            MessageDigest md = MessageDigest.getInstance("SHA-256");
            byte[] bytes = md.digest(p.getBytes());
            StringBuilder sb = new StringBuilder();
            for (byte b : bytes) {
                sb.append(String.format("%02x", b));
            }
            return sb.toString();
        } catch (NoSuchAlgorithmException e) {
            throw new RuntimeException(e);
        }
    }

    private String escapar(String s) {
        if (s == null) {
            return "";
        }
        return s.replace("\"", "\\\"");
    }
}