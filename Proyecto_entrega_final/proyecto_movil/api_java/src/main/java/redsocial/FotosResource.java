package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import jakarta.ws.rs.core.Response.Status;
import java.sql.*;
import java.util.*;

@Path("/fotos")
public class FotosResource {

    @GET
    @Produces(MediaType.APPLICATION_JSON)
    public Response getFotos() {
        List<Map<String, Object>> fotos = new ArrayList<>();
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "SELECT f.*, u.username FROM Fotos f JOIN Usuario u ON f.id_usuario=u.id_usuario ORDER BY f.fecha_subida DESC");
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) fotos.add(mapFoto(rs, true));
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
        return Response.ok(Map.of("total", fotos.size(), "fotos", fotos)).build();
    }

    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response getFoto(@PathParam("id") int id) {
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "SELECT f.*, u.username FROM Fotos f JOIN Usuario u ON f.id_usuario=u.id_usuario WHERE f.id_foto=?")) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            if (!rs.next()) return Response.status(Status.NOT_FOUND).entity(Map.of("error", "Foto " + id + " no encontrada")).build();
            return Response.ok(Map.of("foto", mapFoto(rs, true))).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    @GET
    @Path("/usuario/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response fotosUsuario(@PathParam("id") int id) {
        List<Map<String, Object>> fotos = new ArrayList<>();
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement("SELECT * FROM Fotos WHERE id_usuario=? ORDER BY fecha_subida DESC")) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            while (rs.next()) fotos.add(mapFoto(rs, false));
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
        return Response.ok(Map.of("total", fotos.size(), "fotos", fotos)).build();
    }

    @POST
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response subirFoto(Map<String, String> d) {
        if (!d.containsKey("id_usuario") || d.get("id_usuario").isBlank())
            return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Falta id_usuario")).build();
        if (!d.containsKey("url_foto") || d.get("url_foto").isBlank())
            return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Falta url_foto")).build();
        int uid = Integer.parseInt(d.get("id_usuario"));
        String url = d.get("url_foto").trim();
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "INSERT INTO Fotos (id_usuario, url_foto, descripcion, tipo_foto) VALUES (?,?,?,?)",
                Statement.RETURN_GENERATED_KEYS)) {
            ps.setInt(1, uid); ps.setString(2, url);
            ps.setString(3, d.get("descripcion")); ps.setString(4, d.get("tipo_foto"));
            ps.executeUpdate();
            ResultSet keys = ps.getGeneratedKeys();
            int nuevo = keys.next() ? keys.getInt(1) : -1;
            keys.close();
            UsuariosResource.logActividad(con, uid, "subida_foto", "Foto: " + url);
            return Response.status(Status.CREATED).entity(Map.of("mensaje", "Foto subida", "id_foto", nuevo)).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    @PUT
    @Path("/{id}")
    @Consumes(MediaType.APPLICATION_JSON)
    @Produces(MediaType.APPLICATION_JSON)
    public Response editarFoto(@PathParam("id") int id, Map<String, String> d) {
        StringBuilder sb = new StringBuilder("UPDATE Fotos SET ");
        List<Object> vals = new ArrayList<>();
        if (d.containsKey("url_foto"))    { sb.append("url_foto=?, ");    vals.add(d.get("url_foto")); }
        if (d.containsKey("descripcion")) { sb.append("descripcion=?, "); vals.add(d.get("descripcion")); }
        if (d.containsKey("tipo_foto"))   { sb.append("tipo_foto=?, ");   vals.add(d.get("tipo_foto")); }
        if (vals.isEmpty()) return Response.status(Status.BAD_REQUEST).entity(Map.of("error", "Nada que actualizar")).build();
        vals.add(id);
        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(sb.substring(0, sb.length() - 2) + " WHERE id_foto=?")) {
            for (int i = 0; i < vals.size(); i++) ps.setObject(i + 1, vals.get(i));
            ps.executeUpdate();
            return Response.ok(Map.of("mensaje", "Foto actualizada")).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    @DELETE
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response borrarFoto(@PathParam("id") int id) {
        try (Connection con = Conexion.conectar()) {
            PreparedStatement chk = con.prepareStatement("SELECT id_usuario, url_foto FROM Fotos WHERE id_foto=?");
            chk.setInt(1, id);
            ResultSet rs = chk.executeQuery();
            if (!rs.next()) return Response.status(Status.NOT_FOUND).entity(Map.of("error", "Foto " + id + " no encontrada")).build();
            int uid = rs.getInt("id_usuario"); String url = rs.getString("url_foto");
            rs.close(); chk.close();
            PreparedStatement ps = con.prepareStatement("DELETE FROM Fotos WHERE id_foto=?");
            ps.setInt(1, id); ps.executeUpdate(); ps.close();
            UsuariosResource.logActividad(con, uid, "borrado_foto", "Foto: " + url);
            return Response.ok(Map.of("mensaje", "Foto eliminada", "id", id)).build();
        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR).entity(Map.of("error", e.getMessage())).build();
        }
    }

    private Map<String, Object> mapFoto(ResultSet rs, boolean conUsername) throws SQLException {
        Map<String, Object> m = new HashMap<>();
        m.put("id_foto",      rs.getInt("id_foto"));
        m.put("id_usuario",   rs.getInt("id_usuario"));
        m.put("url_foto",     rs.getString("url_foto"));
        m.put("descripcion",  rs.getString("descripcion"));
        m.put("tipo_foto",    rs.getString("tipo_foto"));
        m.put("fecha_subida", rs.getString("fecha_subida"));
        if (conUsername) m.put("username", rs.getString("username"));
        return m;
    }
}
