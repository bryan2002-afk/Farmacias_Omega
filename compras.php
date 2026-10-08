<?php
include("auth.php"); // 👈 protege la página

include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compras CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_compra.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_compra.js"></script>
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
                    <li><a href="compras.php"><i class="fas fa-box" style="color:#2ecc71"></i><span>Compras</span></a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>
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
                    <li><a href="productos_lista.php"><i class="fas fa-box"></i><span>Productos</span></a></li>
                    <li><a href="categorias.php"><i class="fas fa-user-cog"></i><span>Categorías</span></a></li>
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
                    <li><a href="usuarios.php"><i class="fas fa-user-cog"></i><span>Usuarios</span></a></li>
                    <!--<li><a href="proveedores.php"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>-->
                    <!--<li><a href="clientes.php"><i class="fas fa-handshake"></i><span>Clientes</span></a></li>
                    <li><a href="direcciones.php"><i class="fas fa-map-marker-alt"></i><span>Direcciónes</span></a></li>-->
                </ul>
            </li>
            <li><a href="reportes.php"><i class="fas fa-folder-open"></i><span>Reportes</span></a></li>
            <li><a href="direcciones.php"><i class="fas fa-map-marker-alt"></i><span>Direcciónes</span></a></li>
            <li><a href="caja.php"><i class="fas fa-envelope"></i><span>Caja</span></a></li>
            
           
            
            

        </ul>
    </div>

    
    <main class="contenido">
        
        <header class="dashboard-header">
    
            <!-- FILA SUPERIOR -->
            <div class="header-top">
                <div>
                    <h1>Compras - Caja POS</h1>
                    
                </div>

                <a href="dashboard.php" class="header-logo">
                    <img src="img/s_fondo.png" alt="Omega Logo">
                </a>
            </div>

            <!-- FILA INFERIOR -->
            <div class="header-bottom">
                <div>Usuario: <strong><?php echo $usuarioSesion; ?></strong> 👋</div>
                <div>Caja: <strong>Activa</strong></div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-right-from-bracket"></i> Cerrar sesión   
                </a>
            </div>

        </header>

        <section class="pos">

            <div class="productos">
                <h3>Productos</h3>
                <input type="text" placeholder="Buscar producto...">
            </div>

            <div class="carrito">
                <h3>Compra actual</h3>

                <table width="100%">
                    <tr>
                        <th>Producto</th>
                        <th>Cant</th>
                        <th>Precio</th>
                        <th>Sub</th>
                    </tr>
                </table>

                <h2>Total: $0.00</h2>

                <button class="btn-vender" id="btnCobrar">Pagar</button>
                <button class="btn-cancelar" >Cancelar</button>
            </div>

        </section>

        <div class="chart-container">
            <h2>Compras Realizadas</h2>
            <!--<canvas id="ventasChart"></canvas>-->
        </div>


        
        <footer class="footer">

            <p class="copy">© 2026 Farmacias Omega Oaxaca - Todos los derechos reservados</p>

        </footer>


    </main>
    

</body>
</html>

<?php
$conn->close();
?>