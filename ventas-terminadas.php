<?php
include("auth.php"); // 👈 protege la página
include("conexion.php");

// ================= MENSAJE ==================
$mensaje = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}


// ================= BUSCAR (SEGURO) =================
$buscar = trim($_GET['buscar'] ?? "");


// ================= CONSULTA =================
$sql = "SELECT a.id_venta, a.codigo, a.total, a.fecha_registro, a.estado,
               c.usuario AS usuario
        FROM venta a
        LEFT JOIN usuarios c ON a.id_usuario = c.id_usuario";

if ($buscar != "") {
    $sql .= " WHERE a.codigo LIKE '%$buscar%' ";
}

$sql .= " ORDER BY a.id_venta DESC";
/*  ___________________  */

$resultProceso = $conn->query("
    SELECT a.id_venta, a.codigo, a.total, a.fecha_registro, a.estado,
           c.usuario AS usuario
    FROM venta a
    LEFT JOIN usuarios c ON a.id_usuario = c.id_usuario
    WHERE a.estado = 0
    ORDER BY a.id_venta DESC
");

$resultFinalizada = $conn->query("
    SELECT a.id_venta, a.codigo, a.total, a.fecha_registro, a.estado,
           c.usuario AS usuario
    FROM venta a
    LEFT JOIN usuarios c ON a.id_usuario = c.id_usuario
    WHERE a.estado = 1
    ORDER BY a.id_venta DESC
");

$resultCancelada = $conn->query("
    SELECT a.id_venta, a.codigo, a.total, a.fecha_registro, a.estado,
           c.usuario AS usuario
    FROM venta a
    LEFT JOIN usuarios c ON a.id_usuario = c.id_usuario
    WHERE a.estado = 2
    ORDER BY a.id_venta DESC
");



 //  suma de vrntas realizadas
$sqlVenta = "SELECT COUNT(*) AS venta 
        FROM venta
        WHERE Estado = 0";
$resVenta = $conn->query($sqlVenta);

if ($resVenta && $filaVenta = $resVenta->fetch_assoc()) {
    $totalVenta = $filaVenta['venta'];
}


 //  suma de vrntas canceladas
$sqlVenta = "SELECT COUNT(*) AS venta 
        FROM venta
        WHERE Estado = 2";
$resVenta = $conn->query($sqlVenta);

if ($resVenta && $filaVenta = $resVenta->fetch_assoc()) {
    $totalVenta_cancelada = $filaVenta['venta'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas CAJA - Farmacias Omega Oaxaca </title>
    
    <link rel="stylesheet" href="css/style_ventas.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_ventas.js"></script>
</head>

<style>
        /* ================================
    SECCIONES POR ESTADO
    ================================ */

    .estado-section{
        margin-top: 35px;
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    /* TITULOS */
    .estado-title{
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* COLORES */
    .title-proceso{
        color: #ff9800;
        border-left: 6px solid #ff9800;
        padding-left: 12px;
    }

    .title-finalizada{
        color: #2e7d32;
        border-left: 6px solid #2e7d32;
        padding-left: 12px;
    }

    .title-cancelada{
        color: #d32f2f;
        border-left: 6px solid #d32f2f;
        padding-left: 12px;
    }


    /* ================================
    TABLAS
    ================================ */

    .estado-table{
        width: 100%;
        border-collapse: collapse;
        overflow: hidden;
        border-radius: 12px;
    }

    .estado-table thead{
        background: #1f2937;
        color: white;
    }

    .estado-table th{
        padding: 14px;
        text-align: left;
        font-size: 14px;
        letter-spacing: .5px;
    }

    .estado-table td{
        padding: 14px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }

    /* HOVER */
    .estado-table tbody tr:hover{
        background: #f9fafb;
        transition: 0.2s;
    }


    /* ================================
    BADGES ESTADOS
    ================================ */

    .badge{
        padding: 7px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
    }

    .badge-proceso{
        background: #fff3cd;
        color: #ff9800;
    }

    .badge-finalizada{
        background: #d4edda;
        color: #2e7d32;
    }

    .badge-cancelada{
        background: #f8d7da;
        color: #d32f2f;
    }


    /* ================================
    BOTONES
    ================================ */

    .btn-view,
    .btn-edit,
    .btn-delete{
        padding: 8px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: 0.2s;
        display: inline-block;
    }

    /* CAMBIAR ESTADO */
    .btn-view{
        background: #e3f2fd;
        color: #1565c0;
    }

    .btn-view:hover{
        background: #bbdefb;
    }

    /* PRODUCTOS */
    .btn-edit{
        background: #ede7f6;
        color: #5e35b1;
    }

    .btn-edit:hover{
        background: #d1c4e9;
    }

    /* ELIMINAR */
    .btn-delete{
        background: #ffebee;
        color: #c62828;
    }

    .btn-delete:hover{
        background: #ffcdd2;
    }


    /* ================================
    RESPONSIVE
    ================================ */

    @media(max-width: 900px){

        .estado-table{
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .estado-title{
            font-size: 18px;
        }

    }
</style>


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
                    <li><a href="ventas.php"><i class="fas fa-shopping-cart" style="color:#2ecc71"></i><span>Ventas</span></a></li>
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
                    <li><a href="proveedores.php"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>
                </ul>
            </li>

            <li><a href="inventario.php"><i class="fas fa-clipboard-list" ></i><span>Inventario</span></a></li>
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
                    <h1>Ventas - Caja POS</h1>
                    
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

        <div class="dashboard-cards2">

            <div class="card2" onclick="window.location.href='ventas.php'">
                <div class="card2-icon2"><i class="fas fa-boxes"></i></div>
                <div>
                    <h3>Ventas en Proceso</h3>
                    <p><?= $totalVenta ?></p>
                </div>
            </div>

            <div class="card2" onclick="window.location.href='ventas-canceladas.php'">
                <div class="card2-icon2"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <h3>Ventas Canceladas</h3>
                    <p><?= $totalVenta_cancelada ?> Producto´s</p>
                </div>
            </div>

           


        </div>




        <!-- ===== GRID DE PRODUCTOS ===== -->
        <div class="chart-container">

                
            

            <div class="products-header">

                

                <h3>Ventas: </h3>


             

                <!--<div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscador" placeholder="Buscar venta." onkeyup="filtrarTabla()">
                </div>

                <a href="ventas_agregar.php" class="new-btn">
                    <i class="fas fa-plus"></i> Agregar Venta   
                </a>

                <a href="ventas_agregar.php" class="new-btn">
                    <i class="fas fa-plus"></i> Crear Venta   
                </a>-->

                


            </div>


            <!--<section class="products-grid">

                <table id="tablaVentas">-->
                    <!--<thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Usuario</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Cambiar Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>-->

                <!-- ================= EN PROCESO ================= -->

                    



                    <!-- ================= FINALIZADAS ================= -->

                    <div class="estado-section">

                        <h2 class="estado-title title-finalizada">
                            🟢 Ventas Finalizadas
                        </h2>

                        <table id="tablaFinalizadas" class="estado-table">

                            <thead>
                                <tr>
                                    <th>Codigo</th>
                                    <th>Usuario</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Cambiar Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while($row = $resultFinalizada->fetch_assoc()): ?>
                                <tr>

                                    <td><?= htmlspecialchars($row['codigo']) ?></td>
                                    <td><?= htmlspecialchars($row['usuario']) ?></td>
                                    <td>$ <?= htmlspecialchars($row['total']) ?></td>
                                    <td><?= htmlspecialchars($row['fecha_registro']) ?></td>

                                    <td>
                                        <span class="badge badge-finalizada">
                                            Finalizada
                                        </span>
                                    </td>

                                    <td>
                                        <a href="toggle_estado_ventas.php?id=<?= $row['id_venta'] ?>&estado=<?= $row['estado'] ?>" class="btn-view">
                                            🔄
                                        </a>
                                    </td>

                                    <td>

                                        <a href="ventas_ticket.php?id=<?= $row['id_venta'] ?>" class="btn-edit">
                                            📜 Ticket
                                        </a>

                                        <!--<a href="ventas_eliminar.php?id=<?= $row['id_venta'] ?>"
                                        class="btn-delete"
                                        onclick="return confirm('¿Eliminar la Venta?')">
                                            🗑 Eliminar
                                        </a>-->

                                    </td>

                                </tr>
                                <?php endwhile; ?>
                            </tbody>

                        </table>

                    </div>



                    <!-- ================= CANCELADAS ================= -->

                    




            <!--</section>-->
            

            

            


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