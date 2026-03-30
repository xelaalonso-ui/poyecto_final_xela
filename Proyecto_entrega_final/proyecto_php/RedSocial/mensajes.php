<?php
session_start();
require_once "includes/db_connection.php";
if (!isset($_SESSION['id'])) { header('Location: index.php'); exit; }
$usuario = $_SESSION['usuario'];
$id      = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mensajes — Red Social</title>
  <link rel="stylesheet" href="global.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .chat-container {
      background: white; border-radius: 24px;
      box-shadow: 0 8px 32px rgba(139,92,246,0.15);
      border: 1px solid rgba(139,92,246,0.1);
      overflow: hidden; height: 520px;
      display: flex; flex-direction: column;
      animation: fadeInUp 0.5s ease;
    }
    .chat-header {
      background: linear-gradient(135deg,#7c3aed,#ec4899);
      padding: 18px 24px; color: white;
      display: flex; align-items: center; gap: 12px;
    }
    .chat-header h3 { margin:0; font-size:1.1rem; }
    .chat-messages {
      flex: 1; overflow-y: auto; padding: 20px;
      display: flex; flex-direction: column; gap: 12px;
      background: #faf5ff;
    }
    .msg-bubble {
      max-width: 70%; padding: 12px 18px; border-radius: 18px;
      font-size: 0.93rem; line-height: 1.5; animation: fadeInUp 0.3s ease;
    }
    .msg-bubble.mine {
      background: linear-gradient(135deg,#8b5cf6,#ec4899);
      color: white; align-self: flex-end;
      border-bottom-right-radius: 4px;
    }
    .msg-bubble.other {
      background: white; color: #374151;
      align-self: flex-start;
      border-bottom-left-radius: 4px;
      box-shadow: 0 2px 8px rgba(139,92,246,0.1);
    }
    .msg-author { font-size:0.75rem; opacity:0.7; margin-bottom:4px; font-weight:600; }
    .chat-input-area {
      padding: 16px 20px; border-top: 1px solid #f3e8ff;
      display: flex; gap: 12px; align-items: center;
    }
    .chat-input {
      flex: 1; padding: 12px 18px; border: 2px solid #e9d5ff;
      border-radius: 50px; font-family: 'Poppins',sans-serif;
      font-size: 0.9rem; outline: none; transition: all 0.3s;
    }
    .chat-input:focus { border-color:#8b5cf6; box-shadow:0 0 0 4px rgba(139,92,246,0.1); }
    .btn-send {
      width:46px; height:46px; border-radius:50%; border:none;
      background:linear-gradient(135deg,#8b5cf6,#ec4899);
      color:white; cursor:pointer; display:flex; align-items:center; justify-content:center;
      font-size:1rem; transition:all 0.3s; box-shadow:0 4px 15px rgba(139,92,246,0.35);
    }
    .btn-send:hover { transform:scale(1.1); box-shadow:0 6px 20px rgba(139,92,246,0.5); }
    .empty-chat { text-align:center; padding:40px; color:#9ca3af; }
  </style>
</head>
<body>
<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-brand">🌸 Mi Red Social</div>
    <div class="menu-nav">
      <a href="p_principal.php"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="publicaciones.php"><i class="fa-solid fa-newspaper"></i> Publicaciones</a>
      <a href="mensajes.php" class="active"><i class="fa-solid fa-comments"></i> Mensajes</a>
      <a href="cuenta.php"><i class="fa-solid fa-user"></i> Mi Cuenta</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
    </div>
  </div>
</nav>

<div class="main-container">
  <h1 class="section-title">💬 Mensajes</h1>

  <div class="chat-container">
    <div class="chat-header">
      <i class="fa-solid fa-comments fa-lg"></i>
      <div>
        <h3>Chat de la Comunidad</h3>
        <div style="font-size:0.8rem;opacity:0.85;">Todos los miembros</div>
      </div>
    </div>

    <div class="chat-messages" id="chatMessages">
      <?php
      $res = mysqli_query($db,
          "SELECT a.descripcion, a.fecha_actividad, u.username, u.id_usuario
           FROM Actividad a
           JOIN Usuario u ON u.id_usuario = a.id_usuario
           WHERE a.tipo_actividad = 'mensaje'
           ORDER BY a.fecha_actividad ASC
           LIMIT 50"
      );
      if (mysqli_num_rows($res) > 0):
        while ($r = mysqli_fetch_assoc($res)):
          $esMio = $r['id_usuario'] == $id;
      ?>
        <div class="msg-bubble <?= $esMio ? 'mine' : 'other' ?>">
          <?php if (!$esMio): ?>
            <div class="msg-author">@<?= htmlspecialchars($r['username']) ?></div>
          <?php endif; ?>
          <?= htmlspecialchars($r['descripcion']) ?>
          <div style="font-size:0.7rem;opacity:0.65;margin-top:4px;text-align:right;">
            <?= (new DateTime($r['fecha_actividad']))->format('H:i') ?>
          </div>
        </div>
      <?php endwhile; else: ?>
        <div class="empty-chat">
          <div style="font-size:3rem;margin-bottom:12px;">💬</div>
          <p>Sé el primero en decir algo!</p>
        </div>
      <?php endif; ?>
    </div>

    <div class="chat-input-area">
      <input type="text" class="chat-input" id="msgInput" placeholder="Escribe un mensaje..." maxlength="300">
      <button class="btn-send" id="sendBtn" title="Enviar">
        <i class="fa-solid fa-paper-plane"></i>
      </button>
    </div>
  </div>
</div>

<img src="img/whisper.png" alt="🐼" class="panda-deco" title="¡Haz clic!">
<script src="efectos.js"></script>
<script>
const chatBox   = document.getElementById('chatMessages');
const msgInput  = document.getElementById('msgInput');
const sendBtn   = document.getElementById('sendBtn');
const username  = "<?= htmlspecialchars($usuario) ?>";

chatBox.scrollTop = chatBox.scrollHeight;

function addBubble(text, mine) {
  const div = document.createElement('div');
  div.className = `msg-bubble ${mine ? 'mine' : 'other'}`;
  div.innerHTML = `${!mine ? `<div class="msg-author">@${username}</div>` : ''}
    ${text}
    <div style="font-size:0.7rem;opacity:0.65;margin-top:4px;text-align:right;">
      ${new Date().toLocaleTimeString('es',{hour:'2-digit',minute:'2-digit'})}
    </div>`;
  chatBox.appendChild(div);
  chatBox.scrollTop = chatBox.scrollHeight;
}

async function enviar() {
  const msg = msgInput.value.trim();
  if (!msg) return;
  msgInput.value = '';
  addBubble(msg, true);

  try {
    await fetch('chat/enviar.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: `mensaje=${encodeURIComponent(msg)}`
    });
  } catch(e) { /* sin servidor activo */ }
}

sendBtn.addEventListener('click', enviar);
msgInput.addEventListener('keydown', e => { if(e.key==='Enter') enviar(); });
</script>
</body>
</html>
