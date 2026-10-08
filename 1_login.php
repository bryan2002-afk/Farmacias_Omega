<?php
session_start();

// Redirigir si ya hay sesión
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}

include('conexion.php');

$error_message = "";

// Verificar envío del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $usuario = trim($_POST['usuario']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    // Validar campos
    if (empty($usuario) || empty($correo) || empty($password)) {
        $error_message = "Por favor, ingrese todos los campos.";
    } else {

        // Buscar usuario
        $query = "SELECT * FROM usuarios WHERE usuario = ? AND correo = ?";

        if ($stmt = $conn->prepare($query)) {

            $stmt->bind_param("ss", $usuario, $correo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $user = $result->fetch_assoc();

                // Validar contraseña
                if (password_verify($password, $user['password'])) {

                    // ================= SESIÓN =================
                    $_SESSION['usuario'] = $user['usuario'];
                    $_SESSION['id_usuario'] = $user['id_usuario'];
                    $_SESSION['id_empleado'] = $user['id_empleado']; // Almacenar id_empleado en la sesión

                    // GUARDAR IMAGEN DEL USUARIO
                    $_SESSION['imagen_usuario'] = $user['imagen'];

                    // ================= APERTURA DE CAJA AUTOMÁTICA =================
                    $id_usuario = $user['id_usuario'];
                    $id_caja = 1;

                    $checkCaja = "SELECT * FROM apertura_caja 
                                  WHERE id_usuario = ? AND estado = 1 LIMIT 1";

                    $stmtCaja = $conn->prepare($checkCaja);
                    $stmtCaja->bind_param("i", $id_usuario);
                    $stmtCaja->execute();
                    $resultCaja = $stmtCaja->get_result();

                    if ($resultCaja->num_rows == 0) {

                        $monto_inicial = 0.00;

                        $insertCaja = "INSERT INTO apertura_caja (id_caja, id_usuario, monto_inicial, estado)
                                       VALUES (?, ?, ?, 1)";

                        $stmtInsert = $conn->prepare($insertCaja);
                        $stmtInsert->bind_param("iid", $id_caja, $id_usuario, $monto_inicial);
                        $stmtInsert->execute();

                        $stmtInsert->close();
                    }

                    $stmtCaja->close();

                    // ================= REDIRECCIÓN =================
                    header("Location: dashboard.php");
                    exit();

                } else {
                    $error_message = "Contraseña incorrecta.";
                }

            } else {
                $error_message = "Usuario o correo incorrectos.";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Farmacias Omega</title>

    <!-- Favicon -->
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    /* ================= RESET ================= */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }

    body {
        background-color: white;
        color: #333;
    }

    /* ================= HEADER ================= */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 60px;
        background-color: white;

        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .logo img {
        width: 200px;
    }

    .info-contacto {
        font-size: 14px;
    }

    .telefono {
        color: #0689b5;
    }

    .servicio {
        color: red;
    }

    .redes a {
        font-size: 20px;
        margin-left: 15px;
        color: #0689b5;
        transition: 0.3s;
    }

    .redes a:hover {
        color: #ff006d;
    }

    /* ================= HERO ================= 
    .hero {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 140px 20px 60px;
        min-height: 100vh;

        background: linear-gradient(to right, #0689b5, #00b4d8);
        background-image: url('fondo4.jpg');
        background-size: cover;         🔥 Hace que la imagen cubra todo 
        background-position: center;    🔥 Centra la imagen 
        background-repeat: no-repeat;   🔥 Evita que se repita 
    }*/

    /* ============= nuevo .hero ========*/

    .hero {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 140px 20px 60px;
        min-height: 100vh;

        overflow: hidden;
    }

    .hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: url('img/fondo4.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;

        filter: blur(6px);          /* 🔥 Nivel de desenfoque */
        transform: scale(1.1);      /* 🔥 Evita bordes recortados */
        z-index: 0;
    }

    /* Asegura que el contenido esté encima */
    .login-container {
        position: relative;
        z-index: 1;
    }

    /* ================= LOGIN ================= */
    .login-container {
        display: flex;
        width: 100%;
        max-width: 850px;
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);

        animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(20px);}
        to {opacity: 1; transform: translateY(0);}
    }

    /* FORM */
    .login-form {
        flex: 1;
        padding: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .login-form h2 {
        margin-bottom: 15px;
    }

    .profile-img {
        width: 80px;
        border-radius: 50%;
        margin-bottom: 20px;
    }

    .input-group {
        width: 100%;
        position: relative;
        margin-bottom: 15px;
    }

    .input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #00b4ff;
    }

    .input-group input {
        width: 100%;
        padding: 12px 15px 12px 45px;
        border: 1px solid #ddd;
        border-radius: 25px;
        background: #f9f9f9;
    }

    .input-group input:focus {
        border-color: #00b4ff;
        outline: none;
    }

    /* OPTIONS */
    .options {
        width: 100%;
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        margin-bottom: 20px;
    }


    /* OPTIONS 
    .options {
        width: 100%;
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        margin-bottom: 20px;
    }*/

    .options a{
            /*color:#fff;*/
            text-decoration:none;
            font-weight:bold;
            }

    .options2 {
        width: 100%;
        display: flex;
        justify-content: center;
        font-size: 0.8rem;
        margin-bottom: 20px;
    }

    .options2 a{
            /*color:#fff;*/
            color: #00d2ff;
            text-decoration:none;
            font-weight:bold;
            }


            
    


    /* BUTTON */
    .btn-signin {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 25px;
        background: linear-gradient(to right, #00d2ff, #3a7bd5);
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-signin:hover {
        transform: translateY(-2px);
    }

    .btn-signin:active {
        transform: scale(0.98);
    }

    .create-account {
        margin-top: 15px;
        font-size: 0.8rem;
    }

    /* VIDEO */
    .login-visual {
        flex: 1;
        position: relative;
    }

    .login-visual video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .login-visual::after {
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.3);
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 768px) {

        .header {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }

        .hero {
            padding-top: 180px;
        }

        .login-container {
            flex-direction: column;
        }

        .login-visual {
            height: 200px;
        }
    }
    </style>

    
</head>

<body>

<!-- HEADER -->
<header class="header">
    <div class="logo">
        <img src="img/logo.png" alt="Logo de Farmacias Omega">
    </div>

    <div class="info-contacto">
        <div class="telefono"><strong>Atención a clientes:</strong> 951 516 6641</div>
        <div class="servicio">Servicio a domicilio sin costo</div>
    </div>

    <div class="redes">
        <a href="https://es-la.facebook.com/farmaciasomegaoficial/"><i class="fab fa-facebook"></i></a>
        <a href="https://www.instagram.com/farmaciasomegaoficial/"><i class="fab fa-instagram"></i></a>
        <a href="https://api.whatsapp.com/send?phone=+5219511026829&text=%C2%A
        1Hola!%20Quisiera%20chatear%20con%20alguien%20de%20Farmacias%20Omega."><i class="fab fa-whatsapp"></i></a>
    </div>
</header>

<!-- HERO -->
<section class="hero">

<div class="login-container">

    <!-- FORM -->
    <form class="login-form" method="post">
        <h2>Iniciar Sesión</h2>

        <img src="img/now5.jpg" alt="Usuario" class="profile-img">

        <?php if ($error_message != ""): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="input-group">
            <i class="fa-regular fa-user"></i>
            <input type="text" name="usuario" placeholder="Usuario" required>
        </div>

        <div class="input-group">
            <i class="fa-regular fa-paper-plane"></i>
            <input type="email" name="correo" placeholder="Correo" required>
        </div>

        <div class="input-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Contraseña" required>
        </div>

       

        <div class="options">
            <label><input type="checkbox"> Recuérdame</label>
            <!--<a href="#">¿Olvidaste tu contraseña?</a>-->
        </div>

        
        <button type="submit" class="btn-signin">Ingresar</button>

        <br>
        
        <div class="options2">
            <a href="2_recuperarpss.php">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="options2">
            <a href="Farmacia_inicio.html"><i class="fas fa-arrow-left"></i> Regresar</a>
        </div>
        <!--<p class="create-account">
            ¿No tienes cuenta? <a href="#">Crear</a>
        </p>-->
    </form>

    <!-- VIDEO -->
    <div class="login-visual">
        <video autoplay muted loop playsinline>
            <source src="img/seg_4.mp4" type="video/mp4">
        </video>
    </div>

</div>

</section>

</body>
</html>

<?php
//$conn->close();
?>