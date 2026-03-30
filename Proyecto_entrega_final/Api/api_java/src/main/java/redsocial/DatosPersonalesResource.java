package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import jakarta.ws.rs.core.Response.Status;
import java.sql.*;
import java.util.*;

@Path("/datos_personales")
public class DatosPersonalesResource {

    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response getDatos(@PathParam("id") int id) {
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement("SELECT * FROM Datos_personales WHERE id_usuario=?")) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            if (!rs.next())
                return Response.status(Status.NOT_FOUND).entity(Map.of("error", "Sin datos para el usuario " + id)).build();
            Map<String, Object> datos = new HashMap<>();
            datos.put("id_usuario",       rs.getInt("id_usuario"));
            datos.put("nombre",           rs.getString("nombre"));
            datos.put("apellido",         rs.getString("apellido"));
            datos.put("fecha_nacimiento", rs.getString("fecha_nacimiento"));
            datos.put("genero",           rs.getString("genero"));
            datos.put("direccion",        rs.getString("direccion"));
            datos.put("telefono",         rs.getString("telefono"));
            return Response.ok(Map.of("datos_personales", datos)).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response crearDatos(Map<String, String> d) {
        if (!d.containsKey("id_usuario") || d.get("id_usuario").isBlank())
            return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Falta id_usuario")).build();
        int id = Integer.parseInt(d.get("id_usuario"));
        String sql = "INSERT INTO Datos_personales (id_usuario,nombre,apellido,fecha_nacimiento,genero,direccion,telefono)" +
                     " VALUES (?,?,?,?,?,?,?)" +
                     " ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), apellido=VALUES(apellido)," +
                     " fecha_nacimiento=VALUES(fecha_nacimiento), genero=VALUES(genero)," +
                     " direccion=VALUES(direccion), telefono=VALUES(telefono)";
        try (Connection con = Conexion.conectar(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, id);
            ps.setString(2, d.get("nombre"));
            ps.setString(3, d.get("apellido"));
            ps.setString(4, d.get("fecha_nacimiento"));
            ps.setString(5, d.get("genero"));
            ps.setString(6, d.get("direccion"));
            ps.setString(7, d.get("telefono"));
            ps.executeUpdate();
            return Response.status(Status.CREATED).entity(Map.of("mensaje", "Datos guardados")).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    @PUT
    @Path("/{id}")
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response editarDatos(@PathParam("id") int id, Map<String, String> d) {
        StringBuilder sb = new StringBuilder("UPDATE Datos_personales SET ");
        List<Object> vals = new ArrayList<>();
        if (d.containsKey("nombre"))           { sb.append("nombre=?, ");           vals.add(d.get("nombre")); }
        if (d.containsKey("apellido"))         { sb.append("apellido=?, ");         vals.add(d.get("apellido")); }
        if (d.containsKey("fecha_nacimiento")) { sb.append("fecha_nacimiento=?, "); vals.add(d.get("fecha_nacimiento")); }
        if (d.containsKey("genero"))           { sb.append("genero=?, ");           vals.add(d.get("genero")); }
        if (d.containsKey("direccion"))        { sb.append("direccion=?, ");        vals.add(d.get("direccion")); }
        if (d.containsKey("telefono"))         { sb.append("telefono=?, ");         vals.add(d.get("telefono")); }
        if (vals.isEmpty()) return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Nada que actualizar")).build();
        vals.add(id);
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(sb.substring(0, sb.length() - 2) + " WHERE id_usuario=?")) {
            for (int i = 0; i < vals.size(); i++) ps.setObject(i + 1, vals.get(i));
            ps.executeUpdate();
            return Response.ok(Map.of("mensaje", "Datos actualizados")).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }
}
