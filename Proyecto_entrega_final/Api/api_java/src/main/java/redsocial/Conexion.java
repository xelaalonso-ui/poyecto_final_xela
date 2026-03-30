package redsocial;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class Conexion {

    private static final String URL = "jdbc:mysql://localhost:3306/red_social"
            + "?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=UTC";

    public static Connection conectar() {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            return DriverManager.getConnection(URL, "root", "");
        } catch (ClassNotFoundException | SQLException e) {
            throw new RuntimeException("Error al conectar con la BD: " + e.getMessage());
        }
    }
}
