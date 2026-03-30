package redsocial;

import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import jakarta.ws.rs.core.Response.Status;
import java.sql.*;

@Path("/fotos")
public class FotosResource {

    @GET
    @Produces(MediaType.APPLICATION_JSON)
    public Response getFotos() {

        StringBuilder json = new StringBuilder();
        json.append("{\"fotos\":[");

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "SELECT f.*, u.username FROM Fotos f JOIN Usuario u ON f.id_usuario=u.id_usuario ORDER BY f.fecha_subida DESC");
             ResultSet rs = ps.executeQuery()) {

            boolean primero = true;

            while (rs.next()) {
                if (!primero) json.append(",");
                json.append(mapFoto(rs, true));
                primero = false;
            }

        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }

        json.append("]}");
        return Response.ok(json.toString()).build();
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

            if (!rs.next()) {
                return Response.status(Status.NOT_FOUND)
                        .entity("{\"error\":\"Foto " + id + " no encontrada\"}")
                        .build();
            }

            return Response.ok("{\"foto\":" + mapFoto(rs, true) + "}").build();

        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    @GET
    @Path("/usuario/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response fotosUsuario(@PathParam("id") int id) {

        StringBuilder json = new StringBuilder();
        json.append("{\"fotos\":[");

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "SELECT * FROM Fotos WHERE id_usuario=? ORDER BY fecha_subida DESC")) {

            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();

            boolean primero = true;

            while (rs.next()) {
                if (!primero) json.append(",");
                json.append(mapFoto(rs, false));
                primero = false;
            }

        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }

        json.append("]}");
        return Response.ok(json.toString()).build();
    }

    @POST
    @Consumes(MediaType.APPLICATION_FORM_URLENCODED)
    @Produces(MediaType.APPLICATION_JSON)
    public Response subirFoto(@FormParam("id_usuario") int uid,
                              @FormParam("url_foto") String url,
                              @FormParam("descripcion") String descripcion,
                              @FormParam("tipo_foto") String tipo) {

        if (uid <= 0) {
            return Response.status(Status.BAD_REQUEST)
                    .entity("{\"error\":\"ID inválido\"}")
                    .build();
        }

        if (url == null || url.isBlank()) {
            return Response.status(Status.BAD_REQUEST)
                    .entity("{\"error\":\"Falta url_foto\"}")
                    .build();
        }

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "INSERT INTO Fotos (id_usuario, url_foto, descripcion, tipo_foto) VALUES (?,?,?,?)",
                Statement.RETURN_GENERATED_KEYS)) {

            ps.setInt(1, uid);
            ps.setString(2, url.trim());
            ps.setString(3, descripcion);
            ps.setString(4, tipo);

            ps.executeUpdate();

            ResultSet keys = ps.getGeneratedKeys();
            int nuevo = keys.next() ? keys.getInt(1) : -1;
            keys.close();

            UsuariosResource.logActividad(con, uid, "subida_foto", "Foto: " + url);

            return Response.status(Status.CREATED)
                    .entity("{\"mensaje\":\"Foto subida\",\"id_foto\":" + nuevo + "}")
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
    public Response editarFoto(@PathParam("id") int id,
                                @FormParam("url_foto") String url,
                                @FormParam("descripcion") String descripcion,
                                @FormParam("tipo_foto") String tipo) {

        try (Connection con = Conexion.conectar();
             PreparedStatement ps = con.prepareStatement(
                "UPDATE Fotos SET url_foto=?, descripcion=?, tipo_foto=? WHERE id_foto=?")) {

            ps.setString(1, url);
            ps.setString(2, descripcion);
            ps.setString(3, tipo);
            ps.setInt(4, id);

            int filas = ps.executeUpdate();

            if (filas == 0) {
                return Response.status(Status.NOT_FOUND)
                        .entity("{\"error\":\"Foto " + id + " no encontrada\"}")
                        .build();
            }

            return Response.ok("{\"mensaje\":\"Foto actualizada\"}").build();

        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    @DELETE
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_JSON)
    public Response borrarFoto(@PathParam("id") int id) {

        try (Connection con = Conexion.conectar();
             PreparedStatement chk = con.prepareStatement(
                "SELECT id_usuario, url_foto FROM Fotos WHERE id_foto=?")) {

            chk.setInt(1, id);
            ResultSet rs = chk.executeQuery();

            if (!rs.next()) {
                return Response.status(Status.NOT_FOUND)
                        .entity("{\"error\":\"Foto " + id + " no encontrada\"}")
                        .build();
            }

            int uid = rs.getInt("id_usuario");
            String url = rs.getString("url_foto");

            try (PreparedStatement ps = con.prepareStatement(
                    "DELETE FROM Fotos WHERE id_foto=?")) {

                ps.setInt(1, id);
                ps.executeUpdate();
            }

            UsuariosResource.logActividad(con, uid, "borrado_foto", "Foto: " + url);

            return Response.ok("{\"mensaje\":\"Foto eliminada\",\"id\":" + id + "}").build();

        } catch (SQLException e) {
            return Response.status(Status.INTERNAL_SERVER_ERROR)
                    .entity("{\"error\":\"" + e.getMessage() + "\"}")
                    .build();
        }
    }

    private String mapFoto(ResultSet rs, boolean conUsername) throws SQLException {

        String json = "{"
                + "\"id_foto\":" + rs.getInt("id_foto") + ","
                + "\"id_usuario\":" + rs.getInt("id_usuario") + ","
                + "\"url_foto\":\"" + escapar(rs.getString("url_foto")) + "\","
                + "\"descripcion\":\"" + escapar(rs.getString("descripcion")) + "\","
                + "\"tipo_foto\":\"" + escapar(rs.getString("tipo_foto")) + "\","
                + "\"fecha_subida\":\"" + escapar(rs.getString("fecha_subida")) + "\"";

        if (conUsername) {
            json += ",\"username\":\"" + escapar(rs.getString("username")) + "\"";
        }

        json += "}";

        return json;
    }

    private String escapar(String s) {
        if (s == null) return "";
        return s.replace("\"", "\\\"");
    }
}