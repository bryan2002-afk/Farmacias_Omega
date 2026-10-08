<?php
include("auth.php"); // 👈 protege la página

include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title> Caja POS - Farmacias Omega Oaxaca</title>
    <link rel="stylesheet" href="css/style_caja.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_caja.js"></script>
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

            <li><a href="dashboard.php"><i class="fas fa-th-large" ></i><span> Dashboard</span></a></li>
            <!--<li><a href="ventas.php"><i class="fas fa-shopping-cart"></i><span>Ventas</span></a></li>-->
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-shopping-cart" ></i>
                    <span>Ventas</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="ventas.php"><i class="fas fa-shopping-cart" ></i><span>Ventas</span></a></li>
                    <li><a href="clientes.php"><i class="fas fa-handshake"></i><span>Clientes</span></a></li>
                </ul>
            </li>
            <!--<li><a href="compras.php"><i class="fas fa-box"></i><span>Compras</span></a></li>--> 
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-box"></i>
                    <span>Compras</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="compras.php"><i class="fas fa-box"></i><span>Compras</span></a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck" ></i><span>Proveedores</span></a></li>
                </ul>
            </li>

            <li><a href="inventario.php"><i class="fas fa-clipboard-list"></i><span>Inventario</span></a></li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-boxes"></i>
                    <span>Productos</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="productos_lista.php"><i class="fas fa-box" ></i><span>Productos</span></a></li>
                    <li><a href="categorias.php"><i class="fas fa-user-cog" ></i><span>Categorías</span></a></li>
                </ul>
            </li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="empleados.php"><i class="fas fa-user-tie"></i><span>Empleados</span></a></li>
                    <li><a href="perfiles.php"><i class="fas fa-id-badge"></i><span>Perfiles</span></a></li>
                    <li><a href="usuarios.php"><i class="fas fa-user-cog" ></i><span>Usuarios</span></a></li>
                    <!--<li><a href="proveedores.php"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>-->
                    <!--<li><a href="clientes.php"><i class="fas fa-handshake"></i><span>Clientes</span></a></li>
                    <li><a href="direcciones.php"><i class="fas fa-map-marker-alt"></i><span>Direcciónes</span></a></li>-->
                </ul>
            </li>
            <li><a href="reportes.php"><i class="fas fa-folder-open"></i><span>Reportes</span></a></li>
            <li><a href="direcciones.php"><i class="fas fa-map-marker-alt"></i><span>Direcciónes</span></a></li>
            <li><a href="caja.php"><i class="fas fa-envelope" style="color:#2ecc71"></i><span>Caja</span></a></li>
            
           
            
            

        </ul>       
    </div>

    
    <main class="contenido">
        
        <header class="dashboard-header">
    
            <!-- FILA SUPERIOR -->
            <div class="header-top">
                <div>
                    <h1>Caja - Caja POS</h1>
                    
                </div>

                <a href="dashboard.php" class="header-logo">
                    <img src="img/s_fondo.png" alt="Omega Logo">
                </a>
            </div>

            <!-- FILA INFERIOR -->
            <div class="header-bottom">
                <div>Usuario: <strong><?php echo $usuarioSesion; ?></strong>👋</div>
                <div>Caja: <strong>Activa</strong></div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-right-from-bracket"></i>
                        Cerrar sesión
                </a>
               
            </div>

        </header>

        <!--<div class="dashboard-cards">

            <a href="a_productos.php" class="card-link">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-boxes"></i></div>
                    <div>
                        <h3>Productos</h3>
                        <p>120 Registrados</p>
                    </div>
                </div>
            </a>

            <a href="p_personas.php" class="card-link">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                    <div>
                        <h3>Personas</h3>
                        <p>Comunidad</p>
                    </div>
                </div>
            </a>

            

            <a href="#" class="card-link">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-truck"></i></div>
                    <div>
                        <h3>Proveedores</h3>
                        <p>12 Registrados</p>
                    </div>
                </div>
            </a>

            <a href="#" class="card-link">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div>
                        <h3>Ventas Hoy</h3>
                        <p>$3,450 MXN</p>
                    </div>
                </div>
            </a>

        </div>-->

        <div class="dashboard-cards">
            <div class="card">
                <div class="card-icon"><i class="fas fa-boxes"></i></div>
                <div>
                    <h3>Reporte de Ventas</h3>
                    
                </div>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <div>
                    <h3>Reporte de Compras</h3>
                    
                </div>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                <div>
                    <h3>Ventas Hoy</h3>
                    <p>$3,450</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                <div>
                    <h3>Ingresos Mes</h3>
                    <p>$45,200</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-shopping-bag"></i></div>
                <div>
                    <h3>Ventas Totales</h3>
                    <p>320</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <h3>Stock Bajo</h3>
                    <p>14</p>
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