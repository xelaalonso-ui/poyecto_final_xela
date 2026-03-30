<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ======================
// CONFIG
// ======================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'red_social');

function conectar() {
    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db) {
        http_response_code(500);
        echo json_encode(['error' => 'Error de conexión']);
        exit();
    }
    mysqli_set_charset($db, 'utf8');
    return $db;
}

function responder($codigo, $data) {
    http_response_code($codigo);
    echo json_encode($data);
    exit();
}

function obtenerBody() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// ======================
// RUTAS
// ======================
$uri = trim($_SERVER['REQUEST_URI'], '/');
$partes = explode('/', $uri);
$recurso = $partes[0] ?? null;
$id      = isset($partes[1]) ? (int)$partes[1] : null;
$sub     = $partes[2] ?? null;

switch ($recurso) {
    case 'usuarios':
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':    $id ? getUsuario($id) : getUsuarios(); break;
            case 'POST':   crearUsuario(obtenerBody()); break;
            case 'PUT':    $id ? editarUsuario($id, obtenerBody()) : responder(400, ['error'=>'Falta ID']); break;
            case 'DELETE': $id ? borrarUsuario($id) : responder(400, ['error'=>'Falta ID']); break;
            default:       responder(405, ['error'=>'Método no permitido']);
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') login(obtenerBody());
        else responder(405, ['error'=>'Usa POST']);
        break;

    case 'datos_personales':
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':  $id ? getDatos($id) : responder(400, ['error'=>'Falta id_usuario']); break;
            case 'POST': crearDatos(obtenerBody()); break;
            case 'PUT':  $id ? editarDatos($id, obtenerBody()) : responder(400, ['error'=>'Falta id_usuario']); break;
            default:     responder(405, ['error'=>'Método no permitido']);
        }
        break;

    case 'fotos':
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if ($sub === 'usuario' && $id) fotosDeUsuario($id);
                elseif ($id) getFoto($id);
                else getFotos();
                break;
            case 'POST': subirFoto(obtenerBody()); break;
            case 'PUT':  $id ? editarFoto($id, obtenerBody()) : responder(400, ['error'=>'Falta id_foto']); break;
            case 'DELETE': $id ? borrarFoto($id) : responder(400, ['error'=>'Falta id_foto']); break;
            default: responder(405, ['error'=>'Método no permitido']);
        }
        break;

    case 'comentarios':
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if ($sub === 'foto' && $id) comentariosDeFoto($id);
                elseif ($id) getComentario($id);
                else responder(400, ['error'=>'Indica un ID']);
                break;
            case 'POST': crearComentario(obtenerBody()); break;
            case 'PUT':  $id ? editarComentario($id, obtenerBody()) : responder(400, ['error'=>'Falta ID']); break;
            case 'DELETE': $id ? borrarComentario($id) : responder(400, ['error'=>'Falta ID']); break;
            default: responder(405, ['error'=>'Método no permitido']);
        }
        break;

    case 'actividad':
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':  $id ? getActividad($id) : responder(400, ['error'=>'Falta id_usuario']); break;
            case 'POST': crearActividad(obtenerBody()); break;
            case 'DELETE': $id ? borrarActividad($id) : responder(400, ['error'=>'Falta ID']); break;
            default: responder(405, ['error'=>'Método no permitido']);
        }
        break;

    default:
        responder(404, ['error'=>'Ruta no encontrada']);
}

// ======================
// FUNCIONES GENERALES
// ======================
function logActividad($db, $uid, $tipo, $desc) {
    $stmt = mysqli_prepare($db, "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, 'iss', $uid, $tipo, $desc);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// ======================
// USUARIOS
// ======================
function getUsuarios() {
    $db = conectar();
    $res = mysqli_query($db, "SELECT id_usuario, username, email, estado_cuenta FROM Usuario");
    $usuarios = [];
    while ($fila = mysqli_fetch_assoc($res)) $usuarios[] = $fila;
    mysqli_close($db);
    responder(200, ['usuarios'=>$usuarios]);
}

function getUsuario($id) {
    $db = conectar();
    $stmt = mysqli_prepare($db, "SELECT id_usuario, username, email, estado_cuenta FROM Usuario WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($res);
    mysqli_close($db);
    if (!$usuario) responder(404, ['error'=>'Usuario no encontrado']);
    responder(200, ['usuario'=>$usuario]);
}

function crearUsuario($d) {
    if (empty($d['username']) || empty($d['email']) || empty($d['password'])) responder(400,['error'=>'Faltan campos']);
    $db = conectar();
    $username = trim($d['username']);
    $email = strtolower(trim($d['email']));
    $password = password_hash(trim($d['password']), PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($db, "INSERT INTO Usuario (username,email,password_hash) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt,'sss',$username,$email,$password);
    if (!mysqli_stmt_execute($stmt)) responder(500,['error'=>'Error al crear usuario']);
    $id = mysqli_insert_id($db);
    logActividad($db, $id, 'registro', "Usuario $username registrado");
    mysqli_close($db);
    responder(201,['mensaje'=>'Usuario creado','id_usuario'=>$id]);
}

function editarUsuario($id,$d){
    $db = conectar();
    $campos=[];$vals=[];$tipos='';
    if (!empty($d['username'])) { $campos[]="username=?"; $vals[]=$d['username']; $tipos.='s'; }
    if (!empty($d['email'])) { $campos[]="email=?"; $vals[]=strtolower($d['email']); $tipos.='s'; }
    if (!empty($d['password'])) { $campos[]="password_hash=?"; $vals[]=password_hash($d['password'],PASSWORD_DEFAULT); $tipos.='s'; }
    if (empty($campos)) responder(400,['error'=>'Nada que actualizar']);
    $tipos.='i'; $vals[]=$id;
    $stmt=mysqli_prepare($db,"UPDATE Usuario SET ".implode(',',$campos)." WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt,$tipos,...$vals);
    mysqli_stmt_execute($stmt);
    logActividad($db, $id, 'edicion_perfil','Perfil actualizado');
    mysqli_close($db);
    responder(200,['mensaje'=>'Usuario actualizado']);
}

function borrarUsuario($id){
    $db = conectar();
    $stmt = mysqli_prepare($db,"SELECT username FROM Usuario WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    $res=mysqli_stmt_get_result($stmt);
    $usuario=mysqli_fetch_assoc($res);
    if (!$usuario) { mysqli_close($db); responder(404,['error'=>'Usuario no encontrado']); }
    $stmt=mysqli_prepare($db,"DELETE FROM Usuario WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    logActividad($db, $id, 'borrado_usuario',"Usuario {$usuario['username']} eliminado");
    mysqli_close($db);
    responder(200,['mensaje'=>'Usuario eliminado']);
}

// ======================
// LOGIN
// ======================
function login($d){
    if (empty($d['email'])||empty($d['password'])) responder(400,['error'=>'Faltan campos']);
    $db=conectar();
    $email=strtolower(trim($d['email']));
    $password=trim($d['password']);
    $stmt=mysqli_prepare($db,"SELECT id_usuario, username, email, password_hash FROM Usuario WHERE email=?");
    mysqli_stmt_bind_param($stmt,'s',$email);
    mysqli_stmt_execute($stmt);
    $res=mysqli_stmt_get_result($stmt);
    $usuario=mysqli_fetch_assoc($res);
    if (!$usuario || !password_verify($password,$usuario['password_hash'])) { mysqli_close($db); responder(401,['error'=>'Credenciales incorrectas']); }
    unset($usuario['password_hash']);
    logActividad($db, $usuario['id_usuario'],'login','Sesion iniciada');
    mysqli_close($db);
    responder(200,['mensaje'=>'Login correcto','usuario'=>$usuario]);
}

// ======================
// DATOS PERSONALES
// ======================
function getDatos($id){
    $db=conectar();
    $stmt=mysqli_prepare($db,"SELECT * FROM Datos_personales WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    $res=mysqli_stmt_get_result($stmt);
    $fila=mysqli_fetch_assoc($res);
    mysqli_close($db);
    if (!$fila) responder(404,['error'=>'Datos no encontrados']);
    responder(200,['datos_personales'=>$fila]);
}

function crearDatos($d){
    if (empty($d['id_usuario'])) responder(400,['error'=>'Falta id_usuario']);
    $db=conectar();
    $stmt=mysqli_prepare($db,"INSERT INTO Datos_personales (id_usuario,nombre,apellido,fecha_nacimiento,genero,direccion,telefono) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE nombre=VALUES(nombre),apellido=VALUES(apellido),fecha_nacimiento=VALUES(fecha_nacimiento),genero=VALUES(genero),direccion=VALUES(direccion),telefono=VALUES(telefono)");
    mysqli_stmt_bind_param($stmt,'issssss',$d['id_usuario'],$d['nombre'],$d['apellido'],$d['fecha_nacimiento'],$d['genero'],$d['direccion'],$d['telefono']);
    mysqli_stmt_execute($stmt);
    mysqli_close($db);
    responder(201,['mensaje'=>'Datos guardados']);
}

function editarDatos($id,$d){
    $db=conectar(); $campos=[];$vals=[];$tipos='';
    foreach(['nombre','apellido','fecha_nacimiento','genero','direccion','telefono'] as $c){
        if(isset($d[$c])) { $campos[]="$c=?"; $vals[]=$d[$c]; $tipos.='s'; }
    }
    if(empty($campos)) { mysqli_close($db); responder(400,['error'=>'Nada que actualizar']); }
    $tipos.='i'; $vals[]=$id;
    $stmt=mysqli_prepare($db,"UPDATE Datos_personales SET ".implode(',',$campos)." WHERE id_usuario=?");
    mysqli_stmt_bind_param($stmt,$tipos,...$vals);
    mysqli_stmt_execute($stmt);
    mysqli_close($db);
    responder(200,['mensaje'=>'Datos actualizados']);
}

// ======================
// FOTOS
// ======================
function getFotos() {
    $db=conectar();
    $res=mysqli_query($db,"SELECT f.*, u.username FROM Fotos f JOIN Usuario u ON f.id_usuario=u.id_usuario ORDER BY f.fecha_subida DESC");
    $fotos=[]; while($f=mysqli_fetch_assoc($res)) $fotos[]=$f;
    mysqli_close($db);
    responder(200,['fotos'=>$fotos]);
}

function getFoto($id){
    $db=conectar();
    $stmt=mysqli_prepare($db,"SELECT f.*, u.username FROM Fotos f JOIN Usuario u ON f.id_usuario=u.id_usuario WHERE f.id_foto=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    $res=mysqli_stmt_get_result($stmt);
    $foto=mysqli_fetch_assoc($res);
    mysqli_close($db);
    if(!$foto) responder(404,['error'=>'Foto no encontrada']);
    responder(200,['foto'=>$foto]);
}

function fotosDeUsuario($id){
    $db=conectar();
    $stmt=mysqli_prepare($db,"SELECT * FROM Fotos WHERE id_usuario=? ORDER BY fecha_subida DESC");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    $res=mysqli_stmt_get_result($stmt);
    $fotos=[]; while($f=mysqli_fetch_assoc($res)) $fotos[]=$f;
    mysqli_close($db);
    responder(200,['fotos'=>$fotos]);
}

function subirFoto($d){
    if(empty($d['id_usuario'])||empty($d['url_foto'])) responder(400,['error'=>'Faltan campos']);
    $db=conectar();
    $stmt=mysqli_prepare($db,"INSERT INTO Fotos (id_usuario,url_foto,descripcion,tipo_foto) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt,'isss',$d['id_usuario'],$d['url_foto'],$d['descripcion'],$d['tipo_foto']);
    mysqli_stmt_execute($stmt);
    $id=mysqli_insert_id($db);
    logActividad($db,$d['id_usuario'],'subida_foto','Foto subida');
    mysqli_close($db);
    responder(201,['mensaje'=>'Foto subida','id_foto'=>$id]);
}

function editarFoto($id,$d){
    $db=conectar(); $campos=[];$vals=[];$tipos='';
    foreach(['url_foto','descripcion','tipo_foto'] as $c){
        if(isset($d[$c])) { $campos[]="$c=?"; $vals[]=$d[$c]; $tipos.='s'; }
    }
    if(empty($campos)) { mysqli_close($db); responder(400,['error'=>'Nada que actualizar']); }
    $tipos.='i'; $vals[]=$id;
    $stmt=mysqli_prepare($db,"UPDATE Fotos SET ".implode(',',$campos)." WHERE id_foto=?");
    mysqli_stmt_bind_param($stmt,$tipos,...$vals);
    mysqli_stmt_execute($stmt);
    mysqli_close($db);
    responder(200,['mensaje'=>'Foto actualizada']);
}

function borrarFoto($id){
    $db=conectar();
    $stmt=mysqli_prepare($db,"DELETE FROM Fotos WHERE id_foto=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    mysqli_close($db);
    responder(200,['mensaje'=>'Foto eliminada']);
}

// ======================
// COMENTARIOS
// ======================
function getComentario($id){
    $db=conectar();
    $stmt=mysqli_prepare($db,"SELECT c.*, u.username FROM Comentarios c JOIN Usuario u ON c.id_usuario=u.id_usuario WHERE c.id_comentario=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    $res=mysqli_stmt_get_result($stmt);
    $c=mysqli_fetch_assoc($res);
    mysqli_close($db);
    if(!$c) responder(404,['error'=>'Comentario no encontrado']);
    responder(200,['comentario'=>$c]);
}

function comentariosDeFoto($id_foto){
    $db=conectar();
    $stmt=mysqli_prepare($db,"SELECT c.*, u.username FROM Comentarios c JOIN Usuario u ON c.id_usuario=u.id_usuario WHERE c.id_foto=? ORDER BY c.fecha_publicacion ASC");
    mysqli_stmt_bind_param($stmt,'i',$id_foto);
    mysqli_stmt_execute($stmt);
    $res=mysqli_stmt_get_result($stmt);
    $coms=[]; while($f=mysqli_fetch_assoc($res)) $coms[]=$f;
    mysqli_close($db);
    responder(200,['comentarios'=>$coms]);
}

function crearComentario($d){
    if(empty($d['id_usuario'])||empty($d['id_foto'])||empty($d['contenido'])) responder(400,['error'=>'Faltan campos']);
    $db=conectar();
    $stmt=mysqli_prepare($db,"INSERT INTO Comentarios (id_usuario,id_foto,contenido) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt,'iis',$d['id_usuario'],$d['id_foto'],$d['contenido']);
    mysqli_stmt_execute($stmt);
    $id=mysqli_insert_id($db);
    logActividad($db,$d['id_usuario'],'comentario','Comentario creado');
    mysqli_close($db);
    responder(201,['mensaje'=>'Comentario creado','id_comentario'=>$id]);
}

function editarComentario($id,$d){
    if(empty($d['contenido'])) responder(400,['error'=>'Falta contenido']);
    $db=conectar();
    $stmt=mysqli_prepare($db,"UPDATE Comentarios SET contenido=? WHERE id_comentario=?");
    mysqli_stmt_bind_param($stmt,'si',$d['contenido'],$id);
    mysqli_stmt_execute($stmt);
    mysqli_close($db);
    responder(200,['mensaje'=>'Comentario actualizado']);
}

function borrarComentario($id){
    $db=conectar();
    $stmt=mysqli_prepare($db,"DELETE FROM Comentarios WHERE id_comentario=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    mysqli_close($db);
    responder(200,['mensaje'=>'Comentario eliminado']);
}

// ======================
// ACTIVIDAD
// ======================
function getActividad($id){
    $db=conectar();
    $stmt=mysqli_prepare($db,"SELECT * FROM Actividad WHERE id_usuario=? ORDER BY fecha_actividad DESC");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    $res=mysqli_stmt_get_result($stmt);
    $acts=[]; while($f=mysqli_fetch_assoc($res)) $acts[]=$f;
    mysqli_close($db);
    responder(200,['actividad'=>$acts]);
}

function crearActividad($d){
    if(empty($d['id_usuario']) || empty($d['tipo_actividad']) || empty($d['descripcion'])) {
        responder(400, ['error'=>'Faltan campos']);
    }
    $db = conectar();
    $stmt = mysqli_prepare($db, "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, 'iss', $d['id_usuario'], $d['tipo_actividad'], $d['descripcion']);
    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($db);
    mysqli_close($db);
    responder(201, ['mensaje'=>'Actividad registrada', 'id_actividad'=>$id]);
}

function borrarActividad($id){
    $db = conectar();
    $stmt = mysqli_prepare($db,"DELETE FROM Actividad WHERE id_actividad=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    mysqli_close($db);
    responder(200,['mensaje'=>'Actividad eliminada']);
}
?>