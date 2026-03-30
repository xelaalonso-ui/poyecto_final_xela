package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import java.sql.*;

@Path("/comentarios")
public class ComentariosResource {

    @GET
    @Path("/foto/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response comentariosDeFoto(@PathParam("id") int idFoto) {
        String json = "{\"comentarios\":[";
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "SELECT c.*, u.username FROM Comentarios c JOIN Usuario u ON c.id_usuario=u.id_usuario " +
                "WHERE c.id_foto=? ORDER BY c.fecha_publicacion ASC")) {

            ps.setInt(1, idFoto);
            ResultSet rs = ps.executeQuery();
            boolean primero = true;
            while (rs.next()) {
                if (!primero) json += ",";
                json += mapCom(rs);
                primero = false;
            }

        } catch (SQLException e) {
            return Response.status(500)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
        json += "]}";
        return Response.ok(json).build();
    }

    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response getComentario(@PathParam("id") int id) {
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "SELECT c.*, u.username FROM Comentarios c JOIN Usuario u ON c.id_usuario=u.id_usuario " +
                "WHERE c.id_comentario=?")) {

            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            if (!rs.next()) return Response.status(404)
                    .entity("{\"error\":\"Comentario " + id + " no encontrado\"}")
                    .build();

            return Response.ok(mapCom(rs)).build();

        } catch (SQLException e) {
            return Response.status(500)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    @POST
    @Consumes(MediaType.APPLICATION_FORM_URLENCODED)
    @Produces(MediaType.APPLICATION_JSON)
    public Response crearComentario(@FormParam("id_usuario") int uid,
                                    @FormParam("id_foto") int idFoto,
                                    @FormParam("contenido") String contenido) {
        if (contenido == null || contenido.isBlank()) {
            return Response.status(400).entity("{\"error\":\"Falta contenido\"}").build();
        }

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "INSERT INTO Comentarios (id_usuario, id_foto, contenido) VALUES (?,?,?)",
                Statement.RETURN_GENERATED_KEYS)) {

            ps.setInt(1, uid);
            ps.setInt(2, idFoto);
            ps.setString(3, contenido.trim());
            ps.executeUpdate();

            ResultSet keys = ps.getGeneratedKeys();
            int nuevo = keys.next() ? keys.getInt(1) : -1;
            keys.close();

            UsuariosResource.logActividad(con, uid, "comentario", "En foto " + idFoto);

            return Response.status(201)
                    .entity("{\"mensaje\":\"Comentario creado\",\"id_comentario\":" + nuevo + "}")
                    .build();

        } catch (SQLException e) {
            return Response.status(500)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    @PUT
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response editarComentario(@PathParam("id") int id,
                                     @FormParam("contenido") String contenido) {
        if (contenido == null || contenido.isBlank()) {
            return Response.status(400).entity("{\"error\":\"Falta contenido\"}").build();
        }

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "UPDATE Comentarios SET contenido=? WHERE id_comentario=?")) {

            ps.setString(1, contenido.trim());
            ps.setInt(2, id);
            int filas = ps.executeUpdate();
            if (filas == 0) return Response.status(404)
                    .entity("{\"error\":\"Comentario " + id + " no encontrado\"}")
                    .build();

            return Response.ok("{\"mensaje\":\"Comentario actualizado\"}").build();

        } catch (SQLException e) {
            return Response.status(500)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    @DELETE
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response borrarComentario(@PathParam("id") int id) {
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement("SELECT id_usuario FROM Comentarios WHERE id_comentario=?")) {

            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            if (!rs.next()) return Response.status(404)
                    .entity("{\"error\":\"Comentario " + id + " no encontrado\"}")
                    .build();

            int uid = rs.getInt("id_usuario");
            rs.close();

            try (PreparedStatement del = con.prepareStatement("DELETE FROM Comentarios WHERE id_comentario=?")) {
                del.setInt(1, id);
                del.executeUpdate();
            }

            UsuariosResource.logActividad(con, uid, "borrado_comentario", "Comentario " + id);

            return Response.ok("{\"mensaje\":\"Comentario eliminado\",\"id\":" + id + "}").build();

        } catch (SQLException e) {
            return Response.status(500)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    private String mapCom(ResultSet rs) throws SQLException {
        return "{"
                + "\"id_comentario\":" + rs.getInt("id_comentario") + ","
                + "\"id_usuario\":" + rs.getInt("id_usuario") + ","
                + "\"id_foto\":" + rs.getInt("id_foto") + ","
                + "\"contenido\":\"" + rs.getString("contenido") + "\","
                + "\"fecha_publicacion\":\"" + rs.getString("fecha_publicacion") + "\","
                + "\"username\":\"" + rs.getString("username") + "\""
                + "}";
    }
}