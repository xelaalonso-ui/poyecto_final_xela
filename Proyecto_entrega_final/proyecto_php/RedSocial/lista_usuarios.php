<?php
session_start();
require_once "includes/db_connection.php";

if (!isset($_SESSION['id'])) { header('Location: index.php'); exit; }

require_once "includes/funciones.php";

$msg_ok = '';
if (isset($_GET['eliminado'])) $msg_ok = 'Usuario eliminado correctamente.';

$usuarios = mostrarUsuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios — Red Social</title>
  <link rel="stylesheet" href="global.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .users-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }
    .user-card {
      background: white; border-radius: 20px; overflow: hidden;
      box-shadow: 0 6px 24px rgba(139,92,246,0.1);
      border: 1px solid rgba(139,92,246,0.1);
      transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
      animation: fadeInUp 0.5s ease both;
    }
    .user-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(139,92,246,0.25);
      border-color: rgba(139,92,246,0.3);
    }
    .user-card-header {
      background: linear-gradient(135deg,#7c3aed,#ec4899);
      padding: 28px 20px; text-align: center; position: relative;
    }
    .user-card-avatar {
      width: 80px; height: 80px; border-radius: 50%;
      object-fit: cover;
      border: 3px solid rgba(255,255,255,0.7);
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
      margin: 0 auto 10px; display: block;
    }
    .user-card-avatar-ph {
      width: 80px; height: 80px; border-radius: 50%;
      background: rgba(255,255,255,0.25);
      display: flex; align-items: center; justify-content: center;
      font-size: 2.2rem; font-weight: 700; color: white;
      border: 3px solid rgba(255,255,255,0.5);
      margin: 0 auto 10px;
    }
    .user-card-username { color:white; font-weight:700; font-size:1.05rem; }
    .user-card-email    { color:rgba(255,255,255,0.8); font-size:0.82rem; }
    .user-card-body { padding: 18px 20px; }
    .user-info-row {
      display:flex; align-items:center; gap:8px;
      color:#6b7280; font-size:0.88rem; margin-bottom:6px;
    }
    .user-info-row i { color:#c084fc; width:16px; }
    .user-card-footer {
      padding:14px 20px; border-top:1px solid #f3e8ff;
      display:flex; gap:8px;
    }
    .badge {
      display:inline-flex; align-items:center; gap:4px;
      padding:4px 10px; border-radius:50px; font-size:0.78rem; font-weight:600;
    }
    .badge-active   { background:#d1fae5; color:#065f46; }
    .badge-inactive { background:#fee2e2; color:#991b1b; }
  </style>
</head>
<body>
<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-brand">🌸 Mi Red Social</div>
    <div class="menu-nav">
      <a href="p_principal.php"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="publicaciones.php"><i class="fa-solid fa-newspaper"></i> Publicaciones</a>
      <a href="cuenta.php"><i class="fa-solid fa-user"></i> Mi Cuenta</a>
      <a href="lista_usuarios.php" class="active"><i class="fa-solid fa-users"></i> Usuarios</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
    </div>
  </div>
</nav>

<div class="main-container">
  <h1 class="section-title">👥 Lista de Usuarios</h1>

  <?php if ($msg_ok): ?>
    <div class="alert alert-success">✅ <?= htmlspecialchars($msg_ok) ?></div>
  <?php endif; ?>

  <?php if (count($usuarios) > 0): $i = 0; ?>
    <div class="users-grid">
      <?php foreach ($usuarios as $u): $i++; ?>
        <div class="user-card" style="animation-delay:<?= $i*0.07 ?>s">
          <div class="user-card-header">
            <?php if ($u['url_foto']): ?>
              <img src="<?= htmlspecialchars($u['url_foto']) ?>" class="user-card-avatar" alt="">
            <?php else: ?>
              <div class="user-card-avatar-ph"><?= strtoupper(substr($u['username'],0,1)) ?></div>
            <?php endif; ?>
            <div class="user-card-username">@<?= htmlspecialchars($u['username']) ?></div>
            <div class="user-card-email"><?= htmlspecialchars($u['email']) ?></div>
          </div>
          <div class="user-card-body">
            <?php if ($u['nombre'] || $u['apellido']): ?>
              <div class="user-info-row">
                <i class="fa-solid fa-user"></i>
                <?= htmlspecialchars(trim(($u['nombre']??'') . ' ' . ($u['apellido']??''))) ?>
              </div>
            <?php endif; ?>
            <?php if ($u['fecha_nacimiento']): ?>
              <div class="user-info-row">
                <i class="fa-solid fa-cake-candles"></i>
                <?= (new DateTime($u['fecha_nacimiento']))->format('d/m/Y') ?>
              </div>
            <?php endif; ?>
            <div class="user-info-row">
              <i class="fa-solid fa-calendar"></i>
              Desde <?= (new DateTime($u['fecha_registro']))->format('d/m/Y') ?>
            </div>
            <div class="user-info-row">
              <span class="badge <?= $u['estado_cuenta']==='activo' ? 'badge-active' : 'badge-inactive' ?>">
                <i class="fa-solid fa-circle" style="font-size:0.5rem"></i>
                <?= ucfirst($u['estado_cuenta'] ?? 'activo') ?>
              </span>
            </div>
          </div>
          <div class="user-card-footer">
            <?php if ($u['id_usuario'] != $_SESSION['id']): ?>
              <a href="includes/eliminar_usuario.php?id=<?= $u['id_usuario'] ?>"
                 class="btn btn-danger"
                 style="font-size:0.85rem;padding:8px 16px;"
                 data-confirm="¿Eliminar al usuario @<?= htmlspecialchars($u['username']) ?>? Esta acción no se puede deshacer.">
                <i class="fa-solid fa-trash"></i> Eliminar
              </a>
            <?php else: ?>
              <span style="color:#9ca3af;font-size:0.85rem;padding:8px;"><i class="fa-solid fa-star" style="color:#f59e0b;"></i> Tú</span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div style="text-align:center;padding:60px;background:white;border-radius:20px;box-shadow:0 6px 24px rgba(139,92,246,0.1);">
      <div style="font-size:4rem;margin-bottom:16px;">👤</div>
      <p style="color:#6b7280;">No hay usuarios registrados.</p>
    </div>
  <?php endif; ?>
</div>

<img src="img/panda.png" alt="🐼" class="panda-deco" title="¡Haz clic!">
<script src="efectos.js"></script>
</body>
</html>
