<?php
session_start();

// Procesar formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    // Aquí puedes agregar la lógica de validación con base de datos
    // Por ahora, solo un ejemplo básico
    if (!empty($usuario) && !empty($contrasena)) {
        $_SESSION['usuario'] = $usuario;
        header('Location: p_principal.php');
        exit;
    } else {
        $error_login = "Por favor, completa todos los campos";
    }
}

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    $usuario = $_POST['usuario_reg'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena_reg'] ?? '';

    // Procesar la foto del usuario si se subió
    if (isset($_FILES['foto_usuario']) && $_FILES['foto_usuario']['error'] === 0) {
        $foto_nombre = $_FILES['foto_usuario']['name'];
        $foto_tmp = $_FILES['foto_usuario']['tmp_name'];
        $foto_destino = 'uploads/' . time() . '_' . $foto_nombre;

        // Crear directorio si no existe
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        move_uploaded_file($foto_tmp, $foto_destino);
    }

    // Aquí puedes agregar la lógica para guardar en base de datos
    if (!empty($usuario) && !empty($correo) && !empty($contrasena)) {
        $success_registro = "Registro exitoso. Por favor, inicia sesión.";
    } else {
        $error_registro = "Por favor, completa todos los campos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup Form</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="auth-wrapper">
        <div class="background-shape"></div>
        <div class="secondary-shape"></div>
        <div class="credentials-panel signin">
            <h2 class="slide-element">Iniciar sesión</h2>

            <?php if (isset($error_login)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_login); ?></div>
            <?php endif; ?>

            <?php if (isset($success_registro)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_registro); ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="field-wrapper slide-element">
                    <input type="text" name="usuario" required>
                    <label for="">Usuario</label>
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="field-wrapper slide-element">
                    <input type="password" name="contrasena" required>
                    <label for="">Contraseña</label>
                    <i class="fa-solid fa-lock"></i>
                </div>

                <div class="field-wrapper slide-element">
                    <button class="submit-button" type="submit" name="login">Iniciar</button>
                </div>

                <div class="switch-link slide-element">
                    <p>No tienes cuenta? <br> <a href="#" class="register-trigger">Registrarse</a></p>
                </div>
            </form>
        </div>

        <div class="welcome-section signin">
            <h2 class="slide-element">Bienvenido de vuelta!</h2>
        </div>

        <div class="credentials-panel signup">
            <h2 class="slide-element">Registrarse</h2>

            <?php if (isset($error_registro)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_registro); ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                <div class="field-wrapper slide-element">
                    <input type="text" name="usuario_reg" required>
                    <label for="">Usuario</label>
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="field-wrapper slide-element">
                    <input type="email" name="correo" required>
                    <label for="">Correo</label>
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <div class="field-wrapper slide-element">
                    <input type="password" name="contrasena_reg" required>
                    <label for="">Contraseña</label>
                    <i class="fa-solid fa-lock"></i>
                </div>

                <div class="field-wrapper slide-element">
                    <input type="file" name="foto_usuario" accept="image/*">
                    <label for="">Foto del Usuario</label>
                </div>

                <div class="field-wrapper slide-element">
                    <button class="submit-button" type="submit" name="registro">Registrarse</button>
                </div>

                <div class="switch-link slide-element">
                    <p>Ya tienes la cuenta creada? <br> <a href="#" class="login-trigger">Entrar</a></p>
                </div>
            </form>
        </div>

        <div class="welcome-section signup">
            <h2 class="slide-element">Bienvenido</h2>
        </div>

    </div>

    <script src="style.js"></script>
</body>

</html>