<?php
session_start();
require_once 'includes/db_connection.php';

if (isset($_SESSION['id'])) {
    header('Location: p_principal.php');
    exit;
}

$error_login = $error_registro = $success_registro = '';

/* ── LOGIN ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usuario   = trim($_POST['usuario']   ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    if (!empty($usuario) && !empty($contrasena)) {
        $stmt = mysqli_prepare($db, "SELECT * FROM Usuario WHERE username=? OR email=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'ss', $usuario, $usuario);
        mysqli_stmt_execute($stmt);
        $user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user_data && password_verify($contrasena, $user_data['password_hash'])) {
            $_SESSION['id']       = $user_data['id_usuario'];
            $_SESSION['usuario']  = $user_data['username'];
            $_SESSION['email']    = $user_data['email'];

            $upd = mysqli_prepare($db, "UPDATE Usuario SET ultimo_login=NOW() WHERE id_usuario=?");
            mysqli_stmt_bind_param($upd, 'i', $user_data['id_usuario']);
            mysqli_stmt_execute($upd);

            header('Location: p_principal.php');
            exit;
        } else {
            $error_login = 'Usuario o contraseña incorrectos';
        }
    } else {
        $error_login = 'Completa todos los campos';
    }
}

/* ── REGISTRO ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    $username   = trim($_POST['usuario_reg'] ?? '');
    $correo     = strtolower(trim($_POST['correo'] ?? ''));
    $contrasena = trim($_POST['contrasena_reg'] ?? '');

    if (!empty($username) && !empty($correo) && !empty($contrasena)) {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error_registro = 'El correo no es válido';
        } else {
            $chk = mysqli_prepare($db, "SELECT id_usuario FROM Usuario WHERE username=? OR email=?");
            mysqli_stmt_bind_param($chk, 'ss', $username, $correo);
            mysqli_stmt_execute($chk);
            mysqli_stmt_store_result($chk);

            if (mysqli_stmt_num_rows($chk) > 0) {
                $error_registro = 'El usuario o correo ya están registrados';
            } else {
                $hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $ins  = mysqli_prepare($db, "INSERT INTO Usuario (username,email,password_hash,estado_cuenta) VALUES (?,?,?,'activo')");
                mysqli_stmt_bind_param($ins, 'sss', $username, $correo, $hash);
                if (mysqli_stmt_execute($ins)) {
                    $nuevo_id = mysqli_insert_id($db);

                    if (isset($_FILES['foto_usuario']) && $_FILES['foto_usuario']['error'] === 0) {
                        $ext      = pathinfo($_FILES['foto_usuario']['name'], PATHINFO_EXTENSION);
                        $nombre   = 'perfil_' . $nuevo_id . '_' . time() . '.' . $ext;
                        $destino  = 'uploads/' . $nombre;
                        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
                        if (move_uploaded_file($_FILES['foto_usuario']['tmp_name'], $destino)) {
                            $fi = mysqli_prepare($db, "INSERT INTO Fotos (id_usuario,url_foto,descripcion,tipo_foto) VALUES (?,?,'Foto de perfil','perfil')");
                            mysqli_stmt_bind_param($fi, 'is', $nuevo_id, $destino);
                            mysqli_stmt_execute($fi);
                        }
                    }
                    $success_registro = '¡Registro exitoso! Ya puedes iniciar sesión.';
                } else {
                    $error_registro = 'Error al registrar: ' . mysqli_error($db);
                }
            }
        }
    } else {
        $error_registro = 'Completa todos los campos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Red Social — Acceder</title>
  <link rel="stylesheet" href="global.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      display: flex; align-items: center; justify-content: center;
      min-height: 100vh;
    }

    /* ESTRELLAS */
    #stars  { position:fixed; top:0; left:0; width:1px;  height:1px;  background:transparent; box-shadow: 501px 811px #fff,1450px 1324px #ffa8f0,1093px 1780px #bffc85,1469px 678px #fa91f5,904px 741px #d3a1fc,1160px 781px #fff,1841px 1962px #fff,1630px 1667px #fff,1788px 676px #fff,367px 1734px #fff,1343px 156px #fff,1283px 1142px #fff,1062px 378px #fff,1395px 467px #fff,1017px 1891px #fff,137px 1114px #fff,1767px 1403px #fff,1543px 11px #fff,1078px 181px #fff,1189px 1574px #fff,1697px 1551px #fff,439px 472px #fff,1491px 677px #fff,1364px 599px #fff,34px 382px #fff,1221px 1584px #fff,1266px 1499px #fff,169px 1907px #fff,1219px 1125px #fff,659px 18px #fff; animation:animStar 50s linear infinite; }
    #stars2 { position:fixed; top:0; left:0; width:2px;  height:2px;  background:transparent; box-shadow: 693px 1778px #fff,1016px 711px #fff,1171px 563px #fff,661px 1919px #fff,1610px 44px #fff,1275px 140px #fff,1208px 1802px #fff,1473px 1587px #fff,11px 1117px #fff,853px 1757px #fff,1149px 937px #fff,1353px 428px #fff,270px 279px #fff,258px 1404px #fff,417px 1188px #fff,286px 561px #fff,393px 1765px #fff,147px 881px #fff,666px 1097px #fff,1425px 1278px #fff; animation:animStar 100s linear infinite; }
    #stars3 { position:fixed; top:0; left:0; width:3px;  height:3px;  background:transparent; box-shadow: 200px 981px #fff,1731px 521px #fff,132px 1039px #fff,1888px 1547px #fff,899px 1226px #fff,1887px 580px #fff,1548px 1092px #fff,1626px 689px #fff,254px 1072px #fff,1684px 1211px #fff; animation:animStar 150s linear infinite; }
    @keyframes animStar { from{transform:translateY(0)} to{transform:translateY(-2000px)} }

    /* AUTH WRAPPER */
    .auth-wrapper {
      position: relative; z-index: 10;
      display: flex; width: 820px; max-width: 98vw;
      min-height: 520px;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 30px 80px rgba(0,0,0,0.3);
      animation: aparecer 0.8s ease;
    }
    @keyframes aparecer { from{opacity:0;transform:scale(0.88)} to{opacity:1;transform:scale(1)} }

    .credentials-panel {
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(20px);
      width: 55%; padding: 48px 40px;
      display: flex; flex-direction: column; justify-content: center;
      transition: all 0.7s ease;
    }
    .credentials-panel h2 {
      font-family: 'Gwendolyn', cursive;
      font-size: 2.2rem; color: #5b21b6; margin-bottom: 28px;
      animation: slideIn 0.6s ease;
    }
    @keyframes slideIn { from{opacity:0;transform:translateX(-20px)} to{opacity:1;transform:translateX(0)} }

    .welcome-section {
      background: linear-gradient(135deg, #7c3aed, #ec4899);
      width: 45%; display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      color: white; text-align: center; padding: 40px 28px;
      position: relative; overflow: hidden;
    }
    .welcome-section::before {
      content: ''; position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .welcome-section h2 {
      font-family: 'Gwendolyn', cursive;
      font-size: 2.8rem; margin-bottom: 16px; position:relative;
    }
    .welcome-section p { opacity:0.9; font-size:0.95rem; position:relative; line-height:1.6; }

    /* Hide signup by default */
    .credentials-panel.signup,
    .welcome-section.signup { display:none; }

    .auth-wrapper.toggled .credentials-panel.signin,
    .auth-wrapper.toggled .welcome-section.signin { display:none; }
    .auth-wrapper.toggled .credentials-panel.signup,
    .auth-wrapper.toggled .welcome-section.signup { display:flex; }

    /* FIELD */
    .field-wrapper {
      position: relative; margin-bottom: 20px;
      animation: slideIn 0.5s ease both;
    }
    .field-wrapper input {
      width: 100%; padding: 13px 16px 13px 44px;
      border: 2px solid #e9d5ff; border-radius: 12px;
      font-family: 'Poppins',sans-serif; font-size: 0.95rem;
      color: #333; outline: none; transition: all 0.3s;
      background: #faf5ff;
    }
    .field-wrapper input:focus {
      border-color: #8b5cf6;
      box-shadow: 0 0 0 4px rgba(139,92,246,0.12);
      background: white;
    }
    .field-wrapper label {
      position: absolute; left: 44px; top: 50%; transform: translateY(-50%);
      color: #9ca3af; font-size: 0.9rem; pointer-events: none;
      transition: all 0.3s;
    }
    .field-wrapper input:focus ~ label,
    .field-wrapper input:not(:placeholder-shown) ~ label {
      top: 0; transform: translateY(-50%) translateX(-20px) scale(0.8);
      color: #8b5cf6; background: white; padding: 0 6px; border-radius: 4px;
    }
    .field-wrapper i {
      position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
      color: #c084fc; font-size: 1rem;
    }
    .field-wrapper input[type="file"] { padding: 10px 14px; }

    .submit-button {
      width: 100%; padding: 13px;
      border: none; border-radius: 12px;
      background: linear-gradient(135deg, #8b5cf6, #ec4899);
      color: white; font-weight: 700; font-size: 1rem;
      cursor: pointer; transition: all 0.35s;
      box-shadow: 0 4px 18px rgba(139,92,246,0.4);
      letter-spacing: 0.5px;
    }
    .submit-button:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 8px 28px rgba(139,92,246,0.5);
    }

    .switch-link { text-align:center; font-size:0.9rem; color:#6b7280; margin-top:18px; }
    .switch-link a { color:#8b5cf6; font-weight:600; text-decoration:none; }
    .switch-link a:hover { text-decoration:underline; }

    .error-message   { background:#fee2e2; color:#991b1b; padding:10px 14px; border-radius:10px; font-size:0.88rem; margin-bottom:16px; border-left:3px solid #ef4444; animation:shake 0.4s ease; }
    .success-message { background:#d1fae5; color:#065f46; padding:10px 14px; border-radius:10px; font-size:0.88rem; margin-bottom:16px; border-left:3px solid #10b981; }
    @keyframes shake {
      0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)}
    }

    @media(max-width:640px){
      .auth-wrapper { flex-direction:column; }
      .credentials-panel, .welcome-section { width:100%; }
      .welcome-section { padding:24px; min-height:180px; }
    }
  </style>
</head>
<body>
  <!-- Estrellas -->
  <div id="stars"></div>
  <div id="stars2"></div>
  <div id="stars3"></div>

  <div class="auth-wrapper" id="authWrapper">

    <!-- PANEL LOGIN -->
    <div class="credentials-panel signin">
      <h2>✨ Iniciar sesión</h2>

      <?php if ($error_login): ?>
        <div class="error-message">⚠️ <?= htmlspecialchars($error_login) ?></div>
      <?php endif; ?>
      <?php if ($success_registro): ?>
        <div class="success-message">🎉 <?= htmlspecialchars($success_registro) ?></div>
      <?php endif; ?>

      <form action="" method="POST" autocomplete="off">
        <div class="field-wrapper">
          <i class="fa-solid fa-user"></i>
          <input type="text" name="usuario" placeholder=" " required>
          <label>Usuario o Email</label>
        </div>
        <div class="field-wrapper">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="contrasena" placeholder=" " required>
          <label>Contraseña</label>
        </div>
        <div class="field-wrapper">
          <button class="submit-button" type="submit" name="login">Iniciar sesión</button>
        </div>
      </form>

      <div class="switch-link">
        ¿No tienes cuenta? <a href="#" class="register-trigger">Regístrate aquí</a>
      </div>
    </div>

    <!-- BIENVENIDA LOGIN -->
    <div class="welcome-section signin">
      <h2>¡Bienvenido de vuelta! 🐼</h2>
      <p>Tu espacio para desahogarte y conectar con otros.</p>
    </div>

    <!-- PANEL REGISTRO -->
    <div class="credentials-panel signup">
      <h2>🌟 Regístrate</h2>

      <?php if ($error_registro): ?>
        <div class="error-message">⚠️ <?= htmlspecialchars($error_registro) ?></div>
      <?php endif; ?>

      <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="field-wrapper">
          <i class="fa-solid fa-user"></i>
          <input type="text" name="usuario_reg" placeholder=" " required>
          <label>Nombre de usuario</label>
        </div>
        <div class="field-wrapper">
          <i class="fa-solid fa-envelope"></i>
          <input type="email" name="correo" placeholder=" " required>
          <label>Correo electrónico</label>
        </div>
        <div class="field-wrapper">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="contrasena_reg" placeholder=" " required>
          <label>Contraseña</label>
        </div>
        <div class="field-wrapper">
          <i class="fa-solid fa-camera"></i>
          <input type="file" name="foto_usuario" accept="image/*">
        </div>
        <div class="field-wrapper">
          <button class="submit-button" type="submit" name="registro">Crear cuenta</button>
        </div>
      </form>

      <div class="switch-link">
        ¿Ya tienes cuenta? <a href="#" class="login-trigger">Iniciar sesión</a>
      </div>
    </div>

    <!-- BIENVENIDA REGISTRO -->
    <div class="welcome-section signup">
      <h2>¡Únete! 🌸</h2>
      <p>Comparte, conecta y desahógate con nuestra comunidad.</p>
    </div>

  </div>

  <script src="efectos.js"></script>
  <script>
    const wrap   = document.getElementById('authWrapper');
    document.querySelector('.register-trigger').addEventListener('click', e => {
      e.preventDefault(); wrap.classList.add('toggled');
    });
    document.querySelector('.login-trigger').addEventListener('click', e => {
      e.preventDefault(); wrap.classList.remove('toggled');
    });
  </script>
</body>
</html>
