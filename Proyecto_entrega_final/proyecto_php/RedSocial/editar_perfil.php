<?php
session_start();
require_once "includes/db_connection.php";

if (!isset($_SESSION['id'])) { header('Location: index.php'); exit; }

$id_usuario = (int)$_SESSION['id'];
$msg_ok = $msg_err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre']    ?? '');
    $apellido  = trim($_POST['apellido']  ?? '');
    $fnac      = $_POST['fecha_nacimiento'] ?? '';
    $genero    = trim($_POST['genero']    ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono  = trim($_POST['telefono']  ?? '');

    // Verificar si existen datos personales
    $chk = mysqli_query($db, "SELECT id_datos FROM Datos_personales WHERE id_usuario=$id_usuario");
    if (mysqli_num_rows($chk) > 0) {
        $stmt = mysqli_prepare($db,
            "UPDATE Datos_personales SET nombre=?,apellido=?,fecha_nacimiento=?,genero=?,direccion=?,telefono=? WHERE id_usuario=?"
        );
        $fn = $fnac ?: null;
        mysqli_stmt_bind_param($stmt, 'ssssssi', $nombre, $apellido, $fn, $genero, $direccion, $telefono, $id_usuario);
    } else {
        $stmt = mysqli_prepare($db,
            "INSERT INTO Datos_personales (id_usuario,nombre,apellido,fecha_nacimiento,genero,direccion,telefono) VALUES (?,?,?,?,?,?,?)"
        );
        $fn = $fnac ?: null;
        mysqli_stmt_bind_param($stmt, 'issssss', $id_usuario, $nombre, $apellido, $fn, $genero, $direccion, $telefono);
    }

    if (mysqli_stmt_execute($stmt)) {
        // Foto de perfil
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
            $ext     = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                $nombre_f = 'perfil_' . $id_usuario . '_' . time() . '.' . $ext;
                $destino  = 'uploads/' . $nombre_f;
                if (!is_dir('uploads')) mkdir('uploads', 0777, true);
                if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $destino)) {
                    // Borrar foto anterior
                    mysqli_query($db, "DELETE FROM Fotos WHERE id_usuario=$id_usuario AND tipo_foto='perfil'");
                    $fi = mysqli_prepare($db,
                        "INSERT INTO Fotos (id_usuario,url_foto,descripcion,tipo_foto) VALUES (?,'$destino','Foto de perfil','perfil')"
                    );
                    mysqli_stmt_bind_param($fi, 'i', $id_usuario);
                    mysqli_stmt_execute($fi);
                }
            }
        }

        // Registrar actividad
        $act = mysqli_prepare($db,
            "INSERT INTO Actividad (id_usuario,tipo_actividad,descripcion) VALUES (?,'edicion_perfil','Perfil actualizado')"
        );
        mysqli_stmt_bind_param($act, 'i', $id_usuario);
        mysqli_stmt_execute($act);

        $msg_ok = 'Perfil actualizado correctamente';
    } else {
        $msg_err = 'Error al guardar: ' . mysqli_error($db);
    }
}

// Cargar datos actuales
$stmt2 = mysqli_prepare($db,
    "SELECT u.*, d.nombre, d.apellido, d.fecha_nacimiento, d.genero, d.direccion, d.telefono
     FROM Usuario u LEFT JOIN Datos_personales d ON u.id_usuario=d.id_usuario
     WHERE u.id_usuario=?"
);
mysqli_stmt_bind_param($stmt2, 'i', $id_usuario);
mysqli_stmt_execute($stmt2);
$u = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Perfil — Red Social</title>
  <link rel="stylesheet" href="global.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .form-container {
      background:white; border-radius:24px; padding:36px;
      box-shadow:0 8px 32px rgba(139,92,246,0.15);
      border:1px solid rgba(139,92,246,0.1);
      max-width:640px; margin:0 auto;
      animation:fadeInUp 0.6s ease;
    }
    .form-container h2 {
      color:#5b21b6; margin-bottom:28px; font-size:1.6rem;
      display:flex; align-items:center; gap:10px;
    }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
    .form-grid .full { grid-column:1/-1; }
    .locked-field {
      background:#f9fafb; border:2px solid #e5e7eb;
      border-radius:10px; padding:12px 16px; color:#9ca3af;
      font-family:'Poppins',sans-serif; font-size:0.95rem;
    }
    .btn-group { display:flex; gap:12px; margin-top:24px; }
    .file-label {
      display:flex; align-items:center; gap:10px;
      padding:12px 16px; border:2px dashed #c084fc;
      border-radius:12px; cursor:pointer; color:#8b5cf6;
      font-size:0.9rem; transition:all 0.3s;
    }
    .file-label:hover { background:#faf5ff; border-color:#8b5cf6; }
    input[type="file"] { display:none; }
    @media(max-width:600px){ .form-grid{grid-template-columns:1fr;} }
  </style>
</head>
<body>
<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-brand">🌸 Mi Red Social</div>
    <div class="menu-nav">
      <a href="p_principal.php"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="cuenta.php" class="active"><i class="fa-solid fa-user"></i> Mi Cuenta</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
    </div>
  </div>
</nav>

<div class="main-container">
  <div class="form-container">
    <h2><i class="fa-solid fa-pen-to-square"></i> Editar Perfil</h2>

    <?php if ($msg_ok): ?><div class="alert alert-success">✅ <?= htmlspecialchars($msg_ok) ?></div><?php endif; ?>
    <?php if ($msg_err): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($msg_err) ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

      <div class="form-grid">
        <div class="form-group full">
          <label>Usuario (no editable)</label>
          <div class="locked-field"><i class="fa-solid fa-user"></i> @<?= htmlspecialchars($u['username']) ?></div>
        </div>
        <div class="form-group full">
          <label>Email (no editable)</label>
          <div class="locked-field"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($u['email']) ?></div>
        </div>

        <div class="form-group">
          <label for="nombre"><i class="fa-solid fa-id-card"></i> Nombre</label>
          <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($u['nombre'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="apellido"><i class="fa-solid fa-id-card"></i> Apellido</label>
          <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($u['apellido'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="fecha_nacimiento"><i class="fa-solid fa-cake-candles"></i> Fecha de nacimiento</label>
          <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($u['fecha_nacimiento'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="genero"><i class="fa-solid fa-venus-mars"></i> Género</label>
          <select id="genero" name="genero">
            <option value="">Seleccionar...</option>
            <?php foreach (['Masculino','Femenino','Otro','Prefiero no decir'] as $g): ?>
              <option value="<?= $g ?>" <?= ($u['genero'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="telefono"><i class="fa-solid fa-phone"></i> Teléfono</label>
          <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($u['telefono'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="direccion"><i class="fa-solid fa-location-dot"></i> Dirección</label>
          <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($u['direccion'] ?? '') ?>">
        </div>

        <div class="form-group full">
          <label>Foto de perfil</label>
          <label class="file-label" for="foto_perfil">
            <i class="fa-solid fa-camera"></i>
            <span id="file-name">Seleccionar imagen (JPG, PNG, GIF, WEBP)</span>
          </label>
          <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
        </div>
      </div>

      <div class="btn-group">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
        </button>
        <a href="cuenta.php" class="btn btn-secondary">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
      </div>
    </form>
  </div>
</div>

<img src="img/panda.png" alt="🐼" class="panda-deco">
<script src="efectos.js"></script>
<script>
  document.getElementById('foto_perfil').addEventListener('change', function(){
    document.getElementById('file-name').textContent = this.files[0]?.name || 'Seleccionar imagen';
  });
</script>
</body>
</html>
