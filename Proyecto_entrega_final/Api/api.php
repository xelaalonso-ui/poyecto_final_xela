<?php
error_reporting(0);
ini_set('display_errors', '0');

set_exception_handler(function(Throwable $e) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(['error' => $e->getMessage()]);
    exit();
});

register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(['error' => 'Error interno']);
        exit();
    }
});

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'red_social');

function conectar() {
    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno()) {
        responder(500, ['error' => 'Error de conexion: ' . mysqli_connect_error()]);
    }
    mysqli_set_charset($db, 'utf8');
    return $db;
}

function responder(int $code, array $data): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

function body(): array {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

function campos(array $d, array $requeridos): void {
    foreach ($requeridos as $c) {
        if (empty($d[$c])) {
            responder(400, ['error' => "Falta el campo: $c"]);
        }
    }
}

function logActividad($db, int $uid, string $tipo, string $desc): void {
    $stmt = mysqli_prepare($db, "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) VALUES (?,?,?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'iss', $uid, $tipo, $desc);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

$uri    = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$partes = array_values(array_filter(explode('/', $uri)));
$metodo = $_SERVER['REQUEST_METHOD'];
$input  = body();

$validos = ['usuarios', 'login', 'datos_personales', 'fotos', 'comentarios', 'actividad'];
$recurso = null;
$sub     = null;
$id      = null;

foreach ($partes as $i => $p) {
    if (in_array($p, $validos)) {
        $recurso = $p;
        $sig = $partes[$i + 1] ?? null;
        if ($sig !== null && !is_numeric($sig)) {
            $sub = $sig;
            $id  = $partes[$i + 2] ?? null;
        } else {
            $id = $sig;
        }
        break;
    }
}

if (!$recurso) {
    $recurso = $_GET['recurso'] ?? null;
    $id      = $_GET['id']      ?? null;
    $sub     = $_GET['sub']     ?? null;
}

switch ($recurso) {
    case 'usuarios':
        switch ($metodo) {
            case 'GET':    $id ? getUsuario((int)$id) : getUsuarios(); break;
            case 'POST':   crearUsuario($input); break;
            case 'PUT':    if (!$id) responder(400, ['error' => 'Falta ID']); editarUsuario((int)$id, $input); break;
            case 'DELETE': if (!$id) responder(400, ['error' => 'Falta ID']); borrarUsuario((int)$id); break;
            default:       responder(405, ['error' => 'Metodo no permitido']);
        }
        break;

    case 'login':
        if ($metodo !== 'POST') responder(405, ['error' => 'Usa POST']);
        login($input);
        break;

    case 'datos_personales':
        switch ($metodo) {
            case 'GET':  if (!$id) responder(400, ['error' => 'Falta id_usuario']); getDatos((int)$id); break;
            case 'POST': crearDatos($input); break;
            case 'PUT':  if (!$id) responder(400, ['error' => 'Falta id_usuario']); editarDatos((int)$id, $input); break;
            default:     responder(405, ['error' => 'Metodo no permitido']);
        }
        break;

    case 'fotos':
        switch ($metodo) {
            case 'GET':
                if ($sub === 'usuario' && $id) fotosDeUsuario((int)$id);
                elseif ($id)                   getFoto((int)$id);
                else                           getFotos();
                break;
            case 'POST':   subirFoto($input); break;
            case 'PUT':    if (!$id) responder(400, ['error' => 'Falta id_foto']); editarFoto((int)$id, $input); break;
            case 'DELETE': if (!$id) responder(400, ['error' => 'Falta id_foto']); borrarFoto((int)$id); break;
            default:       responder(405, ['error' => 'Metodo no permitido']);
        }
        break;

    case 'comentarios':
        switch ($metodo) {
            case 'GET':
                if ($sub === 'foto' && $id) comentariosDeFoto((int)$id);
                elseif ($id)               getComentario((int)$id);
                else                       responder(400, ['error' => 'Indica un ID']);
                break;
            case 'POST':   crearComentario($input); break;
            case 'PUT':    if (!$id) responder(400, ['error' => 'Falta ID']); editarComentario((int)$id, $input); break;
            case 'DELETE': if (!$id) responder(400, ['error' => 'Falta ID']); borrarComentario((int)$id); break;
            default:       responder(405, ['error' => 'Metodo no permitido']);
        }
        break;

    case 'actividad':
        switch ($metodo) {
            case 'GET':    if (!$id) responder(400, ['error' => 'Falta id_usuario']); getActividad((int)$id); break;
            case 'POST':   crearActividad($input); break;
            case 'DELETE': if (!$id) responder(400, ['error' => 'Falta ID']); borrarActividad((int)$id); break;
            default:       responder(405, ['error' => 'Metodo no permitido']);
        }
        break;

    default:
        responder(404, ['error' => 'Ruta no encontrada']);
}


// ============================================================
//  USUARIOS
// ============================================================

function getUsuarios() {
    $db  = conectar();
    $res = mysqli_query($db, "SELECT id_usuario, username, email, fecha_registro, estado_cuenta, ultimo_login FROM Usuario ORDER BY fecha_registro DESC");
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    mysqli_close($db);
    responder(200, ['total' => count($lista), 'usuarios' => $lista]);
}

function getUsuario(int $id) {
    $db   = conectar();
    $stmt = mysqli_prepare($db,
        "SELECT u.id_usuario, u.username, u.email, u.fecha_registro, u.estado_cuenta, u.ultimo_login,
                d.nombre, d.apellido, d.fecha_nacimiento, d.genero, d.direccion, d.telefono
         FROM Usuario u
         LEFT JOIN Datos_personales d ON u.id_usuario = d.id_usuario
         WHERE u.id_usuario = ?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_close($db);
    if (!$fila) responder(404, ['error' => "Usuario $id no encontrado"]);
    responder(200, ['usuario' => $fila]);
}

function crearUsuario(array $d) {
    campos($d, ['username', 'email', 'password']);

    $username = trim($d['username']);
    $email    = strtolower(trim($d['email']));
    $hash     = password_hash(trim($d['password']), PASSWORD_DEFAULT);
    $estado   = $d['estado_cuenta'] ?? 'activo';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        responder(400, ['error' => 'Email no valido']);

    $db  = conectar();
    $chk = mysqli_prepare($db, "SELECT id_usuario FROM Usuario WHERE email=? OR username=?");
    mysqli_stmt_bind_param($chk, 'ss', $email, $username);
    mysqli_stmt_execute($chk);
    mysqli_stmt_store_result($chk);
    if (mysqli_stmt_num_rows($chk) > 0) {
        mysqli_close($db);
        responder(409, ['error' => 'El username o email ya existe']);
    }
    mysqli_stmt_close($chk);

    $stmt = mysqli_prepare($db, "INSERT INTO Usuario (username, email, password_hash, estado_cuenta) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $hash, $estado);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    $nuevo = mysqli_insert_id($db);

    logActividad($db, $nuevo, 'registro', "Nuevo usuario: $username");
    mysqli_close($db);
    responder(201, ['mensaje' => 'Usuario creado', 'id_usuario' => $nuevo]);
}

function editarUsuario(int $id, array $d) {
    $db  = conectar();
    $chk = mysqli_prepare($db, "SELECT id_usuario FROM Usuario WHERE id_usuario=?");
    mysqli_stmt_bind_param($chk, 'i', $id);
    mysqli_stmt_execute($chk);
    mysqli_stmt_store_result($chk);
    if (mysqli_stmt_num_rows($chk) === 0) {
        mysqli_close($db);
        responder(404, ['error' => "Usuario $id no encontrado"]);
    }
    mysqli_stmt_close($chk);

    $campos = []; $vals = []; $tipos = '';
    if (!empty($d['username']))     { $campos[] = "username=?";      $vals[] = trim($d['username']); $tipos .= 's'; }
    if (!empty($d['email']))        { $campos[] = "email=?";         $vals[] = strtolower(trim($d['email'])); $tipos .= 's'; }
    if (!empty($d['password']))     { $campos[] = "password_hash=?"; $vals[] = password_hash(trim($d['password']), PASSWORD_DEFAULT); $tipos .= 's'; }
    if (isset($d['estado_cuenta'])) { $campos[] = "estado_cuenta=?"; $vals[] = $d['estado_cuenta']; $tipos .= 's'; }

    if (empty($campos)) {
        mysqli_close($db);
        responder(400, ['error' => 'Nada que actualizar']);
    }

    $tipos .= 'i'; $vals[] = $id;
    $stmt = mysqli_prepare($db, "UPDATE Usuario SET " . implode(',', $campos) . " WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt, $tipos, ...$vals);

    try {
        if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    } catch (mysqli_sql_exception $e) {
        mysqli_close($db);
        responder(409, ['error' => 'Username o email duplicado']);
    }

    logActividad($db, $id, 'edicion_perfil', 'Perfil actualizado');
    mysqli_close($db);
    responder(200, ['mensaje' => 'Usuario actualizado']);
}

function borrarUsuario(int $id) {
    $db  = conectar();
    $chk = mysqli_prepare($db, "SELECT username FROM Usuario WHERE id_usuario=?");
    mysqli_stmt_bind_param($chk, 'i', $id);
    mysqli_stmt_execute($chk);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($chk));
    if (!$fila) {
        mysqli_close($db);
        responder(404, ['error' => "Usuario $id no encontrado"]);
    }

    $stmt = mysqli_prepare($db, "DELETE FROM Usuario WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    mysqli_close($db);
    responder(200, ['mensaje' => "Usuario '{$fila['username']}' eliminado", 'id' => $id]);
}

function login(array $d) {
    campos($d, ['email', 'password']);
    $email = strtolower(trim($d['email']));
    $db    = conectar();

    $stmt = mysqli_prepare($db, "SELECT id_usuario, username, email, password_hash, estado_cuenta FROM Usuario WHERE email=?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$fila || !password_verify(trim($d['password']), $fila['password_hash'])) {
        mysqli_close($db);
        responder(401, ['error' => 'Credenciales incorrectas']);
    }

    $upd = mysqli_prepare($db, "UPDATE Usuario SET ultimo_login=NOW() WHERE id_usuario=?");
    mysqli_stmt_bind_param($upd, 'i', $fila['id_usuario']);
    mysqli_stmt_execute($upd);

    logActividad($db, $fila['id_usuario'], 'login', 'Sesion iniciada');
    mysqli_close($db);

    unset($fila['password_hash']);
    responder(200, ['mensaje' => 'Login correcto', 'usuario' => $fila]);
}


// ============================================================
//  DATOS PERSONALES
// ============================================================

function getDatos(int $id_usuario) {
    $db   = conectar();
    $stmt = mysqli_prepare($db, "SELECT * FROM Datos_personales WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_close($db);
    if (!$fila) responder(404, ['error' => "Sin datos para el usuario $id_usuario"]);
    responder(200, ['datos_personales' => $fila]);
}

function crearDatos(array $d) {
    campos($d, ['id_usuario']);
    $id   = (int)$d['id_usuario'];
    $nom  = $d['nombre']           ?? null;
    $ape  = $d['apellido']         ?? null;
    $fnac = $d['fecha_nacimiento'] ?? null;
    $gen  = $d['genero']           ?? null;
    $dir  = $d['direccion']        ?? null;
    $tel  = $d['telefono']         ?? null;

    $db   = conectar();
    $stmt = mysqli_prepare($db,
        "INSERT INTO Datos_personales (id_usuario,nombre,apellido,fecha_nacimiento,genero,direccion,telefono)
         VALUES (?,?,?,?,?,?,?)
         ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), apellido=VALUES(apellido),
         fecha_nacimiento=VALUES(fecha_nacimiento), genero=VALUES(genero),
         direccion=VALUES(direccion), telefono=VALUES(telefono)"
    );
    mysqli_stmt_bind_param($stmt, 'issssss', $id, $nom, $ape, $fnac, $gen, $dir, $tel);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    mysqli_close($db);
    responder(201, ['mensaje' => 'Datos guardados']);
}

function editarDatos(int $id_usuario, array $d) {
    $db     = conectar();
    $campos = []; $vals = []; $tipos = '';

    if (isset($d['nombre']))           { $campos[] = "nombre=?";           $vals[] = $d['nombre'];           $tipos .= 's'; }
    if (isset($d['apellido']))         { $campos[] = "apellido=?";         $vals[] = $d['apellido'];         $tipos .= 's'; }
    if (isset($d['fecha_nacimiento'])) { $campos[] = "fecha_nacimiento=?"; $vals[] = $d['fecha_nacimiento']; $tipos .= 's'; }
    if (isset($d['genero']))           { $campos[] = "genero=?";           $vals[] = $d['genero'];           $tipos .= 's'; }
    if (isset($d['direccion']))        { $campos[] = "direccion=?";        $vals[] = $d['direccion'];        $tipos .= 's'; }
    if (isset($d['telefono']))         { $campos[] = "telefono=?";         $vals[] = $d['telefono'];         $tipos .= 's'; }

    if (empty($campos)) {
        mysqli_close($db);
        responder(400, ['error' => 'Nada que actualizar']);
    }

    $tipos .= 'i'; $vals[] = $id_usuario;
    $stmt = mysqli_prepare($db, "UPDATE Datos_personales SET " . implode(',', $campos) . " WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt, $tipos, ...$vals);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    mysqli_close($db);
    responder(200, ['mensaje' => 'Datos actualizados']);
}


// ============================================================
//  FOTOS
// ============================================================

function getFotos() {
    $db  = conectar();
    $res = mysqli_query($db,
        "SELECT f.*, u.username FROM Fotos f
         JOIN Usuario u ON f.id_usuario = u.id_usuario
         ORDER BY f.fecha_subida DESC"
    );
    $fotos = [];
    while ($f = mysqli_fetch_assoc($res)) $fotos[] = $f;
    mysqli_close($db);
    responder(200, ['total' => count($fotos), 'fotos' => $fotos]);
}

function getFoto(int $id) {
    $db   = conectar();
    $stmt = mysqli_prepare($db,
        "SELECT f.*, u.username FROM Fotos f
         JOIN Usuario u ON f.id_usuario = u.id_usuario
         WHERE f.id_foto=?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_close($db);
    if (!$fila) responder(404, ['error' => "Foto $id no encontrada"]);
    responder(200, ['foto' => $fila]);
}

function fotosDeUsuario(int $id_usuario) {
    $db   = conectar();
    $stmt = mysqli_prepare($db, "SELECT * FROM Fotos WHERE id_usuario=? ORDER BY fecha_subida DESC");
    mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
    mysqli_stmt_execute($stmt);
    $res   = mysqli_stmt_get_result($stmt);
    $fotos = [];
    while ($f = mysqli_fetch_assoc($res)) $fotos[] = $f;
    mysqli_close($db);
    responder(200, ['total' => count($fotos), 'fotos' => $fotos]);
}

function subirFoto(array $d) {
    campos($d, ['id_usuario', 'url_foto']);
    $uid  = (int)$d['id_usuario'];
    $url  = trim($d['url_foto']);
    $desc = $d['descripcion'] ?? null;
    $tipo = $d['tipo_foto']   ?? null;

    $db   = conectar();
    $stmt = mysqli_prepare($db, "INSERT INTO Fotos (id_usuario, url_foto, descripcion, tipo_foto) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'isss', $uid, $url, $desc, $tipo);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    $nuevo = mysqli_insert_id($db);

    logActividad($db, $uid, 'subida_foto', "Foto: $url");
    mysqli_close($db);
    responder(201, ['mensaje' => 'Foto subida', 'id_foto' => $nuevo]);
}

function editarFoto(int $id, array $d) {
    $db     = conectar();
    $campos = []; $vals = []; $tipos = '';

    if (isset($d['url_foto']))    { $campos[] = "url_foto=?";    $vals[] = $d['url_foto'];    $tipos .= 's'; }
    if (isset($d['descripcion'])) { $campos[] = "descripcion=?"; $vals[] = $d['descripcion']; $tipos .= 's'; }
    if (isset($d['tipo_foto']))   { $campos[] = "tipo_foto=?";   $vals[] = $d['tipo_foto'];   $tipos .= 's'; }

    if (empty($campos)) {
        mysqli_close($db);
        responder(400, ['error' => 'Nada que actualizar']);
    }

    $tipos .= 'i'; $vals[] = $id;
    $stmt = mysqli_prepare($db, "UPDATE Fotos SET " . implode(',', $campos) . " WHERE id_foto=?");
    mysqli_stmt_bind_param($stmt, $tipos, ...$vals);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    mysqli_close($db);
    responder(200, ['mensaje' => 'Foto actualizada']);
}

function borrarFoto(int $id) {
    $db  = conectar();
    $chk = mysqli_prepare($db, "SELECT id_usuario, url_foto FROM Fotos WHERE id_foto=?");
    mysqli_stmt_bind_param($chk, 'i', $id);
    mysqli_stmt_execute($chk);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($chk));
    if (!$fila) {
        mysqli_close($db);
        responder(404, ['error' => "Foto $id no encontrada"]);
    }

    $stmt = mysqli_prepare($db, "DELETE FROM Fotos WHERE id_foto=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);

    logActividad($db, $fila['id_usuario'], 'borrado_foto', "Foto: {$fila['url_foto']}");
    mysqli_close($db);
    responder(200, ['mensaje' => 'Foto eliminada', 'id' => $id]);
}


// ============================================================
//  COMENTARIOS
// ============================================================

function getComentario(int $id) {
    $db   = conectar();
    $stmt = mysqli_prepare($db,
        "SELECT c.*, u.username FROM Comentarios c
         JOIN Usuario u ON c.id_usuario = u.id_usuario
         WHERE c.id_comentario=?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_close($db);
    if (!$fila) responder(404, ['error' => "Comentario $id no encontrado"]);
    responder(200, ['comentario' => $fila]);
}

function comentariosDeFoto(int $id_foto) {
    $db   = conectar();
    $stmt = mysqli_prepare($db,
        "SELECT c.*, u.username FROM Comentarios c
         JOIN Usuario u ON c.id_usuario = u.id_usuario
         WHERE c.id_foto=? ORDER BY c.fecha_publicacion ASC"
    );
    mysqli_stmt_bind_param($stmt, 'i', $id_foto);
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $coms = [];
    while ($f = mysqli_fetch_assoc($res)) $coms[] = $f;
    mysqli_close($db);
    responder(200, ['total' => count($coms), 'comentarios' => $coms]);
}

function crearComentario(array $d) {
    campos($d, ['id_usuario', 'id_foto', 'contenido']);
    $uid      = (int)$d['id_usuario'];
    $id_foto  = (int)$d['id_foto'];
    $contenido = trim($d['contenido']);

    $db   = conectar();
    $stmt = mysqli_prepare($db, "INSERT INTO Comentarios (id_usuario, id_foto, contenido) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, 'iis', $uid, $id_foto, $contenido);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    $nuevo = mysqli_insert_id($db);

    logActividad($db, $uid, 'comentario', "En foto $id_foto");
    mysqli_close($db);
    responder(201, ['mensaje' => 'Comentario creado', 'id_comentario' => $nuevo]);
}

function editarComentario(int $id, array $d) {
    if (empty($d['contenido'])) responder(400, ['error' => 'Falta el contenido']);
    $contenido = trim($d['contenido']);

    $db   = conectar();
    $stmt = mysqli_prepare($db, "UPDATE Comentarios SET contenido=? WHERE id_comentario=?");
    mysqli_stmt_bind_param($stmt, 'si', $contenido, $id);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    if (mysqli_stmt_affected_rows($stmt) === 0) responder(404, ['error' => "Comentario $id no encontrado"]);
    mysqli_close($db);
    responder(200, ['mensaje' => 'Comentario actualizado']);
}

function borrarComentario(int $id) {
    $db  = conectar();
    $chk = mysqli_prepare($db, "SELECT id_usuario FROM Comentarios WHERE id_comentario=?");
    mysqli_stmt_bind_param($chk, 'i', $id);
    mysqli_stmt_execute($chk);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($chk));
    if (!$fila) {
        mysqli_close($db);
        responder(404, ['error' => "Comentario $id no encontrado"]);
    }

    $stmt = mysqli_prepare($db, "DELETE FROM Comentarios WHERE id_comentario=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);

    logActividad($db, $fila['id_usuario'], 'borrado_comentario', "Comentario $id");
    mysqli_close($db);
    responder(200, ['mensaje' => 'Comentario eliminado', 'id' => $id]);
}


// ============================================================
//  ACTIVIDAD
// ============================================================

function getActividad(int $id_usuario) {
    $db   = conectar();
    $stmt = mysqli_prepare($db, "SELECT * FROM Actividad WHERE id_usuario=? ORDER BY fecha_actividad DESC");
    mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $acts = [];
    while ($f = mysqli_fetch_assoc($res)) $acts[] = $f;
    mysqli_close($db);
    responder(200, ['total' => count($acts), 'actividad' => $acts]);
}

function crearActividad(array $d) {
    campos($d, ['id_usuario', 'tipo_actividad']);
    $uid  = (int)$d['id_usuario'];
    $tipo = trim($d['tipo_actividad']);
    $desc = $d['descripcion'] ?? null;

    $db   = conectar();
    $stmt = mysqli_prepare($db, "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, 'iss', $uid, $tipo, $desc);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    $nuevo = mysqli_insert_id($db);
    mysqli_close($db);
    responder(201, ['mensaje' => 'Actividad registrada', 'id_actividad' => $nuevo]);
}

function borrarActividad(int $id) {
    $db   = conectar();
    $stmt = mysqli_prepare($db, "DELETE FROM Actividad WHERE id_actividad=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) responder(500, ['error' => mysqli_stmt_error($stmt)]);
    if (mysqli_stmt_affected_rows($stmt) === 0) responder(404, ['error' => "Actividad $id no encontrada"]);
    mysqli_close($db);
    responder(200, ['mensaje' => 'Actividad eliminada', 'id' => $id]);
}
