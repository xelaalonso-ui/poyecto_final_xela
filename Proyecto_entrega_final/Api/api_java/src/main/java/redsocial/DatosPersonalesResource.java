package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import java.sql.*;

@Path("/datos_personales")
public class DatosPersonalesResource {

    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response getDatos(@PathParam("id") int id) {
        Connection con = null;
        PreparedStatement ps = null;
        ResultSet rs = null;
        try {
            con = Conexion.conectar();
            ps = con.prepareStatement("SELECT * FROM Datos_personales WHERE id_usuario=?");
            ps.setInt(1, id);
            rs = ps.executeQuery();
            if (!rs.next()) {
                return Response.status(Response.Status.NOT_FOUND)
                        .entity("{\"error\":\"Sin datos para el usuario " + id + "\"}")
                        .build();
            }
            String json = "{"
                    + "\"id_usuario\":" + rs.getInt("id_usuario") + ","
                    + "\"nombre\":\"" + escapar(rs.getString("nombre")) + "\","
                    + "\"apellido\":\"" + escapar(rs.getString("apellido")) + "\","
                    + "\"fecha_nacimiento\":\"" + escapar(rs.getString("fecha_nacimiento")) + "\","
                    + "\"genero\":\"" + escapar(rs.getString("genero")) + "\","
                    + "\"direccion\":\"" + escapar(rs.getString("direccion")) + "\","
                    + "\"telefono\":\"" + escapar(rs.getString("telefono")) + "\""
                    + "}";
            return Response.ok("{\"datos_personales\":" + json + "}")
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

    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response crearDatos(Map<String, String> d) {
        if (!d.containsKey("id_usuario") || d.get("id_usuario").isBlank()) {
            return Response.status(Response.Status.BAD_REQUEST)
                    .entity("{\"error\":\"Falta id_usuario\"}")
                    .build();
        }
        int id = Integer.parseInt(d.get("id_usuario"));
        Connection con = null;
        PreparedStatement ps = null;
        try {
            con = Conexion.conectar();
            String sql = "INSERT INTO Datos_personales "
                    + "(id_usuario,nombre,apellido,fecha_nacimiento,genero,direccion,telefono) "
                    + "VALUES (?,?,?,?,?,?,?) "
                    + "ON DUPLICATE KEY UPDATE "
                    + "nombre=VALUES(nombre), apellido=VALUES(apellido), "
                    + "fecha_nacimiento=VALUES(fecha_nacimiento), genero=VALUES(genero), "
                    + "direccion=VALUES(direccion), telefono=VALUES(telefono)";
            ps = con.prepareStatement(sql);
            ps.setInt(1, id);
            ps.setString(2, d.get("nombre"));
            ps.setString(3, d.get("apellido"));
            ps.setString(4, d.get("fecha_nacimiento"));
            ps.setString(5, d.get("genero"));
            ps.setString(6, d.get("direccion"));
            ps.setString(7, d.get("telefono"));
            ps.executeUpdate();
            return Response.status(Response.Status.CREATED)
                    .entity("{\"mensaje\":\"Datos guardados\"}")
                    .build();
        } catch (SQLException e) {
            return Response.status(Response.Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        } finally {
            try { if (ps != null) ps.close(); } catch (SQLException ex) {}
            try { if (con != null) con.close(); } catch (SQLException ex) {}
        }
    }

    @PUT
    @Path("/{id}")
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response editarDatos(@PathParam("id") int id, Map<String, String> d) {
        StringBuilder sb = new StringBuilder("UPDATE Datos_personales SET ");
        boolean primero = true;

        if (d.containsKey("nombre")) {
            sb.append("nombre=?"); primero = false;
        }
        if (d.containsKey("apellido")) {
            if (!primero) sb.append(", "); sb.append("apellido=?"); primero = false;
        }
        if (d.containsKey("fecha_nacimiento")) {
            if (!primero) sb.append(", "); sb.append("fecha_nacimiento=?"); primero = false;
        }
        if (d.containsKey("genero")) {
            if (!primero) sb.append(", "); sb.append("genero=?"); primero = false;
        }
        if (d.containsKey("direccion")) {
            if (!primero) sb.append(", "); sb.append("direccion=?"); primero = false;
        }
        if (d.containsKey("telefono")) {
            if (!primero) sb.append(", "); sb.append("telefono=?"); primero = false;
        }

        if (primero) {
            return Response.status(Response.Status.BAD_REQUEST)
                    .entity("{\"error\":\"Nada que actualizar\"}")
                    .build();
        }

        sb.append(" WHERE id_usuario=?");

        Connection con = null;
        PreparedStatement ps = null;
        try {
            con = Conexion.conectar();
            ps = con.prepareStatement(sb.toString());
            int index = 1;
            if (d.containsKey("nombre")) ps.setString(index++, d.get("nombre"));
            if (d.containsKey("apellido")) ps.setString(index++, d.get("apellido"));
            if (d.containsKey("fecha_nacimiento")) ps.setString(index++, d.get("fecha_nacimiento"));
            if (d.containsKey("genero")) ps.setString(index++, d.get("genero"));
            if (d.containsKey("direccion")) ps.setString(index++, d.get("direccion"));
            if (d.containsKey("telefono")) ps.setString(index++, d.get("telefono"));
            ps.setInt(index, id);
            ps.executeUpdate();
            return Response.ok("{\"mensaje\":\"Datos actualizados\"}")
                    .build();
        } catch (SQLException e) {
            return Response.status(Response.Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        } finally {
            try { if (ps != null) ps.close(); } catch (SQLException ex) {}
            try { if (con != null) con.close(); } catch (SQLException ex) {}
        }
    }

    private String escapar(String s) {
        if (s == null) return "";
        return s.replace("\"", "\\\"");
    }
}