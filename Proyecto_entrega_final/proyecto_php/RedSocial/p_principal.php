<?php
session_start();

if (!isset($_SESSION['id'])) { header('Location: index.php'); exit; }

$id_usuario = $_SESSION['id'];
$usuario    = $_SESSION['usuario'];

// URL base de la API
define('API_BASE', 'http://localhost/RedSocial/api/api.php');

/**
 * Helper cURL para llamar a la API REST
 */
function llamarAPI(string $endpoint, string $metodo = 'GET', array $datos = []): array {
    $url = API_BASE . '/' . ltrim($endpoint, '/');
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $metodo);
    curl_setopt($ch, CURLOPT_HTTPHEADER,     ['Content-Type: application/json']);
    if (!empty($datos)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
    }
    $respuesta = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = json_decode($respuesta, true) ?? [];
    $json['__http_code'] = $httpCode;
    return $json;
}

$msg_ok = $msg_err = '';

// ── CREAR publicación → POST /fotos ─────────────────────────
if (isset($_POST['publicar'])) {
    $texto = trim($_POST['texto'] ?? '');
    if (!empty($texto)) {
        $resp = llamarAPI('fotos', 'POST', [
            'id_usuario'  => $id_usuario,
            'url_foto'    => '',
            'descripcion' => $texto,
            'tipo_foto'   => 'publicacion'
        ]);
        if ($resp['__http_code'] === 201) {
            $msg_ok = '¡Publicación creada! (API: POST /fotos → 201 Created)';
        } else {
            $msg_err = 'Error API: ' . ($resp['error'] ?? 'desconocido');
        }
    } else {
        $msg_err = 'El texto no puede estar vacío.';
    }
}

// ── ELIMINAR publicación → DELETE /fotos/{id} ───────────────
if (isset($_POST['eliminar']) && !empty($_POST['id_foto'])) {
    $id_foto = (int)$_POST['id_foto'];
    $resp = llamarAPI('fotos/' . $id_foto, 'DELETE');
    if ($resp['__http_code'] === 200) {
        $msg_ok = '¡Publicación eliminada! (API: DELETE /fotos/' . $id_foto . ' → 200 OK)';
    } else {
        $msg_err = 'Error API: ' . ($resp['error'] ?? 'desconocido');
    }
}

// ── MOSTRAR publicaciones → GET /fotos ──────────────────────
$resp        = llamarAPI('fotos', 'GET');
$todas_fotos = $resp['fotos'] ?? [];
$publicaciones = array_values(array_filter($todas_fotos, fn($f) => $f['tipo_foto'] === 'publicacion'));

// Foto de perfil del usuario actual (directo a DB solo para el avatar del composer)
require_once "includes/db_connection.php";
$fotoRes  = mysqli_query($db, "SELECT url_foto FROM Fotos WHERE id_usuario=$id_usuario AND tipo_foto='perfil' ORDER BY fecha_subida DESC LIMIT 1");
$fotoPerfil = mysqli_fetch_assoc($fotoRes)['url_foto'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio — Red Social</title>
  <link rel="stylesheet" href="global.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .panda-top {
      position: fixed; top: 70px; left: -10px; width: 90px; z-index: 5;
      animation: pandaSlide 1.2s ease forwards, float 3.5s ease-in-out 1.2s infinite;
      opacity: 0;
    }
    @keyframes pandaSlide {
      from { opacity:0; transform:translateX(-100px); }
      to   { opacity:1; transform:translateX(0); }
    }
    .publish-box {
      background: white; border-radius: 20px; padding: 24px;
      box-shadow: 0 8px 32px rgba(139,92,246,0.15);
      border: 1px solid rgba(139,92,246,0.12); margin-bottom: 28px;
      animation: fadeInUp 0.5s ease;
    }
    .publish-header { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
    .publish-box textarea {
      width: 100%; min-height: 90px; padding: 14px 16px;
      border: 2px solid #e9d5ff; border-radius: 14px;
      font-family: 'Poppins',sans-serif; font-size: 0.95rem;
      color:#333; resize: vertical; outline: none; transition: all 0.3s;
    }
    .publish-box textarea:focus { border-color: #8b5cf6; box-shadow: 0 0 0 4px rgba(139,92,246,0.1); }
    .publish-footer { display: flex; justify-content: flex-end; margin-top: 12px; }
    .post-card {
      background: white; border-radius: 20px; padding: 22px 26px; margin-bottom: 18px;
      box-shadow: 0 6px 24px rgba(139,92,246,0.1); border: 1px solid rgba(139,92,246,0.08);
      transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
      opacity: 0; transform: translateY(20px); animation: fadeInUp 0.5s ease forwards;
    }
    .post-card:hover { transform: translateY(-6px); box-shadow: 0 18px 50px rgba(139,92,246,0.22); border-color: rgba(139,92,246,0.25); }
    .post-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .post-author-name { font-weight: 700; color: #5b21b6; font-size: 1rem; }
    .post-body { color: #374151; line-height: 1.75; font-size: 0.97rem; padding: 12px 0; border-top: 1px solid #f3e8ff; border-bottom: 1px solid #f3e8ff; margin: 8px 0; }
    .post-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; }
    .post-date { color:#b0b8c9; font-size:0.8rem; display:flex; align-items:center; gap:5px; }
    .post-actions { display:flex; gap:10px; }
    .post-btn { background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 0.88rem; padding: 5px 10px; border-radius: 20px; transition: all 0.25s; display: flex; align-items: center; gap: 5px; font-family: 'Poppins',sans-serif; }
    .post-btn:hover { background: #f3e8ff; color: #8b5cf6; }
    .avatar-sm { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid #c084fc; box-shadow: 0 3px 10px rgba(139,92,246,0.25); }
    .avatar-placeholder-sm { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg,#8b5cf6,#ec4899); display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:1.1rem; border: 2px solid #c084fc; }
    .empty-state { text-align:center; padding: 60px 20px; background: white; border-radius: 20px; box-shadow: 0 6px 24px rgba(139,92,246,0.1); }
    .empty-state .emoji { font-size: 4rem; margin-bottom: 16px; }
    .empty-state p { color:#6b7280; font-size:1.05rem; }
    .api-badge { display:inline-block; background:#ede9fe; color:#7c3aed; font-size:0.72rem; font-weight:700; padding:3px 10px; border-radius:20px; margin-left:8px; vertical-align:middle; }
  </style>
</head>
<body>

  <img src="img/panda.png" alt="" class="panda-top">

  <nav class="navbar">
    <div class="navbar-container">
      <div class="navbar-brand">🌸 Mi Red Social</div>
      <div class="menu-nav">
        <a href="p_principal.php" class="active"><i class="fa-solid fa-house"></i> Inicio</a>
        <a href="publicaciones.php"><i class="fa-solid fa-newspaper"></i> Publicaciones</a>
        <a href="descripcion.php"><i class="fa-solid fa-circle-info"></i> Descripción</a>
        <a href="cuenta.php"><i class="fa-solid fa-user"></i> Mi Cuenta</a>
        <a href="lista_usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
      </div>
    </div>
  </nav>

  <div class="main-container">

    <?php if ($msg_ok): ?>
      <div class="alert alert-success">🎉 <?= htmlspecialchars($msg_ok) ?></div>
    <?php endif; ?>
    <?php if ($msg_err): ?>
      <div class="alert alert-error">⚠️ <?= htmlspecialchars($msg_err) ?></div>
    <?php endif; ?>

    <!-- CREAR → llama POST /fotos a través de la API -->
    <div class="publish-box">
      <div class="publish-header">
        <?php if ($fotoPerfil): ?>
          <img src="<?= htmlspecialchars($fotoPerfil) ?>" class="avatar-sm" alt="Tu foto">
        <?php else: ?>
          <div class="avatar-placeholder-sm"><?= strtoupper(substr($usuario, 0, 1)) ?></div>
        <?php endif; ?>
        <div>
          <strong style="color:#5b21b6;">@<?= htmlspecialchars($usuario) ?></strong>
          <div style="color:#9ca3af;font-size:0.82rem;">¿Qué está pasando hoy?</div>
        </div>
      </div>
      <form method="POST">
        <textarea name="texto" placeholder="Cuéntale algo a la comunidad... 💬" maxlength="500" required></textarea>
        <div class="publish-footer">
          <button type="submit" name="publicar" class="btn btn-primary">
            <i class="fa-solid fa-paper-plane"></i> Publicar
          </button>
        </div>
      </form>
    </div>

    <!-- MOSTRAR → resultado de GET /fotos (API) -->
    <?php if (!empty($publicaciones)): $i = 0; ?>
      <?php foreach ($publicaciones as $row): $i++; ?>
        <div class="post-card" style="animation-delay:<?= $i * 0.08 ?>s">
          <div class="post-header">
            <div class="avatar-placeholder-sm"><?= strtoupper(substr($row['username'] ?? '?', 0, 1)) ?></div>
            <div>
              <div class="post-author-name">@<?= htmlspecialchars($row['username'] ?? 'usuario') ?></div>
            </div>
          </div>
          <div class="post-body"><?= nl2br(htmlspecialchars($row['descripcion'])) ?></div>
          <div class="post-footer">
            <div class="post-date">
              <i class="fa-regular fa-clock"></i>
              <?= isset($row['fecha_subida']) ? (new DateTime($row['fecha_subida']))->format('d/m/Y H:i') : '' ?>
            </div>
            <div class="post-actions">
              <button class="post-btn" onclick="likePost(this)">
                <i class="fa-regular fa-heart"></i> Me gusta
              </button>
              <?php if ((int)$row['id_usuario'] === (int)$id_usuario): ?>
                <!-- ELIMINAR → llama DELETE /fotos/{id} a través de la API -->
                <form method="POST" style="display:inline;"
                      onsubmit="return confirm('¿Eliminar esta publicación?');">
                  <input type="hidden" name="id_foto" value="<?= (int)$row['id_foto'] ?>">
                  <button type="submit" name="eliminar" class="post-btn" style="color:#ef4444;">
                    <i class="fa-solid fa-trash"></i> Eliminar
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state">
        <div class="emoji">📭</div>
        <p>Aún no hay publicaciones. ¡Sé el primero en compartir algo!</p>
      </div>
    <?php endif; ?>

  </div>

  <img src="img/whisper.png" alt="🐼" class="panda-deco" title="¡Haz clic!">

  <script src="efectos.js"></script>
  <script>
    function likePost(btn) {
      const icon = btn.querySelector('i');
      if (icon.classList.contains('fa-regular')) {
        icon.classList.replace('fa-regular','fa-solid');
        btn.style.color = '#ec4899';
        showToast('❤️ ¡Te gustó esta publicación!', 'success');
      } else {
        icon.classList.replace('fa-solid','fa-regular');
        btn.style.color = '';
      }
    }
  </script>
</body>
</html>
