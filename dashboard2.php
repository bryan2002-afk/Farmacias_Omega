<?php
include("auth.php"); // 👈 protege la página

include("conexion.php");

$totalProductos = 0;

// Obtener el id_empleado desde la sesión
$id_empleado = $_SESSION['id_empleado']; // Esto lo guardamos durante el login

// Obtener el nombre del empleado
$sqlEmpleado = "SELECT nombre FROM empleado WHERE id_empleado = ?";
$stmtEmpleado = $conn->prepare($sqlEmpleado);
$stmtEmpleado->bind_param("i", $id_empleado);
$stmtEmpleado->execute();
$resultEmpleado = $stmtEmpleado->get_result();

if ($resultEmpleado && $filaEmpleado = $resultEmpleado->fetch_assoc()) {
    $nombreEmpleado = $filaEmpleado['nombre']; // Almacenar nombre del empleado
}

//  suma de articulos existentes
$sqlTotal = "SELECT COUNT(*) AS total FROM articulo";
$resTotal = $conn->query($sqlTotal);

if ($resTotal && $filaTotal = $resTotal->fetch_assoc()) {
    $totalProductos = $filaTotal['total'];
}

// suma de artículos con stock menor o igual a 15
$sqlTotal = "SELECT COUNT(*) AS total 
             FROM articulo
             WHERE stock <= 15";

$resTotal = $conn->query($sqlTotal);

if ($resTotal && $filaTotal = $resTotal->fetch_assoc()) {
    $totalStock_Bajo = $filaTotal['total'];
}



//  suma de clientes existentes
$sqlTotal = "SELECT COUNT(*) AS total FROM cliente";
$resTotal = $conn->query($sqlTotal);

if ($resTotal && $filaTotal = $resTotal->fetch_assoc()) {
    $totalClientes = $filaTotal['total'];
}



 //  suma de vrntas realizadas
$sqlVenta = "SELECT COUNT(*) AS venta 
        FROM venta
        WHERE Estado = 1";
$resVenta = $conn->query($sqlVenta);

if ($resVenta && $filaVenta = $resVenta->fetch_assoc()) {
    $totalVenta = $filaVenta['venta'];
}




$sql = "SELECT SUM(total) AS total_ventas 
        FROM venta 
        WHERE Estado = 1";

$resultado = $conn->query($sql);

if ($resultado && $fila = $resultado->fetch_assoc()) {

    $totalVentas = $fila['total_ventas'];

} else {

    $totalVentas = 0;

}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard CAJA - Farmacias Omega Oaxaca</title>
    <link rel="stylesheet" href="css/style_dashboard2.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_dashboard.js"></script>
</head>




<body>


    <div class="sidebar" id="sidebar">
        
        <button id="toggle-btn">
            <i class="fas fa-bars"></i>
        </button>

        <ul>
            <br>
            <li class="submenu" style="list-style: none;">
                <a href="#" class="submenu-toggle">
                    <!--<img src="img/1.png" alt="Perfil" class="avatar">-->
                    <img src="ima_usuarios/<?php echo $imagenUsuario; ?>" alt="Perfil" class="avatar">
                    <!--<span>Bryan</span>-->
                    <span><?php echo $nombreEmpleado; ?></span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list" >
                    <li><a href="mi_perfil.php"><i class="fas fa-user"></i><span>Mi Perfil</span></a></li>
                    <li><a href="configuracion.html"><i class="fas fa-cog"></i><span>Configuración</span></a></li>
                    <li><a href="logout.php"><i class="fas fa-right-from-bracket"></i><span>Cerrar Sesión</span></a></li>
                </ul>
            </li>
            
            <!--style="color:#2ecc71;-->

            <li><a href="dashboard.php"><i class="emoji-icon">📊</i><span> Dashboard</span></a></li>
            <!--<li><a href="ventas.php"><i class="fas fa-shopping-cart"></i><span>Ventas</span></a></li>-->
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="emoji-icon" >🛒</i>
                    <span>Ventas</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="ventas.php"><i class="emoji-icon" >🛒</i><span>Ventas</span></a></li>
                    <li><a href="clientes.php"><i class="emoji-icon">👤</i><span>Clientes</span></a></li>
                </ul>
            </li>
            <!--<li><a href="compras.php"><i class="fas fa-box"></i><span>Compras</span></a></li>--> 
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="emoji-icon">🧺</i>
                    <span>Compras</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="compras.php"><i class="emoji-icon">🧺</i><span>Compras</span></a></li>
                    <li><a href="proveedores.php"><i class="emoji-icon">🚚</i><span>Proveedores</span></a></li>
                </ul>
            </li>

            <li><a href="inventario.php"><i class="emoji-icon">📝</i><span>Inventario</span></a></li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="emoji-icon">🔗</i>
                    <span>Productos</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="productos_lista.php"><i class="emoji-icon">📦</i><span>Productos</span></a></li>
                    <li><a href="categorias.php"><i class="emoji-icon">📚</i><span>Categorías</span></a></li>
                </ul>
            </li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="emoji-icon">🪪</i>
                    <span>Usuarios</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="empleados.php"><i class="emoji-icon">🚹</i><span>Empleados</span></a></li>
                    <li><a href="perfiles.php"><i class="emoji-icon">👤</i><span>Perfiles</span></a></li>
                    <li><a href="usuarios.php"><i class="emoji-icon">🪪</i><span>Usuarios</span></a></li>
                    <!--<li><a href="proveedores.php"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>-->
                    <!--<li><a href="clientes.php"><i class="fas fa-handshake"></i><span>Clientes</span></a></li>
                    <li><a href="direcciones.php"><i class="fas fa-map-marker-alt"></i><span>Direcciónes</span></a></li>-->
                </ul>
            </li>
            <li><a href="reportes.php"><i class="emoji-icon">📄</i><span>Reportes</span></a></li>
            <li><a href="direcciones.php"><i class="emoji-icon">📌</i><span>Direcciónes</span></a></li>
            <li><a href="caja.php"><i class="emoji-icon">🪙</i><span>Caja</span></a></li>
            
           
            
            

        </ul>       
    </div>

    
    <main class="contenido">
        
        <header class="dashboard-header">
    
            <!-- FILA SUPERIOR -->
            <div class="header-top">
                <div>
                    <h1>Dashboard - Caja POS</h1>
                    
                </div>

                <a href="dashboard.php" class="header-logo">
                    <img src="img/s_fondo.png" alt="Omega Logo">
                </a>
            </div>

            <!-- FILA INFERIOR -->
            <div class="header-bottom">
                <div>Usuario: <strong> <?php echo $usuarioSesion; ?></strong>👋</div>
                <div>Caja: <strong>Activa</strong></div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-right-from-bracket"></i>
                        Cerrar sesión
                </a>
               
            </div>

        </header>


        <div class="dashboard-cards">

            <div class="card" onclick="window.location.href='productos_lista.php'">
                <div class="card-icon"><i class="fas fa-boxes"></i></div>
                <div>
                    <h3>Productos</h3>
                    <p><?= $totalProductos ?> Producto´s</p>
                </div>
            </div>

            <div class="card" onclick="window.location.href='clientes.php'">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <div>
                    <h3>Clientes</h3>
                    <p><?= $totalClientes ?> Cliente´s</p>
                </div>
            </div>

            <div class="card" onclick="window.location.href='ventas.php'">
                <div class="card-icon"><i class="fas fa-shopping-bag"></i></div>
                <div>
                    <h3>Ventas Totales</h3>
                    <p><?= $totalVenta ?> Venta's</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                <div>
                    <h3>Ingresos Mes</h3>
                    <p>$ <?= $totalVentas ?></p>
                </div>
            </div>

            <!--<div class="card">
                <div class="card-icon"><i class="fas fa-shopping-bag"></i></div>
                <div>
                    <h3>Ventas Totales</h3>
                    <p>320</p>
                </div>
            </div>-->

            <div class="card" onclick="window.location.href='stock_bajo.php'">
                <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <h3>Stock Bajo</h3>
                    <p><?= $totalStock_Bajo ?> Producto´s</p>
                </div>
            </div>

        </div>

        <div class="chart-container">
            <h2>Ventas de la Semana</h2>
            <canvas id="ventasChart"></canvas>
        </div>

        <!--
        <div class="chart-container">
            <h2>Ventas de la Semana</h2>
            <canvas id="ventasChart"></canvas>
        </div>-->

        <div class="charts-grid">

            <div class="chart-container">
                <h2>Ventas Semanales</h2>
                <canvas id="ventasSemana"></canvas>
            </div>

            <div class="chart-container">
                <h2>Productos Más Vendidos</h2>
                <canvas id="productosTop"></canvas>
            </div>

            <div class="chart-container">
                <h2>Ventas por Categoría</h2>
                <canvas id="categorias"></canvas>
            </div>

        </div>

        
        <footer class="footer">

            <p class="copy">© 2026 Farmacias Omega Oaxaca - Todos los derechos reservados</p>

        </footer>



    </main>


    <script>
    

        // ====== GRAFICA 1: VENTAS SEMANA ======
        new Chart(document.getElementById("ventasSemana"), {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Ventas $',
                    data: [1200, 1900, 1500, 2200, 3000, 2800, 3500],
                    borderWidth: 2,
                    tension: 0.4
                }]
            }
        });

        // ====== GRAFICA 2: PRODUCTOS TOP ======
        new Chart(document.getElementById("productosTop"), {
            type: 'bar',
            data: {
                labels: ['Paracetamol', 'Jarabe', 'Vitamina C', 'Ibuprofeno'],
                datasets: [{
                    label: 'Ventas',
                    data: [50, 30, 40, 60],
                    borderWidth: 1
                }]
            }
        });

        // ====== GRAFICA 3: CATEGORIAS ======
        new Chart(document.getElementById("categorias"), {
            type: 'doughnut',
            data: {
                labels: ['Medicamentos', 'Higiene', 'Vitaminas'],
                datasets: [{
                    data: [60, 25, 15]
                }]
            }
        });

    </script>
    
    

</body>
</html>

<?php
$conn->close();
?>