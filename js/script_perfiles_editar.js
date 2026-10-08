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
=========================*/
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
}


/* =========================
   VER EMPLEADO
=========================*/
function verEmpleado(empleado) {

    const modal = document.getElementById("productModal");
    /*if (!modal) return;*/

    modal.style.display = "flex";

    document.getElementById("modalNombre").textContent =
        empleado.nombre + " " + (empleado.nombre_seg || "");

    document.getElementById("modalApellido").textContent =
        empleado.apellido_p + " " + (empleado.apellido_m || "");

    document.getElementById("modalCurp").textContent = empleado.curp;
    document.getElementById("modalTelefono").textContent = empleado.telefono;
    document.getElementById("modalCorreo").textContent =
        empleado.correo || "Sin correo";
}


/* =========================
   CERRAR MODAL
=========================*/
function cerrarModal() {
    const modal = document.getElementById("productModal");
    if (modal) modal.style.display = "none";
}

// Mostrar preview de imagen antes de subir
function previewImage(event) {
    const reader = new FileReader();

    reader.onload = function(){
        const output = document.getElementById('preview');
        output.src = reader.result;
        output.style.display = "block"; // 👈 mostrar la imagen
    };

    reader.readAsDataURL(event.target.files[0]);
}

    