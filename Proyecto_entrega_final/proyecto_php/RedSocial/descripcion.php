<?php
session_start();
if (!isset($_SESSION['id'])) { header('Location: index.php'); exit; }
$usuario_actual = $_SESSION['usuario'] ?? 'Invitado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Descripción — Red Social</title>
  <link rel="stylesheet" href="global.css">
  <link href="https://fonts.googleapis.com/css2?family=Gwendolyn:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .welcome-banner {
      background: linear-gradient(135deg,#7c3aed,#ec4899);
      border-radius: 24px; padding: 32px; color: white; text-align: center;
      box-shadow: 0 12px 40px rgba(124,58,237,0.35);
      margin-bottom: 28px; animation: fadeInUp 0.5s ease;
      position: relative; overflow: hidden;
    }
    .welcome-banner::before {
      content:''; position:absolute; inset:0;
      background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                  radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 40%);
    }
    .welcome-banner h2 { font-family:'Gwendolyn',cursive; font-size:2.4rem; margin-bottom:8px; position:relative; }
    .welcome-banner p  { opacity:0.9; position:relative; }

    .desc-card {
      background: white; border-radius: 20px; padding: 32px;
      box-shadow: 0 8px 32px rgba(139,92,246,0.12);
      border: 1px solid rgba(139,92,246,0.1);
      margin-bottom: 22px; animation: fadeInUp 0.5s ease both;
    }
    .desc-card h2 {
      font-family:'Gwendolyn',cursive; font-size:2rem;
      color:#5b21b6; margin-bottom:16px;
      display:flex; align-items:center; gap:10px;
    }
    .desc-card p { color:#374151; line-height:1.9; font-size:0.97rem; margin-bottom:10px; }

    .feature-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 16px;
    }
    .feature-item {
      background: #faf5ff; border-radius: 14px; padding: 16px 20px;
      border: 1px solid #e9d5ff; display: flex; align-items: flex-start; gap: 12px;
      transition: all 0.3s;
    }
    .feature-item:hover { background:#f3e8ff; transform:translateY(-3px); box-shadow:0 8px 20px rgba(139,92,246,0.1); }
    .feature-icon { font-size: 1.8rem; flex-shrink: 0; }
    .feature-title { font-weight: 700; color:#5b21b6; font-size:0.95rem; margin-bottom:4px; }
    .feature-desc  { color:#6b7280; font-size:0.85rem; line-height:1.5; }

    .lazo-anim { text-align:center; margin:10px 0; }
    .lazo-anim img { width:70px; animation:rotateSlow 6s linear infinite; opacity:0.7; }
    @keyframes rotateSlow { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

    @media(max-width:600px){ .feature-grid{grid-template-columns:1fr;} }
  </style>
</head>
<body>
<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-brand">🌸 Mi Red Social</div>
    <div class="menu-nav">
      <a href="p_principal.php"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="publicaciones.php"><i class="fa-solid fa-newspaper"></i> Publicaciones</a>
      <a href="descripcion.php" class="active"><i class="fa-solid fa-circle-info"></i> Descripción</a>
      <a href="cuenta.php"><i class="fa-solid fa-user"></i> Mi Cuenta</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
    </div>
  </div>
</nav>

<div class="main-container">

  <div class="welcome-banner">
    <h2>¡Bienvenido, <?= htmlspecialchars($usuario_actual) ?>! 🐼</h2>
    <p>Tu espacio seguro para expresarte libremente</p>
  </div>

  <div class="lazo-anim">
    <img src="img/lazo-de-cinta.png" alt="✨">
  </div>

  <div class="desc-card" style="animation-delay:0.1s">
    <h2>✨ ¿Qué es esta Red Social?</h2>
    <p>
      Esta red social nació con un propósito único: ser el lugar donde la gente puede
      <strong>desahogarse de lo que le pasa en la vida cotidiana</strong>, tanto en lo personal
      como en el trabajo, la vida amorosa, los amigos o la familia.
    </p>
    <p>
      Si quieres desestresarte, compartir tus experiencias, o simplemente saber que no estás
      solo en lo que vives, ¡esta es tu comunidad perfecta!
    </p>
  </div>

  <div class="desc-card" style="animation-delay:0.2s">
    <h2>🎯 ¿Qué puedes hacer aquí?</h2>
    <div class="feature-grid">
      <div class="feature-item">
        <div class="feature-icon">📝</div>
        <div>
          <div class="feature-title">Compartir experiencias</div>
          <div class="feature-desc">Cuenta lo que te pasa sin filtros, de forma anónima o con tu identidad.</div>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">💬</div>
        <div>
          <div class="feature-title">Interactuar</div>
          <div class="feature-desc">Lee y reacciona a las historias de la comunidad. Comenta y apoya.</div>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">🔒</div>
        <div>
          <div class="feature-title">Privacidad</div>
          <div class="feature-desc">Tú decides qué compartir. Tu perfil es tuyo y puedes personalizarlo.</div>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">🌟</div>
        <div>
          <div class="feature-title">Comunidad</div>
          <div class="feature-desc">Miles de usuarios comparten sus historias. No estás solo/a en esto.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="desc-card" style="animation-delay:0.3s">
    <h2>💡 Únete a la Comunidad</h2>
    <p>
      Descubre que no eres el único pasando por situaciones difíciles. Encuentra apoyo,
      consejos y la empatía de personas que entienden lo que vives.
    </p>
    <div style="text-align:center; margin-top:24px;">
      <a href="publicaciones.php" class="btn btn-primary" style="font-size:1.05rem;padding:14px 32px;">
        <i class="fa-solid fa-paper-plane"></i> ¡Empieza a compartir!
      </a>
    </div>
  </div>

</div>

<img src="img/whisper (1).png" alt="🐼" class="panda-deco" title="¡Haz clic!">
<script src="efectos.js"></script>
</body>
</html>
