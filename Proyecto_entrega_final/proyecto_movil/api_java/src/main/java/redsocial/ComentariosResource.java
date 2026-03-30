package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import jakarta.ws.rs.core.Response.Status;
import java.sql.*;
import java.util.*;

@Path("/comentarios")
public class ComentariosResource {

    @GET
    @Path("/foto/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response comentariosDeFoto(@PathParam("id") int idFoto) {
        List<Map<String, Object>> lista = new ArrayList<>();
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "SELECT c.*, u.username FROM Comentarios c JOIN Usuario u ON c.id_usuario=u.id_usuario" +
                " WHERE c.id_foto=? ORDER BY c.fecha_publicacion ASC")) {
            ps.setInt(1, idFoto);
            ResultSet rs = ps.executeQuery();
            while (rs.next()) lista.add(mapCom(rs));
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
        return Response.ok(Map.of("total", lista.size(), "comentarios", lista)).build();
    }

    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response getComentario(@PathParam("id") int id) {
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "SELECT c.*, u.username FROM Comentarios c JOIN Usuario u ON c.id_usuario=u.id_usuario" +
                " WHERE c.id_comentario=?")) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            if (!rs.next()) return Response.status(Status.NOT_FOUND).entity(Map.of("error", "Comentario " + id + " no encontrado")).build();
            return Response.ok(Map.of("comentario", mapCom(rs))).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response crearComentario(Map<String, String> d) {
        if (!d.containsKey("id_usuario") || d.get("id_usuario").isBlank())
            return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Falta id_usuario")).build();
        if (!d.containsKey("id_foto") || d.get("id_foto").isBlank())
            return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Falta id_foto")).build();
        if (!d.containsKey("contenido") || d.get("contenido").isBlank())
            return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Falta contenido")).build();
        int uid = Integer.parseInt(d.get("id_usuario"));
        int idFoto = Integer.parseInt(d.get("id_foto"));
        String contenido = d.get("contenido").trim();
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "INSERT INTO Comentarios (id_usuario, id_foto, contenido) VALUES (?,?,?)",
                Statement.RETURN_GENERATED_KEYS)) {
            ps.setInt(1, uid); ps.setInt(2, idFoto); ps.setString(3, contenido);
            ps.executeUpdate();
            ResultSet keys = ps.getGeneratedKeys();
            int nuevo = keys.next() ? keys.getInt(1) : -1;
            keys.close();
            UsuariosResource.logActividad(con, uid, "comentario", "En foto " + idFoto);
            return Response.status(Status.CREATED).entity(Map.of("mensaje", "Comentario creado", "id_comentario", nuevo)).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    @PUT
    @Path("/{id}")
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response editarComentario(@PathParam("id") int id, Map<String, String> d) {
        if (!d.containsKey("contenido") || d.get("contenido").isBlank())
            return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Falta contenido")).build();
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement("UPDATE Comentarios SET contenido=? WHERE id_comentario=?")) {
            ps.setString(1, d.get("contenido").trim()); ps.setInt(2, id);
            int filas = ps.executeUpdate();
            if (filas == 0) return Response.status(Status.NOT_FOUND).entity(Map.of("error", "Comentario " + id + " no encontrado")).build();
            return Response.ok(Map.of("mensaje", "Comentario actualizado")).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    @DELETE
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response borrarComentario(@PathParam("id") int id) {
        try (Connection con = Conexion.conectar()) {
            PreparedStatement chk = con.prepareStatement("SELECT id_usuario FROM Comentarios WHERE id_comentario=?");
            chk.setInt(1, id);
            ResultSet rs = chk.executeQuery();
            if (!rs.next()) return Response.status(Status.NOT_FOUND).entity(Map.of("error", "Comentario " + id + " no encontrado")).build();
            int uid = rs.getInt("id_usuario");
            rs.close(); chk.close();
            PreparedStatement ps = con.prepareStatement("DELETE FROM Comentarios WHERE id_comentario=?");
            ps.setInt(1, id); ps.executeUpdate(); ps.close();
            UsuariosResource.logActividad(con, uid, "borrado_comentario", "Comentario " + id);
            return Response.ok(Map.of("mensaje", "Comentario eliminado", "id", id)).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    private Map<String, Object> mapCom(ResultSet rs) throws SQLException {
        Map<String, Object> m = new HashMap<>();
        m.put("id_comentario",    rs.getInt("id_comentario"));
        m.put("id_usuario",       rs.getInt("id_usuario"));
        m.put("id_foto",          rs.getInt("id_foto"));
        m.put("contenido",        rs.getString("contenido"));
        m.put("fecha_publicacion", rs.getString("fecha_publicacion"));
        m.put("username",         rs.getString("username"));
        return m;
    }
}
