# RedSocialApp - Android

App Android para la red social, conectada al backend Java (Jersey/JAX-RS) desplegado en Tomcat.

## Configurar la URL

En `ApiClient.java` cambiar `BASE_URL` según donde esté el servidor:

```
// emulador android studio
http://10.0.2.2:8080/red-social/api/

// movil fisico (poner la ip del pc)
http://192.168.1.XXX:8080/red-social/api/
```

El WAR del backend se llama `red-social` (ver pom.xml), así que la ruta es siempre `/red-social/api/`.

## Compilar el backend

```bash
cd java/
mvn clean package
# copiar red-social.war a webapps/ de tomcat
```

La base de datos tiene que llamarse `red_social` y el usuario/contraseña está en `Conexion.java`.

## Cambios respecto a la versión anterior

- `ApiClient.java` - corregida la URL base, antes apuntaba a un php que no existe
- `ApiService.java` - añadidos los endpoints que faltaban (datos_personales, actividad, los PUT)
- `ApiModels.java` - añadidas las clases que faltaban para mapear las respuestas del backend Java
- `PerfilFragment.java` - arreglado el parseado del usuario que a veces venía en "usuario" y a veces en "datos"
