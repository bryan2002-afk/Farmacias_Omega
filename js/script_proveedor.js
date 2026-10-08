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


/* =========================
   FILTRAR TABLA
=========================
function filtrarTabla() {

    const input = document.getElementById("buscador");
    if (!input) return;

    const filtro = input.value.toLowerCase();
    const filas = document.querySelectorAll("#tablaEmpleados tbody tr");

    filas.forEach(fila => {

        const curp = fila.cells[0].textContent.toLowerCase();
        const nombre = fila.cells[1].textContent.toLowerCase();
        const apellido = fila.cells[2].textContent.toLowerCase();
        const telefono = fila.cells[3].textContent.toLowerCase();

        fila.style.display =
            curp.includes(filtro) ||
            nombre.includes(filtro) ||
            apellido.includes(filtro) ||
            telefono.includes(filtro)
            ? ""
            : "none";
    });
}*/




function filtrarTabla() {

    const input = document.getElementById("buscador");
    if (!input) return;

    const filtro = input.value.toLowerCase();
    const filas = document.querySelectorAll("#tablaProveedores tbody tr");

    filas.forEach(fila => {

        const textoFila = fila.textContent.toLowerCase();

        fila.style.display = textoFila.includes(filtro) ? "" : "none";
    });
}

/*
function filtrarTabla() {

    const input = document.getElementById("buscador");
    if (!input) return;

    const filtro = input.value.toLowerCase();
    const filas = document.querySelectorAll("#tablaUsuarios tbody tr");

    filas.forEach(fila => {

        const usuario  = fila.cells[1].textContent.toLowerCase();
        const correo   = fila.cells[2].textContent.toLowerCase();
        const empleado = fila.cells[3].textContent.toLowerCase();
        const perfil   = fila.cells[4].textContent.toLowerCase();
        const estado   = fila.cells[5].textContent.toLowerCase();

        fila.style.display =
            usuario.includes(filtro) ||
            correo.includes(filtro) ||
            empleado.includes(filtro) ||
            perfil.includes(filtro) ||
            estado.includes(filtro)
            ? ""
            : "none";
    });
}
*/

/* =========================
   VER EMPLEADO
=========================*/

function verProveedor(data) {

    document.getElementById("modalNombre").textContent = data.nombre;
    document.getElementById("modalTelefono").textContent = data.telefono;
    document.getElementById("modalCorreo").textContent = data.correo;
    document.getElementById("modalDireccion").textContent = data.direccion;
    
    // Imagen
    let img = document.getElementById("modalImagen");
    if (data.imagen) {
        img.src = "ima_proveedores/" + data.imagen;
    } else {
        img.src = "";
      
    }

    document.getElementById("productModal").style.display = "flex";
}

function cerrarModal() {
    document.getElementById("productModal").style.display = "none";
}




/*
function verProveedor(data) {

    document.getElementById("modalNombre").textContent = data.nombre;
    document.getElementById("modalTelefono").textContent = data.telefono;
    document.getElementById("modalCorreo").textContent = data.correo;
    document.getElementById("modalDireccion").textContent = data.direccion;

    let img = document.getElementById("modalImagen");

    if (data.imagen && data.imagen.trim() !== "") {
        img.src = "ima_proveedores/" + data.imagen;
        img.style.display = "block";
        img.alt = "Imagen del proveedor";
    } else {
        img.src = "";
        img.alt = "Sin imagen";
        img.style.display = "block";
        img.replaceWith(img); // mantiene referencia visual
        img.outerHTML = '<p id="modalImagen" style="margin-top:10px;">Sin imagen</p>';
    }

    document.getElementById("productModal").style.display = "flex";
}

function cerrarModal() {
    document.getElementById("productModal").style.display = "none";
}*/