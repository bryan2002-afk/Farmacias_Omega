document.addEventListener("DOMContentLoaded", function () {

    

    /* =========================
       SIDEBAR TOGGLE
    ==========================*/
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');
    const contenido = document.querySelector('.contenido');

    if (toggleBtn && sidebar && contenido) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            contenido.classList.toggle('collapsed');
        });
    }

    /* =========================
       SUBMENÚ (ACORDEÓN)
    ==========================*/
    document.querySelectorAll('.submenu-toggle').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();

            const submenu = btn.parentElement;

            document.querySelectorAll('.submenu').forEach(item => {
                if (item !== submenu) {
                    item.classList.remove('open');
                }
            });

            submenu.classList.toggle('open');
        });
    });

    /* =========================
       MENÚ PERFIL (SI EXISTE)
    ==========================*/
    const profileBtn = document.querySelector('.profile-btn');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    const arrow = document.querySelector('.arrow');

    if (profileBtn && dropdownMenu && arrow) {

        profileBtn.addEventListener('click', () => {
            dropdownMenu.classList.toggle('active');
            arrow.classList.toggle('rotate');
        });

        window.addEventListener('click', (e) => {
            if (!profileBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('active');
                arrow.classList.remove('rotate');
            }
        });

    }

    /* =========================
       GRÁFICA
    ==========================*/
    const canvas = document.getElementById("ventasChart");

    if (canvas) {
        const ctx = canvas.getContext("2d");

        new Chart(ctx, {
            type: "line",
            data: {
                labels: ["Lun","Mar","Mié","Jue","Vie","Sáb","Dom"],
                datasets: [{
                    label: "Ventas (MXN)",
                    data: [1200,1900,3000,2500,2200,3100,2800],
                    borderColor: "#2ecc71",
                    backgroundColor: "rgba(46,204,113,0.15)",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    /* =========================
       MENÚ ACTIVO SEGÚN PÁGINA
    ==========================*/
    const currentPage = window.location.pathname.split("/").pop().split("?")[0];

    document.querySelectorAll(".sidebar a").forEach(link => {
        const linkPage = link.getAttribute("href");

        if (linkPage === currentPage) {
            link.classList.add("active");

            const parentSubmenu = link.closest(".submenu");
            if (parentSubmenu) {
                parentSubmenu.classList.add("open");
            }
        }
    });

    /* =========================
       MODAL (ESC + CLICK FUERA)
    ==========================*/
    const modal = document.getElementById("productModal");

    if (modal) {

        document.addEventListener("keydown", function(e){
            if(e.key === "Escape"){
                modal.style.display = "none";
            }
        });

        window.addEventListener("click", function (e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
    }

    /* =========================
       FILTRO DE TABLA
    ==========================*/
    const input = document.getElementById("buscador");

    if (input) {

        let timeout;

        input.addEventListener("keyup", () => {
            clearTimeout(timeout);
            timeout = setTimeout(filtrarTabla, 200); // debounce
        });

    }

});




function filtrarTabla() {
    const input = document.getElementById("buscador");
    if (!input) return;

    const filtro = input.value.toLowerCase();
    // Seleccionamos solo las filas del cuerpo de la tabla
    const filas = document.querySelectorAll("#tablaProductos tbody tr");

    filas.forEach(fila => {
        // Buscamos específicamente en las celdas que contienen datos útiles:
        // celda 0: Nombre, celda 1: Código, celda 4: Categoría
        const nombre = fila.cells[0]?.textContent.toLowerCase() || "";
        const codigo = fila.cells[1]?.textContent.toLowerCase() || "";
        const categoria = fila.cells[4]?.textContent.toLowerCase() || "";

        // Si el filtro coincide con cualquiera de esos tres campos
        if (nombre.includes(filtro) || codigo.includes(filtro) || categoria.includes(filtro)) {
            fila.style.display = ""; // Mostrar
        } else {
            fila.style.display = "none"; // Ocultar
        }
    });
}


/* =========================
   VER EMPLEADO
=========================

function verArticulo(data) {

    document.getElementById("modalNombre").textContent = data.nombre;
    document.getElementById("modalCodigo").textContent = data.codigo;
    document.getElementById("modalDescripcion").textContent = data.descripcion;
    document.getElementById("modalPrecio").textContent = data.precio;
    document.getElementById("modalStock").textContent = data.stock ?? "Sin Producto";
    document.getElementById("modalCategoria").textContent = data.categoria ?? "Sin Categoria";

    document.getElementById("modalEstado").textContent =
        data.estado == 1 ? "Activo" : "Inactivo";

    // Imagen
    let img = document.getElementById("modalImagen");
    if (data.imagen) {
        img.src = "ima_productos/" + data.imagen;
    } else {
        img.src = "";
    }

    document.getElementById("productModal").style.display = "flex";
}

function cerrarModal() {
    document.getElementById("productModal").style.display = "none";
}

*/

    /* =========================
       MODAL PRODUCTO (BOTÓN OJO)
    ==========================*/

    const modal = document.getElementById("productModal");

if (modal) {

    const closeModal = document.querySelector(".close-modal");

    document.addEventListener("keydown", function(e){
        if(e.key === "Escape"){
            modal.style.display = "none";
        }
    });

    const modalNombre = document.getElementById("modalNombre");
    const modalCodigo = document.getElementById("modalCodigo");
    const modalDescripcion = document.getElementById("modalDescripcion");
    const modalCategoria = document.getElementById("modalCategoria");
    const modalPrecio = document.getElementById("modalPrecio");
    const modalStock = document.getElementById("modalStock");
    const modalImagen = document.getElementById("modalImagen");
    const modalActivo = document.getElementById("modalActivo");

    /* EVENTO GLOBAL PARA BOTÓN OJO */
    document.addEventListener("click", function(e){

        const btn = e.target.closest(".product-eye");

        if(!btn) return;

        const nombre = btn.getAttribute("data-nombre");
        const codigo = btn.getAttribute("data-codigo");
        const descripcion = btn.getAttribute("data-descripcion");
        const categoria = btn.getAttribute("data-categoria");
        const precio = btn.getAttribute("data-precio");
        const stock = btn.getAttribute("data-stock");
        const imagen = btn.getAttribute("data-imagen");
        const activo = btn.getAttribute("data-activo");

        modalNombre.textContent = nombre;
        modalCodigo.textContent = codigo;
        modalDescripcion.textContent = descripcion;
        modalCategoria.textContent = categoria;
        modalPrecio.textContent = precio;
        modalStock.textContent = stock;

        // ✅ CORRECCIÓN AQUÍ
        modalActivo.textContent = (parseInt(activo) === 1) ? "Activo" : "Inactivo";

        modalImagen.src = "ima_productos/" + imagen;

        modal.style.display = "flex";

    });


        if (closeModal) {
            closeModal.addEventListener("click", function () {
                modal.style.display = "none";
            });
        }

        window.addEventListener("click", function (e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });

    }

    

