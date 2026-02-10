<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lista de Usuarios</title>
    <link rel='stylesheet' href='./css/style.css'>
    <link rel="shortcut icon" href="./img/agregar-contacto.png" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            background: linear-gradient(90deg, #84FFC9, #AAB2FF, #ECA0FF);
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .usuarios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card2 {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card2:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .foto {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .card2 p {
            margin: 10px 0;
            color: #555;
            font-size: 14px;
        }

        .card2 p strong {
            color: #333;
            font-weight: 600;
        }

        .card2 a {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 15px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .card2 a:hover {
            background-color: #c82333;
        }

        .botones {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .Btn {
            padding: 12px 30px;
            background-color: #8d47ff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .Btn:hover {
            background-color: #e76dff;
        }

        .Btn a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .no-usuarios {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            color: #666;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #666;
        }

        @media (max-width: 768px) {
            .usuarios-grid {
                grid-template-columns: 1fr;
            }

            .botones {
                flex-direction: column;
                align-items: center;
            }

            .Btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Usuarios</h1>

        <?php
        include_once("includes/funciones.php");
        $usuarios = mostrarUsuarios();
        
        if(count($usuarios) > 0):
        ?>
        
        <div class="usuarios-grid">
            <?php foreach ($usuarios as $usuario): ?>
                <section class="card2">
                    <?php 
                    if(!empty($usuario['nombre_imagen'])){
                        $imagenbase64 = base64_encode($usuario['imagen']);
                        $tipoimagen = $usuario['tipo_imagen'];
                        $urlimagen = "data:$tipoimagen;base64,$imagenbase64";
                    } else {
                        $urlimagen = "./img/orgulloso.png";
                    }
                    ?>
                    
                    <img class="foto" src="<?php echo htmlspecialchars($urlimagen); ?>" alt="Foto de perfil">
                    
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo']); ?></p>
                    <p><strong>Fecha de nacimiento:</strong> <?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?></p>
                    <p><strong>Rol:</strong> <?php echo htmlspecialchars($usuario['rol']); ?></p>
                    
                    <a href="./includes/eleminarUsuarios.php?id=<?php echo urlencode($usuario['id']); ?>" 
                       onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                        Eliminar usuario
                    </a>
                </section>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
            <div class="no-usuarios">
                <p>No hay usuarios registrados en el sistema.</p>
            </div>
        <?php endif; ?>

        <div class="botones">
            <button class="Btn"><a href="./index.php">Atrás</a></button>
            <button class="Btn"><a href="./includes/cerrar_ssion.php">Cerrar sesión</a></button>
        </div>
    </div>

    <script src="./js/botonsub.js"></script>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Sistema de Gestión de Usuarios</p>
    </footer>
</body>
</html>