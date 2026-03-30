package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import jakarta.ws.rs.core.Response.Status;
import java.sql.*;

@Path("/datos_personales")
public class DatosPersonalesResource {

    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response getDatos(@PathParam("id") int id) {

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                     "SELECT * FROM Datos_personales WHERE id_usuario=?")) {

            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();

            if (!rs.next()) {
                return Response.status(Status.NOT_FOUND)
                        .entity("{\"error\":\"Sin datos para el usuario " + id + "\"}")
                        .build();
            }

            String json = "{"
                    + "\"id_usuario\":" + rs.getInt("id_usuario") + ","
                    + "\"nombre\":\"" + escapar(rs.getString("nombre")) + "\","
                    + "\"apellido\":\"" + escapar(rs.getString("apellido")) + "\","
                    + "\"fecha_nacimiento\":\"" + rs.getString("fecha_nacimiento") + "\","
                    + "\"genero\":\"" + escapar(rs.getString("genero")) + "\","
                    + "\"direccion\":\"" + escapar(rs.getString("direccion")) + "\","
                    + "\"telefono\":\"" + escapar(rs.getString("telefono")) + "\""
                    + "}";

            return Response.ok("{\"datos_personales\":" + json + "}").build();

        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    @POST
    @Consumes(MediaType.APPLICATION_FORM_URLENCODED)
    @Produces(MediaType.APPLICATION_JSON)
    public Response crearDatos(@FormParam("id_usuario") int id,
                               @FormParam("nombre") String nombre,
                               @FormParam("apellido") String apellido,
                               @FormParam("fecha_nacimiento") String fecha,
                               @FormParam("genero") String genero,
                               @FormParam("direccion") String direccion,
                               @FormParam("telefono") String telefono) {

        String sql = "INSERT INTO Datos_personales (id_usuario,nombre,apellido,fecha_nacimiento,genero,direccion,telefono) " +
                     "VALUES (?,?,?,?,?,?,?) " +
                     "ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), apellido=VALUES(apellido), " +
                     "fecha_nacimiento=VALUES(fecha_nacimiento), genero=VALUES(genero), " +
                     "direccion=VALUES(direccion), telefono=VALUES(telefono)";

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(sql)) {

            ps.setInt(1, id);
            ps.setString(2, nombre);
            ps.setString(3, apellido);
            ps.setString(4, fecha);
            ps.setString(5, genero);
            ps.setString(6, direccion);
            ps.setString(7, telefono);

            ps.executeUpdate();

            return Response.status(Status.CREATED)
                    .entity("{\"mensaje\":\"Datos guardados\"}")
                    .build();

        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    @PUT
    @Path("/{id}")
    @Consumes(MediaType.APPLICATION_FORM_URLENCODED)
    @Produces(MediaType.APPLICATION_JSON)
    public Response editarDatos(@PathParam("id") int id,
                                @FormParam("nombre") String nombre,
                                @FormParam("apellido") String apellido,
                                @FormParam("fecha_nacimiento") String fecha,
                                @FormParam("genero") String genero,
                                @FormParam("direccion") String direccion,
                                @FormParam("telefono") String telefono) {

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "UPDATE Datos_personales SET nombre=?, apellido=?, fecha_nacimiento=?, genero=?, direccion=?, telefono=? WHERE id_usuario=?")) {

            ps.setString(1, nombre);
            ps.setString(2, apellido);
            ps.setString(3, fecha);
            ps.setString(4, genero);
            ps.setString(5, direccion);
            ps.setString(6, telefono);
            ps.setInt(7, id);

            int filas = ps.executeUpdate();

            if (filas == 0) {
                return Response.status(Status.NOT_FOUND)
                        .entity("{\"error\":\"Usuario " + id + " no encontrado\"}")
                        .build();
            }

            return Response.ok("{\"mensaje\":\"Datos actualizados\"}").build();

        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    private String escapar(String s) {
        if (s == null) return "";
        return s.replace("\"", "\\\"");
    }
}