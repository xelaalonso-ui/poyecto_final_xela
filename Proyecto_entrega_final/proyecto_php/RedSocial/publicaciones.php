<?php
session_start();

// ── Seguridad: solo usuarios logueados
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id'];
$msg_ok = $msg_err = '';

// ── URL base de la API
define('API_BASE', 'http://localhost/RedSocial/api/api.php');

// ── Función para llamar a la API
function llamarAPI(string $endpoint, string $metodo = 'GET', array $datos = []): array {
    $url = API_BASE . '/' . ltrim($endpoint, '/');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    if (!empty($datos)) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
    $respuesta = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = json_decode($respuesta, true) ?? [];
    $json['__http_code'] = $httpCode;
    return $json;
}

// ── CREAR publicación
if (isset($_POST['publicar'])) {
    $texto = trim($_POST['texto'] ?? '');
    if ($texto !== '') {
        $resp = llamarAPI('fotos', 'POST', [
            'id_usuario'  => $id_usuario,
            'url_foto'    => '',
            'descripcion' => $texto,
            'tipo_foto'   => 'publicacion'
        ]);
        if ($resp['__http_code'] === 201) {
            $msg_ok = '🎉 ¡Tu publicación se ha creado con éxito!';
        } else {
            $msg_err = '⚠️ Ups, algo salió mal: ' . ($resp['error'] ?? 'desconocido');
        }
    } else {
        $msg_err = '⚠️ No puedes dejar el texto vacío. 💬';
    }
}

// ── ELIMINAR publicación
if (isset($_POST['eliminar']) && !empty($_POST['id_foto'])) {
    $id_foto = (int)$_POST['id_foto'];
    $resp = llamarAPI('fotos/' . $id_foto, 'DELETE');
    if ($resp['__http_code'] === 200) {
        $msg_ok = '🗑️ Publicación eliminada.';
    } else {
        $msg_err = '⚠️ No se pudo eliminar: ' . ($resp['error'] ?? 'desconocido');
    }
}

// ── MOSTRAR publicaciones
$resp = llamarAPI('fotos', 'GET');
$todas_fotos = $resp['fotos'] ?? [];
$publicaciones = array_values(array_filter($todas_fotos, fn($f) => $f['tipo_foto'] === 'publicacion'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Publicaciones — Red Social</title>
  <link rel="stylesheet" href="global.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* Estilos de publicaciones y cajas */
    .publish-box {
      background:white; border-radius:20px; padding:24px;
      box-shadow:0 8px 32px rgba(139,92,246,0.15);
      border:1px solid rgba(139,92,246,0.12); margin-bottom:28px;
      animation: fadeInUp 0.5s ease;
    }
    .publish-box textarea {
      width:100%; min-height:100px; padding:14px 16px;
      border:2px solid #e9d5ff; border-radius:14px;
      font-family:'Poppins',sans-serif; font-size:0.95rem;
      color:#333; resize:vertical; outline:none; transition:all 0.3s;
    }
    .publish-box textarea:focus { border-color:#8b5cf6; box-shadow:0 0 0 4px rgba(139,92,246,0.1); }
    .post-card {
      background:white; border-radius:20px; padding:22px 26px;
      margin-bottom:18px; box-shadow:0 6px 24px rgba(139,92,246,0.1);
      border:1px solid rgba(139,92,246,0.08);
      transition:all 0.35s; animation:fadeInUp 0.5s ease both;
    }
    .post-card:hover { transform:translateY(-6px); box-shadow:0 18px 50px rgba(139,92,246,0.22); }
    .post-header { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .avatar-ph  { width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg,#8b5cf6,#ec4899); display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:1.1rem; }
    .post-autor { font-weight:700; color:#5b21b6; }
    .post-body  { color:#374151; line-height:1.75; padding:12px 0; border-top:1px solid #f3e8ff; border-bottom:1px solid #f3e8ff; margin:8px 0; }
    .post-footer{ display:flex; align-items:center; justify-content:space-between; margin-top:10px; }
    .post-date  { color:#b0b8c9; font-size:0.8rem; }
    .post-btn   { background:none; border:none; cursor:pointer; color:#9ca3af; font-size:0.88rem; padding:5px 12px; border-radius:20px; transition:all 0.25s; display:flex; align-items:center; gap:5px; font-family:'Poppins',sans-serif; }
    .post-btn:hover { background:#f3e8ff; color:#8b5cf6; }
    .api-badge  { display:inline-block; background:#ede9fe; color:#7c3aed; font-size:0.72rem; font-weight:700; padding:3px 10px; border-radius:20px; margin-left:8px; vertical-align:middle; letter-spacing:.3px; }
  </style>
</head>
<body>
<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-brand">🌸 Mi Red Social</div>
    <div class="menu-nav">
      <a href="p_principal.php"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="publicaciones.php" class="active"><i class="fa-solid fa-newspaper"></i> Publicaciones</a>
      <a href="descripcion.php"><i class="fa-solid fa-circle-info"></i> Descripción</a>
      <a href="cuenta.php"><i class="fa-solid fa-user"></i> Mi Cuenta</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
    </div>
  </div>
</nav>

<div class="main-container">
  <h1 class="section-title">📰 Publicaciones <span class="api-badge">⚡ API REST</span></h1>

  <?php
  if ($msg_ok) { 
    echo '<div class="alert alert-success">' . htmlspecialchars($msg_ok) . '</div>'; 
    }
  if ($msg_err) { 
    echo '<div class="alert alert-error">' . htmlspecialchars($msg_err) . '</div>'; 
    }
  ?>

  <!-- CREAR publicación -->
  <div class="publish-box">
    <h3 style="margin-bottom:14px;color:#5b21b6;">
      <i class="fa-solid fa-pen-to-square"></i> Nueva publicación
      <span class="api-badge">POST /fotos</span>
    </h3>
    <form method="POST">
      <textarea name="texto" placeholder="¿Qué te apetece compartir hoy? 💬" maxlength="500" required></textarea>
      <div style="display:flex;justify-content:flex-end;margin-top:10px;">
        <button type="submit" name="publicar" class="btn btn-primary">
          <i class="fa-solid fa-paper-plane"></i> Publicar
        </button>
      </div>
    </form>
  </div>

  <!-- MOSTRAR publicaciones -->
  <?php
  if (!empty($publicaciones)) {
      $i = 0;
      foreach ($publicaciones as $row) {
          $i++;
          echo '<div class="post-card" style="animation-delay:' . ($i * 0.07) . 's">';
          echo '<div class="post-header">';
          echo '<div class="avatar-ph">' . strtoupper(substr($row['username'] ?? '?',0,1)) . '</div>';
          echo '<div><div class="post-autor">@' . htmlspecialchars($row['username'] ?? 'usuario') . '</div></div>';
          echo '</div>';
          echo '<div class="post-body">' . nl2br(htmlspecialchars($row['descripcion'])) . '</div>';
          echo '<div class="post-footer">';
          echo '<div class="post-date"><i class="fa-regular fa-clock"></i> ' . (isset($row['fecha_subida']) ? (new DateTime($row['fecha_subida']))->format('d/m/Y H:i') : '') . '</div>';
          echo '<div style="display:flex;gap:8px;align-items:center;">';
          echo '<button class="post-btn" onclick="like(this)"><i class="fa-regular fa-heart"></i> Me gusta</button>';
          if ((int)$row['id_usuario'] === (int)$id_usuario) {
              echo '<form method="POST" style="display:inline;" onsubmit="return confirm(\'¿Eliminar esta publicación?\');">';
              echo '<input type="hidden" name="id_foto" value="' . (int)$row['id_foto'] . '">';
              echo '<button type="submit" name="eliminar" class="post-btn" style="color:#ef4444;">';
              echo '<i class="fa-solid fa-trash"></i> Eliminar</button></form>';
          }
          echo '</div></div></div>';
      }
  } else {
      echo '<div style="text-align:center;padding:60px;background:white;border-radius:20px;box-shadow:0 6px 24px rgba(139,92,246,0.1);">';
      echo '<div style="font-size:4rem;margin-bottom:16px;">📭</div>';
      echo '<p style="color:#6b7280;">Aún no hay publicaciones. ¡Sé el primero!</p>';
      echo '</div>';
  }
  ?>
</div>

<img src="img/koala.png" alt="🐨" class="panda-deco" title="¡Haz clic!">
<script src="efectos.js"></script>
<script>
function like(btn) {
    const i = btn.querySelector('i');
    if (i.classList.contains('fa-regular')) {
        i.classList.replace('fa-regular','fa-solid');
        btn.style.color='#ec4899';
        showToast('❤️ ¡Te gustó!','success');
    } else {
        i.classList.replace('fa-solid','fa-regular');
        btn.style.color='';
    }
}
</script>
</body>
</html>