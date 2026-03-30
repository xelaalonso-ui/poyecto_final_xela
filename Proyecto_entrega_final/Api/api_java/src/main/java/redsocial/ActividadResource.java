package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import java.sql.*;

@Path("/actividad")
public class ActividadResource {

    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response getActividad(@PathParam("id") int id) {
        StringBuilder json = new StringBuilder();
        json.append("{\"actividad\":[");

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                 "SELECT * FROM Actividad WHERE id_usuario=? ORDER BY fecha_actividad DESC")) {

            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();

            boolean primero = true;
            while (rs.next()) {
                if (!primero) {
                    json.append(",");
                }

                json.append("{")
                    .append("\"id_actividad\":").append(rs.getInt("id_actividad")).append(",")
                    .append("\"id_usuario\":").append(rs.getInt("id_usuario")).append(",")
                    .append("\"tipo_actividad\":\"").append(escapar(rs.getString("tipo_actividad"))).append("\",")
                    .append("\"descripcion\":\"").append(escapar(rs.getString("descripcion"))).append("\",")
                    .append("\"fecha_actividad\":\"").append(escapar(rs.getString("fecha_actividad"))).append("\"")
                    .append("}");

                primero = false;
            }

        } catch (SQLException e) {
            return Response.status(500)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }

        json.append("]}");
        return Response.ok(json.toString()).build();
    }

    @POST
    @Consumes(MediaType.APPLICATION_FORM_URLENCODED)
    @Produces(MediaType.APPLICATION_JSON)
    public Response crearActividad(@FormParam("id_usuario") int uid,
                                   @FormParam("tipo_actividad") String tipo,
                                   @FormParam("descripcion") String descripcion) {

        if (uid <= 0) {
            return Response.status(400)
                    .entity("{\"error\":\"ID inválido\"}")
                    .build();
        }

        if (tipo == null || tipo.isBlank()) {
            return Response.status(400)
                    .entity("{\"error\":\"Falta tipo_actividad\"}")
                    .build();
        }

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                 "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) VALUES (?,?,?)",
                 Statement.RETURN_GENERATED_KEYS)) {

            ps.setInt(1, uid);
            ps.setString(2, tipo.trim());
            ps.setString(3, descripcion);

            ps.executeUpdate();

            ResultSet keys = ps.getGeneratedKeys();
            int nuevo = keys.next() ? keys.getInt(1) : -1;
            keys.close();

            return Response.status(201)
                    .entity("{\"mensaje\":\"Actividad registrada\",\"id_actividad\":" + nuevo + "}")
                    .build();

        } catch (SQLException e) {
            return Response.status(500)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    @DELETE
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response borrarActividad(@PathParam("id") int id) {

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                 "DELETE FROM Actividad WHERE id_actividad=?")) {

            ps.setInt(1, id);
            int filas = ps.executeUpdate();

            if (filas == 0) {
                return Response.status(404)
                        .entity("{\"error\":\"Actividad " + id + " no encontrada\"}")
                        .build();
            }

            return Response.ok("{\"mensaje\":\"Actividad eliminada\",\"id\":" + id + "}")
                    .build();

        } catch (SQLException e) {
            return Response.status(500)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    private String escapar(String s) {
        if (s == null) return "";
        return s.replace("\"", "\\\"");
    }
}