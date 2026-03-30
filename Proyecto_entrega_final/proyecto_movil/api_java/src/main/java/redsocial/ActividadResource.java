package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import jakarta.ws.rs.core.Response.Status;
import java.sql.*;
import java.util.*;

@Path("/actividad")
public class ActividadResource {

    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response getActividad(@PathParam("id") int id) {
        List<Map<String, Object>> lista = new ArrayList<>();
        try (Connection con = Conexion.conectar();
                PreparedStatement ps = con.prepareStatement(
                        "SELECT * FROM Actividad WHERE id_usuario=? ORDER BY fecha_actividad DESC")) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            while (rs.next()) {
                Map<String, Object> a = new HashMap<>();
                a.put("id_actividad", rs.getInt("id_actividad"));
                a.put("id_usuario", rs.getInt("id_usuario"));
                a.put("tipo_actividad", rs.getString("tipo_actividad"));
                a.put("descripcion", rs.getString("descripcion"));
                a.put("fecha_actividad", rs.getString("fecha_actividad"));
                lista.add(a);
            }
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
        return Response.ok(Map.of("total", lista.size(), "actividad", lista)).build();
    }

    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response crearActividad(Map<String, String> d) {
        if (!d.containsKey("id_usuario") || d.get("id_usuario").isBlank()) {
            return Response.status(Status.BAD_REQUEST)
                    .entity(Map.of("error", "Falta id_usuario"))
                    .build();
        }

        if (!d.containsKey("tipo_actividad") || d.get("tipo_actividad").isBlank()) {
            return Response.status(Status.BAD_REQUEST)
                    .entity(Map.of("error", "Falta tipo_actividad"))
                    .build();
        }

        int uid = Integer.parseInt(d.get("id_usuario"));

        try (Connection con = Conexion.conectar();
                PreparedStatement ps = con.prepareStatement(
                        "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) VALUES (?,?,?)",
                        Statement.RETURN_GENERATED_KEYS)) {

            ps.setInt(1, uid);
            ps.setString(2, d.get("tipo_actividad").trim());
            ps.setString(3, d.get("descripcion"));

            ps.executeUpdate();

            ResultSet keys = ps.getGeneratedKeys();
            int nuevo = keys.next() ? keys.getInt(1) : -1;
            keys.close();

            return Response.status(Status.CREATED)
                    .entity(Map.of("mensaje", "Actividad registrada", "id_actividad", nuevo))
                    .build();

        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR)
                    .entity(Map.of("error", e.getMessage()))
                    .build();
        }
    }

    @DELETE
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response borrarActividad(@PathParam("id") int id) {
  try (Connection con = Conexion.conectar();
     PreparedStatement ps = con.prepareStatement("DELETE FROM Actividad WHERE id_actividad=?")) {

    ps.setInt(1, id);
    int filas = ps.executeUpdate();

    if (filas == 0) {
        return Response.status(Status.NOT_FOUND)
                .entity(Map.of("error", "Actividad " + id + " no encontrada"))
                .build();
    }

    return Response.ok(Map.of("mensaje", "Actividad eliminada", "id", id)).build();

} catch (SQLException e) {
    return Response.status(Status.INTERNAL_SERVER_ERROR)
            .entity(Map.of("error", e.getMessage()))
            .build();
}
    }
}
