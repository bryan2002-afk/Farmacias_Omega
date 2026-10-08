<?php
session_start(); // 🔥 ESTO TE FALTA
include("auth.php"); 
include("conexion.php");

// ================= MENSAJE ==================
$mensaje = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}


// ================= BUSCAR (SEGURO) =================
$buscar = trim($_GET['buscar'] ?? "");


/* ================= CONSULTA =================
$sql = "SELECT a.id_articulo, a.codigo, a.nombre, a.descripcion, a.precio, a.stock, a.estado,
               c.nombre AS categoria, a.imagen
        FROM articulo a
       
        LEFT JOIN categoria c ON a.id_categoria = c.id_categoria";
        
if ($buscar != "") {
    $sql .= " WHERE a.nombre LIKE '%$buscar%' 
              OR a.codigo LIKE '%$buscar%'";
} */

$sql = "SELECT a.id_articulo, a.codigo, a.nombre, a.descripcion, 
               a.precio, a.stock, a.estado,
               c.nombre AS categoria, a.imagen
        FROM articulo a
        LEFT JOIN categoria c 
            ON a.id_categoria = c.id_categoria
        WHERE a.stock <= 15";

if ($buscar != "") {
    $sql .= " AND (
                a.nombre LIKE '%$buscar%' 
                OR a.codigo LIKE '%$buscar%'
              )";
}


$sql .= " ORDER BY a.id_articulo DESC";
$result = $conn->query($sql);


//  suma de articulos existentes
$sqlTotal = "SELECT COUNT(*) AS total FROM articulo";
$resTotal = $conn->query($sqlTotal);

if ($resTotal && $filaTotal = $resTotal->fetch_assoc()) {
    $totalProductos = $filaTotal['total'];
}

//  suma de categorias existentes
$sqlTotal = "SELECT COUNT(*) AS total FROM categoria";
$resTotal = $conn->query($sqlTotal);

if ($resTotal && $filaTotal = $resTotal->fetch_assoc()) {
    $totalCategorias = $filaTotal['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stock Bajo CAJA - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="css/style_productos_lista.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_productos_lista.js"></script>
</head>


<style>
    /* MENSAJE SIN STOCK BAJO */
    .sin-stock{
        width: 100%;

        padding: 60px 20px;

        text-align: center;

        background: #fff;

        border-radius: 16px;

        margin-top: 20px;

        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .sin-stock i{
        font-size: 70px;

        color: #22c55e;

        margin-bottom: 20px;
    }

    .sin-stock h2{
        color: #1e293b;

        margin-bottom: 10px;
    }

    .sin-stock p{
        color: #64748b;

        font-size: 15px;
    }
</style>


<body>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <script>
    window.addEventListener('DOMContentLoaded', () => {
        const toastDiv = document.getElementById('toast');

         // Usar la variable $mensaje que ya definimos en PHP
        const mensaje = <?php echo $mensaje ? json_encode($mensaje) : 'null'; ?>;

        if (mensaje) {
            toastDiv.classList.add(mensaje.tipo); // success o error
            toastDiv.textContent = mensaje.texto;
            //toastDiv.innerHTML = mensaje.texto;
            toastDiv.style.opacity = 1;
            toastDiv.style.transform = 'translateY(0)';

            setTimeout(() => {
                toastDiv.style.opacity = 0;
                toastDiv.style.transform = 'translateY(-20px)';
            }, 4000);
        }
    });
    </script>


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
                    <li><a href="proveedores.php"><i class="fas fa-truck" style="color:#2ecc71"></i><span>Proveedores</span></a></li>
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
                    <h1>Stock Bajo Productos - Caja POS</h1>
                    
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
                    <i class="fas fa-right-from-bracket"></i> Cerrar sesión   
                </a>
            </div>

        </header>

        <div class="dashboard-cards">

            <div class="card" onclick="window.location.href='productos_lista.php'">
                <div class="card-icon"><i class="fas fa-boxes"></i></div>
                <div>
                    <h3>Productos</h3>
                    <p><?= $totalProductos ?></p>
                </div>
            </div>

            <div class="card" onclick="window.location.href='categorias.php'">
                <div class="card-icon"><i class="fas fa-user-cog"></i></div>
                <div>
                    <h3>Categorías</h3>
                    <p><?= $totalCategorias ?> Categoría´s</p>
                </div>
            </div>


        </div>



        <!-- ===== GRID DE PRODUCTOS ===== -->
        <div class="chart-container">

                
            

            <div class="products-header">

                

                <h3>Stock Bajo: -15 pzs</h3>


             

                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscador" placeholder="Buscar por Nombre, Codigo o Categoria." onkeyup="filtrarTabla()">
                </div>

                <!--<a href="productos_agregar.php" class="new-btn">
                    <i class="fas fa-plus"></i> Agregar producto   
                </a>-->


                


            </div>


            <?php if($result->num_rows > 0): ?>

            <div class="container-n">
                <table id="tablaProductos">
                    <thead>
                        <tr>
                            <th>Producto </th>
                            <th>Codigo</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Categoria</th>
                            <th>Estado</th>
                            <th style="text-align: center;">Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="product-cell">
                                <img src="ima_productos/<?= $row['imagen']; ?>" 
                                    alt="<?= htmlspecialchars($row['nombre']); ?>" 
                                    class="product-img">

                                <?= htmlspecialchars($row['nombre']) ?>
                            </td>

                            <td class="id-cell"><?= $row['codigo'] ?></td>

                            <td class="price-cell">
                                $ <?= number_format($row['precio'], 2) ?>
                            </td>

                            <td class="stock-cell">
                                <?= $row['stock'] ?> pzs
                            </td>

                            <td class="type-cell">
                                <?= htmlspecialchars($row['categoria']) ?>
                            </td>

                            <td>
                                <span class="status-badge <?= ($row['estado'] == 1) ? 'active' : 'inactive' ?>">
                                    <?= ($row['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>

                            <td style="text-align: center;">

                                <a href="productos_editar.php?id=<?= $row['id_articulo'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="productos_eliminar.php?id=<?= $row['id_articulo'] ?>" 
                                class="btn-delete"
                                onclick="return confirm('¿Eliminar el producto: <?= $row['nombre'] ?> ?') ">
                                    <i class="fas fa-trash"></i>
                                </a>

                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>

            <?php else: ?>

                <div class="sin-stock">
                    <i class="fas fa-check-circle"></i>
                    <h2>No hay productos con Stock Bajo</h2>
                    <p>Todos los productos tienen más de 15 piezas disponibles ☑️</p>
                </div>

            <?php endif; ?>

            <!-- Sección de los productos o de section -->

            

            


        </div>


        

        
        <footer class="footer">

            <p class="copy">© 2026 Farmacias Omega Oaxaca - Todos los derechos reservados</p>

        </footer>


    </main>



    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="close-modal" onclick="cerrarModal()">×</div>

            <h2>Información del Producto</h2>

            <div class="modal-info">
                <p><strong>Nombre:</strong> <span id="modalNombre"></span></p>
                <p><strong>Código:</strong> <span id="modalCodigo"></span></p>
                <p><strong>Descripción:</strong> <span id="modalDescripcion"></span></p>
                <p><strong>Categoría:</strong> <span id="modalCategoria"></span></p>
                <p><strong>Precio:</strong> $ <span id="modalPrecio"></span></p>
                <p><strong>Stock disponible:</strong> <span id="modalStock"></span> pcs</p>
                <p><strong>Estado:</strong> <span id="modalActivo"></span></p>
                
                <p><strong>Imagen:</strong><br> 
                    <span id="modalImagen"></span>
                </p>
            </div>
        </div>
    </div>
   
        
        

</body>
</html>

<?php
$conn->close();
?>