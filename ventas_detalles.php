<?php
include("auth.php"); // protege la página
include("conexion.php");

/* ================= MENSAJE ================= */
$mensaje = null;

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

/* ================= VALIDAR ID ================= */
if (!isset($_GET['id'])) {

    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'Venta no especificada ❌'
    ];

    header("Location: ventas.php");
    exit();
}

/* ================= ID VENTA ================= */
$id_venta = intval($_GET['id']);

/* ================= OBTENER VENTA ================= */
$sql_venta = "SELECT a.id_venta, a.codigo, a.total, a.fecha_registro, a.estado,
                     c.usuario AS usuario
              FROM venta a
              LEFT JOIN usuarios c 
                    ON a.id_usuario = c.id_usuario
              WHERE a.id_venta = ?";

$stmt_venta = $conn->prepare($sql_venta);

if (!$stmt_venta) {
    die("Error en SELECT venta: " . $conn->error);
}

$stmt_venta->bind_param("i", $id_venta);
$stmt_venta->execute();

$result_venta = $stmt_venta->get_result();
$venta = $result_venta->fetch_assoc();

if (!$venta) {

    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'Venta no encontrada ❌'
    ];

    header("Location: ventas.php");
    exit();
}

/* ================= PRODUCTOS ================= */

$sql = "SELECT a.id_articulo, a.codigo, a.nombre, a.descripcion, 
               a.precio, a.stock, a.estado,
               c.nombre AS categoria, a.imagen
        FROM articulo a
        LEFT JOIN categoria c 
            ON a.id_categoria = c.id_categoria
        WHERE a.stock > 0
        ORDER BY a.id_articulo DESC
        LIMIT 50";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas Detalles - Farmacias Omega Oaxaca </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="css/style_ventas_detalles.css">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/script_ventas.js"></script>
</head>


<style>
    /* =========================================================
   DETALLE DE VENTA
========================================================= */

/*
  Contenedor principal de los datos de venta.
  Se usa CSS Grid para distribuir los elementos.
*/
.venta-detalle{
    display: grid;

    /* 4 columnas iguales */
    grid-template-columns: repeat(4, 1fr);

    gap: 20px;
    margin-top: 20px;
    padding: 15px;

    background: #ffffff;
    border-radius: 12px;

    /* sombra suave */
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);

    font-family: Arial, sans-serif;
}

/*
  Tarjetas individuales de información
*/
.venta-item{
    padding: 15px;
    background: #f9f9f9;

    border-radius: 10px;
    border: 1px solid #e6e6e6;

    transition: all 0.3s ease;
}

/*
  Efecto hover elegante
*/
.venta-item:hover{
    transform: translateY(-2px);

    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/*
  Etiqueta del dato
*/
.venta-item label{
    display: block;

    font-weight: 600;
    margin-bottom: 8px;

    font-size: 13px;
    color: #666;

    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/*
  Valor principal
*/
.venta-item strong{
    display: block;

    color: #222;
    font-size: 18px;

    text-align: center;
}

/* =========================================================
   POS (PUNTO DE VENTA)
========================================================= */

/*
  Contenedor principal del POS
*/
.pos{
    display: flex;
    gap: 20px;

    margin-top: 20px;

    align-items: flex-start;
}

/* =========================================================
   PRODUCTOS
========================================================= */

.productos{
    width: 56%;

    background: #ffffff;

    padding: 20px;

    border-radius: 12px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

/*
  Título
*/
.productos h3{
    margin-bottom: 15px;

    color: #333;
}

/*
  Input de búsqueda
*/
.productos input{
    width: 100%;

    padding: 12px;

    border: 1px solid #dcdcdc;
    border-radius: 8px;

    outline: none;

    transition: border 0.3s;
}

/*
  Focus elegante
*/
.productos input:focus{
    border-color: #3498db;
}

/* =========================================================
   CARRITO
========================================================= */

.carrito{
    width: 42%;

    background: #ffffff;

    padding: 20px;

    border-radius: 12px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.08);

    position: sticky;
    top: 20px;
}

/*
  Tabla del carrito
*/
.carrito table{
    width: 100%;

    border-collapse: collapse;

    margin-top: 15px;
}

/*
  Encabezado tabla
*/
.carrito th{
    background: #f4f4f4;

    padding: 10px;

    font-size: 13px;
    color: #555;
}

/*
  Celdas
*/
.carrito td{
    padding: 10px;

    border-bottom: 1px solid #ececec;

    text-align: center;
}

/*
  Total
*/
.carrito h2{
    margin-top: 20px;

    text-align: right;

    color: #2c3e50;
}

/* =========================================================
   BOTONES
========================================================= */

button{
    padding: 12px;

    margin-top: 12px;

    /*width: 100%;*/

    border: none;

    border-radius: 8px;

    cursor: pointer;

    font-size: 15px;
    font-weight: 600;

    transition: all 0.3s ease;
}

/*
  Botón pagar
*/
.btn-vender{
    background: #2ecc71;
    color: white;
    padding: 15px 100px;
}

.btn-vender:hover{
    background: #27ae60;
}

/*
  Botón cancelar
*/
.btn-cancelar{
    background: #e74c3c;
    color: white;
    padding: 15px 100px;
    
}

.btn-cancelar:hover{
    background: #c0392b;
}

/* =========================================================
   CONTENEDOR GENERAL
========================================================= */

.chart-container{
    background: #ffffff;

    padding: 20px;

    border-radius: 12px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.08);

    margin-top: 20px;
}

/* =========================================================
   HEADER DE PRODUCTOS
========================================================= */

.products-header{
    display: flex;

    justify-content: space-between;
    align-items: center;

    margin-bottom: 20px;
}

/*
  Botón regresar
*/
.new-btn {
  text-decoration: none;
  background: linear-gradient(135deg, #6ee7a6, #4cd97b);
  color: #262323;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: all 0.25s ease;
}

/* Hover elegante */
.new-btn:hover {
  background: linear-gradient(135deg, #4cd97b, #34c96a);
  transform: translateY(-2px);
  box-shadow: 0 6px 14px rgba(0,0,0,0.2);
}

/* Click (efecto presión) */
.new-btn:active {
  transform: scale(0.96);
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
/* =========================================================
   RESPONSIVE
========================================================= */

/*
  Tablets
*/
@media(max-width: 992px){

    .venta-detalle{
        grid-template-columns: repeat(2, 1fr);
    }

    .pos{
        flex-direction: column;
    }

    .productos,
    .carrito{
        width: 100%;
    }
}

/*
  Móviles
*/
@media(max-width: 600px){

    .venta-detalle{
        grid-template-columns: 1fr;
    }

    .products-header{
        flex-direction: column;

        gap: 15px;

        align-items: stretch;
    }

    .new-btn{
        text-align: center;
    }

    .carrito h2{
        text-align: center;
    }
}




.search-box {
    position: relative;
    width: 400px;
    
}

.search-box i {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: #3498db;
}

.search-box input {
    width: 100%;
    padding: 8px 10px 8px 35px;
    border-radius: 8px;
    border: 1px solid #3498db;
    outline: none;
    transition: 0.2s;
}

.search-box input:focus {
    border-color: #0074b7;
    box-shadow: 0 0 5px rgba(0,116,183,0.3);
}

.btn-carrito{
    background: #71d199;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
}

.btn-carrito:hover{
    background: #2ec16b;
}

.cantidad{
    padding: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.eliminar{
    background: #e9978e;
    color: white;
    border: none;
    padding: 5px 8px;
    border-radius: 5px;
    cursor: pointer;
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
                    <img src="img/1.png" alt="Perfil" class="avatar">
                    <span><?php echo $nombreEmpleado; ?></span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list" >
                    <li><a href="mi_perfil.php"><i class="fas fa-user"></i><span>Mi Perfil</span></a></li>
                    <li><a href="configuracion.html"><i class="fas fa-cog"></i><span>Configuración</span></a></li>
                    <li><a href="logout.php"><i class="fas fa-right-from-bracket"></i><span>Cerrar Sesión</span></a></li>
                </ul>
            </li>
            <!--<button id="toggle-btn"><i class="fas fa-bars"></i></button><br>-->

            <li><a href="dashboard.php"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
            <li><a href="ventas.php"><i class="fas fa-shopping-cart"></i><span>Ventas</span></a></li>
            <li><a href="compras.php"><i class="fas fa-box"></i><span>Compras</span></a></li>
            <li><a href="inventario.php"><i class="fas fa-clipboard-list"></i><span>Inventario</span></a></li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-boxes"></i>
                    <span>Productos</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="p_listaproductos.html"><i class="fas fa-user-tie"></i><span>Lista Productos</span></a></li>
                    <li><a href="p_agregarproductos.html"><i class="fas fa-id-badge"></i><span>Agregar Productos</span></a></li>
                    <li><a href="p_categorias.html"><i class="fas fa-user-cog"></i><span>Categorías</span></a></li>
                </ul>
            </li>
            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-users"></i>
                    <span>Personas</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-list">
                    <li><a href="empleados.php"><i class="fas fa-user-tie"></i><span>Empleados</span></a></li>
                    <li><a href="perfiles.php"><i class="fas fa-id-badge"></i><span>Perfiles</span></a></li>
                    <li><a href="usuarios.php"><i class="fas fa-user-cog"></i><span>Usuarios</span></a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck"></i><span>Proveedores</span></a></li>
                    <li><a href="clientes.php"><i class="fas fa-handshake"></i><span>Clientes</span></a></li>
                </ul>
            </li>
            <li><a href="reportes.php"><i class="fas fa-folder-open"></i><span>Reportes</span></a></li>
            <li><a href="caja.php"><i class="fas fa-envelope"></i><span>Caja</span></a></li>
        </ul>       
    </div>

    
    <main class="contenido">
        
        <header class="dashboard-header">
    
            <!-- FILA SUPERIOR -->
            <div class="header-top">
                <div>
                    <h1>Ventas Detalles - Caja POS</h1>
                    
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





       

                
        <div class="chart-container">    

            <div class="products-header">

                <h3>Venta: </h3>

                <a href="ventas.php" class="new-btn">
                    <i class="fas fa-reply"></i> Regresar  
                </a>

            </div>

                        <!-- mensaje-->

                <form action="" class="venta-detalle">
                    
                    <div class="venta-item">   
                        <label>ID-Venta:</label>
                        <strong><?php echo $venta['id_venta']; ?></strong> 
                    </div>
                    <div class="venta-item">
                        <label>Código-Venta:</label> 
                        <strong><?php echo $venta['codigo']; ?></strong>
                    </div>
                    <div class="venta-item">   
                        <label>ID-user:</label> 
                        <strong><?php echo $venta['usuario']; ?></strong>
                    </div>
                    
                    
                    <div class="venta-item">
                        <label>Total:</label> 
                        <strong>$<?php echo number_format($venta['total'], 2); ?></strong>
                    </div>
                </form>

        
        </div>


        <!-- Datos de la venta  -->
        <section class="pos">

            <div class="productos">
                <h3>Productos</h3>
                
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscador" placeholder="Buscar por Nombre, Codigo o Categoria." onkeyup="filtrarTabla()">
                </div>


                <div class="container-n">
                    <table id="tablaProductos">
                        <thead>
                            <tr>
                                
                                <th>Producto </th>
                                <th>Codigo</th>
                                <th>Precio</th>
                                <th>Stock</th>
                            
                                <th style="text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="product-cell">
                                    <img src="ima_productos/<?= $row['imagen']; ?>" alt="<?= htmlspecialchars($row['nombre']); ?>" class="product-img">
                                    <?= htmlspecialchars($row['nombre']) ?>
                                </td>
                                
                                <td class="id-cell"><?= $row['codigo'] ?></td>
                                <td class="price-cell">$ <?= number_format($row['precio'], 2) ?></td>
                                <td class="stock-cell"><?= $row['stock'] ?> pzs</td>
                            
                                
                                <td style="text-align: center;">
                                    


                                    <button 
                                            class="btn-carrito"
                                            data-id="<?= $row['id_articulo'] ?>"
                                            data-nombre="<?= htmlspecialchars($row['nombre']) ?>"
                                            data-precio="<?= $row['precio'] ?>"
                                            data-stock="<?= $row['stock'] ?>"
                                        >
                                            🧺
                                    </button>

                                

                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>


                    </table>
                </div>

            </div>

            <div class="carrito">
                <h3>Compra actual</h3>

                <table width="100%">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Sub</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody id="carrito-items">

                    </tbody>
                </table>

                <h2>Subtotal: $<span id="subtotal">0.00</span></h2>

                <h2>IVA (16%): $<span id="iva">0.00</span></h2>

                <h2>Total: $<span id="totalFinal">0.00</span></h2>
                

                <button class="btn-vender" id="btnCobrar">Pagar</button>
                <button class="btn-cancelar" onclick="window.location.href='ventas.php'">Cancelar</button>
            </div>

        </section>

     
        
        <footer class="footer">

            <p class="copy">© 2026 Farmacias Omega Oaxaca - Todos los derechos reservados</p>

        </footer>


    </main>


    <script>

        document.addEventListener('DOMContentLoaded', () => {

            // BOTONES AGREGAR
            const botones = document.querySelectorAll('.btn-carrito');

            botones.forEach(btn => {

                btn.addEventListener('click', agregarAlCarrito);

            });

            // BOTÓN PAGAR
            document.getElementById('btnCobrar')
                .addEventListener('click', pagarVenta);

        });


        // ======================================================
        // AGREGAR PRODUCTO
        // ======================================================

        function agregarAlCarrito(e){

            const btn = e.currentTarget;

            const id = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            const precio = parseFloat(btn.dataset.precio);
            const stock = parseInt(btn.dataset.stock);

            const carrito = document.getElementById('carrito-items');

            // VALIDAR SI YA EXISTE
            const filaExistente = document.querySelector(`tr[data-id="${id}"]`);

            if(filaExistente){

                const inputCantidad =
                    filaExistente.querySelector('.cantidad');

                let nuevaCantidad =
                    parseInt(inputCantidad.value) + 1;

                // VALIDAR STOCK
                if(nuevaCantidad > stock){

                    alert(
                        `No hay suficiente stock.\nStock disponible: ${stock}`
                    );

                    return;
                }

                inputCantidad.value = nuevaCantidad;

                actualizarFila(filaExistente);

                actualizarTotal();

                return;
            }

            // CREAR FILA
            const tr = document.createElement('tr');

            tr.setAttribute('data-id', id);
            tr.setAttribute('data-stock', stock);

            tr.innerHTML = `
            
                <td>${nombre}</td>

                <td class="precio">
                    ${precio.toFixed(2)}
                </td>

                <td>
                    <input 
                        type="number"
                        value="1"
                        min="1"
                        class="cantidad"
                        style="width:60px;"
                    >
                </td>

                <td class="subtotal">
                    ${precio.toFixed(2)}
                </td>

                <td>
                    <button class="eliminar">
                        ❌
                    </button>
                </td>
            `;

            carrito.appendChild(tr);

            // ======================================================
            // CAMBIAR CANTIDAD
            // ======================================================

            tr.querySelector('.cantidad')
                .addEventListener('input', (e) => {

                    const input = e.target;

                    const stockDisponible =
                        parseInt(tr.dataset.stock);

                    let cantidad =
                        parseInt(input.value);

                    // VALIDAR VACÍO
                    if(isNaN(cantidad) || cantidad < 1){

                        cantidad = 1;

                        input.value = 1;
                    }

                    // VALIDAR STOCK
                    if(cantidad > stockDisponible){

                        alert(
                            `No hay suficiente stock.\nStock disponible: ${stockDisponible}`
                        );

                        input.value = stockDisponible;

                        cantidad = stockDisponible;
                    }

                    actualizarFila(tr);

                    actualizarTotal();

                });

            // ======================================================
            // ELIMINAR
            // ======================================================

            tr.querySelector('.eliminar')
                .addEventListener('click', () => {

                    tr.remove();

                    actualizarTotal();

                });

            actualizarTotal();
        }


        // ======================================================
        // ACTUALIZAR FILA
        // ======================================================

        function actualizarFila(tr){

            const precio = parseFloat(
                tr.querySelector('.precio').innerText
            );

            const cantidad = parseInt(
                tr.querySelector('.cantidad').value
            );

            const subtotal = precio * cantidad;

            tr.querySelector('.subtotal').innerText =
                subtotal.toFixed(2);
        }


        // ======================================================
        // ACTUALIZAR TOTALES
        // ======================================================

        function actualizarTotal(){

            let subtotalGeneral = 0;

            const filas = document.querySelectorAll(
                '#carrito-items tr'
            );

            filas.forEach(tr => {

                const subtotal = parseFloat(
                    tr.querySelector('.subtotal').innerText
                );

                subtotalGeneral += subtotal;

            });

            // IVA
            const iva = subtotalGeneral * 0.16;

            // TOTAL
            const totalFinal = subtotalGeneral + iva;

            // MOSTRAR
            document.getElementById('subtotal').innerText =
                subtotalGeneral.toFixed(2);

            document.getElementById('iva').innerText =
                iva.toFixed(2);

            document.getElementById('totalFinal').innerText =
                totalFinal.toFixed(2);
        }


        // ======================================================
        // PAGAR VENTA
        // ======================================================

        async function pagarVenta(){

            const filas = document.querySelectorAll(
                '#carrito-items tr'
            );

            // VALIDAR CARRITO
            if(filas.length === 0){

                alert('El carrito está vacío');

                return;
            }

            let productos = [];

            filas.forEach(tr => {

                productos.push({

                    id_articulo : tr.dataset.id,

                    cantidad : parseInt(
                        tr.querySelector('.cantidad').value
                    ),

                    precio : parseFloat(
                        tr.querySelector('.precio').innerText
                    ),

                    subtotal : parseFloat(
                        tr.querySelector('.subtotal').innerText
                    )
                });

            });

            // TOTALES
            const subtotal = parseFloat(
                document.getElementById('subtotal').innerText
            );

            const iva = parseFloat(
                document.getElementById('iva').innerText
            );

            const total = parseFloat(
                document.getElementById('totalFinal').innerText
            );

            // ID VENTA
            const idVenta = <?= $venta['id_venta']; ?>;

            try{

                const response = await fetch(
                    'guardar_venta.php',
                    {

                        method : 'POST',

                        headers : {
                            'Content-Type': 'application/json'
                        },

                        body : JSON.stringify({

                            id_venta : idVenta,

                            subtotal : subtotal,

                            iva : iva,

                            total : total,

                            productos : productos
                        })
                    }
                );

                const data = await response.json();

                if(data.success){

                    //alert('Productos guardados correctamente ☑️');

                    // LIMPIAR CARRITO
                    document.getElementById(
                        'carrito-items'
                    ).innerHTML = '';

                    actualizarTotal();
                    

                    // REDIRECCIONAR
                    window.location.href = 'ventas_clientes_metodos.php?id=' + idVenta;


                }else{

                    alert(data.mensaje);
                }

            }catch(error){

                console.error(error);

                alert('Error al guardar venta');
            }
        }

    </script>


   
    

</body>
</html>

<?php
$conn->close();
?>