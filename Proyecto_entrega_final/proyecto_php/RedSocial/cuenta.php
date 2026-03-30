<?php
session_start();
require_once "includes/db_connection.php";

if (!isset($_SESSION['id'])) { header('Location: index.php'); exit; }

$id_usuario = (int)$_SESSION['id'];

$stmt = mysqli_prepare($db,
    "SELECT u.*, d.nombre, d.apellido, d.fecha_nacimiento, d.genero, d.direccion, d.telefono
     FROM Usuario u
     LEFT JOIN Datos_personales d ON u.id_usuario = d.id_usuario
     WHERE u.id_usuario = ?"
);
mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
mysqli_stmt_execute($stmt);
$usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$fotoRes = mysqli_query($db,
    "SELECT url_foto FROM Fotos WHERE id_usuario=$id_usuario AND tipo_foto='perfil' ORDER BY fecha_subida DESC LIMIT 1"
);
$foto = mysqli_fetch_assoc($fotoRes)['url_foto'] ?? null;

$countRes = mysqli_query($db,
    "SELECT COUNT(*) AS total FROM Fotos WHERE id_usuario=$id_usuario AND tipo_foto='publicacion'"
);
$total_pubs = mysqli_fetch_assoc($countRes)['total'];

$postsRes = mysqli_query($db,
    "SELECT * FROM Fotos WHERE id_usuario=$id_usuario AND tipo_foto='publicacion' ORDER BY fecha_subida DESC LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Cuenta — Red Social</title>
  <link rel="stylesheet" href="global.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .profile-hero {
      background: linear-gradient(135deg,#7c3aed,#ec4899);
      border-radius: 24px; padding: 36px; color: white;
      display: flex; align-items: center; gap: 28px;
      box-shadow: 0 12px 40px rgba(124,58,237,0.35);
      margin-bottom: 24px; flex-wrap: wrap;
      animation: fadeInUp 0.6s ease;
    }
    .profile-avatar {
      width: 120px; height: 120px; border-radius: 50%;
      object-fit: cover;
      border: 4px solid rgba(255,255,255,0.7);
      box-shadow: 0 8px 24px rgba(0,0,0,0.25);
      flex-shrink: 0;
    }
    .profile-avatar-ph {
      width: 120px; height: 120px; border-radius: 50%;
      background: rgba(255,255,255,0.25);
      display: flex; align-items: center; justify-content: center;
      font-size: 3.5rem; font-weight: 700; color: white;
      border: 4px solid rgba(255,255,255,0.5); flex-shrink: 0;
    }
    .profile-info h2 { font-size: 1.8rem; margin-bottom: 4px; }
    .profile-info .email { opacity: 0.85; font-size: 0.95rem; margin-bottom: 16px; }
    .stats { display: flex; gap: 28px; flex-wrap: wrap; }
    .stat { text-align: center; }
    .stat .num { font-size: 1.8rem; font-weight: 700; display: block; }
    .stat .lbl { font-size: 0.8rem; opacity: 0.85; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .info-item {
      background: #faf5ff; border-radius: 12px; padding: 14px 18px;
      border: 1px solid #e9d5ff;
    }
    .info-item .lbl { font-size: 0.78rem; color: #8b5cf6; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .info-item .val { color: #374151; font-weight: 500; }

    .mini-post {
      background: #faf5ff; border-radius: 12px; padding: 14px 18px;
      border-left: 3px solid #8b5cf6; margin-bottom: 10px;
      transition: all 0.25s;
    }
    .mini-post:hover { transform: translateX(4px); background: #f3e8ff; }
    .mini-post .fecha { font-size: 0.78rem; color: #9ca3af; margin-bottom: 4px; }
    .mini-post .texto { color: #374151; font-size: 0.92rem; }

    @media(max-width:600px) {
      .profile-hero { flex-direction:column; align-items:flex-start; }
      .info-grid { grid-template-columns:1fr; }
    }
  </style>
</head>
<body>
<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-brand">🌸 Mi Red Social</div>
    <div class="menu-nav">
      <a href="p_principal.php"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="publicaciones.php"><i class="fa-solid fa-newspaper"></i> Publicaciones</a>
      <a href="descripcion.php"><i class="fa-solid fa-circle-info"></i> Descripción</a>
      <a href="cuenta.php" class="active"><i class="fa-solid fa-user"></i> Mi Cuenta</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
    </div>
  </div>
</nav>

<div class="main-container">

  <!-- Hero perfil -->
  <div class="profile-hero">
    <?php if ($foto): ?>
      <img src="<?= htmlspecialchars($foto) ?>" class="profile-avatar" alt="Foto de perfil">
    <?php else: ?>
      <div class="profile-avatar-ph"><?= strtoupper(substr($usuario['username'],0,1)) ?></div>
    <?php endif; ?>
    <div class="profile-info">
      <h2>@<?= htmlspecialchars($usuario['username']) ?></h2>
      <div class="email"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($usuario['email']) ?></div>
      <div class="stats">
        <div class="stat">
          <span class="num"><?= $total_pubs ?></span>
          <span class="lbl">Publicaciones</span>
        </div>
        <div class="stat">
          <span class="num"><?= ucfirst($usuario['estado_cuenta'] ?? 'activo') ?></span>
          <span class="lbl">Estado</span>
        </div>
        <div class="stat">
          <span class="num"><?= (new DateTime($usuario['fecha_registro']))->format('d/m/Y') ?></span>
          <span class="lbl">Miembro desde</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Info personal -->
  <div class="card" style="margin-bottom:20px;">
    <h3 style="margin-bottom:18px;"><i class="fa-solid fa-address-card"></i> Información Personal</h3>
    <?php if ($usuario['nombre'] || $usuario['apellido'] || $usuario['telefono']): ?>
      <div class="info-grid">
        <?php if ($usuario['nombre'] || $usuario['apellido']): ?>
          <div class="info-item">
            <div class="lbl">Nombre completo</div>
            <div class="val"><?= htmlspecialchars(trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''))) ?></div>
          </div>
        <?php endif; ?>
        <?php if ($usuario['fecha_nacimiento']): ?>
          <div class="info-item">
            <div class="lbl">Fecha de nacimiento</div>
            <div class="val"><?= (new DateTime($usuario['fecha_nacimiento']))->format('d/m/Y') ?></div>
          </div>
        <?php endif; ?>
        <?php if ($usuario['genero']): ?>
          <div class="info-item">
            <div class="lbl">Género</div>
            <div class="val"><?= htmlspecialchars($usuario['genero']) ?></div>
          </div>
        <?php endif; ?>
        <?php if ($usuario['telefono']): ?>
          <div class="info-item">
            <div class="lbl">Teléfono</div>
            <div class="val"><?= htmlspecialchars($usuario['telefono']) ?></div>
          </div>
        <?php endif; ?>
        <?php if ($usuario['direccion']): ?>
          <div class="info-item" style="grid-column:1/-1">
            <div class="lbl">Dirección</div>
            <div class="val"><?= htmlspecialchars($usuario['direccion']) ?></div>
          </div>
        <?php endif; ?>
        <?php if ($usuario['ultimo_login']): ?>
          <div class="info-item">
            <div class="lbl">Último acceso</div>
            <div class="val"><?= (new DateTime($usuario['ultimo_login']))->format('d/m/Y H:i') ?></div>
          </div>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <p style="color:#9ca3af;font-style:italic;">
        No has completado tu información personal aún.
      </p>
    <?php endif; ?>
    <div style="margin-top:20px;">
      <a href="editar_perfil.php" class="btn btn-primary">
        <i class="fa-solid fa-pen"></i> Editar perfil
      </a>
    </div>
  </div>

  <!-- Últimas publicaciones -->
  <?php if (mysqli_num_rows($postsRes) > 0): ?>
    <div class="card">
      <h3 style="margin-bottom:16px;"><i class="fa-solid fa-clock-rotate-left"></i> Últimas Publicaciones</h3>
      <?php while ($p = mysqli_fetch_assoc($postsRes)): ?>
        <div class="mini-post">
          <div class="fecha"><i class="fa-regular fa-clock"></i> <?= (new DateTime($p['fecha_subida']))->format('d/m/Y H:i') ?></div>
          <div class="texto"><?= nl2br(htmlspecialchars($p['descripcion'])) ?></div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

</div>

<img src="img/whisper.png" alt="🐼" class="panda-deco" title="¡Haz clic!">
<script src="efectos.js"></script>
</body>
</html>
