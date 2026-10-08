document.addEventListener("DOMContentLoaded", function () {

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
       SUBMENÚ PRODUCTOS (ACORDEÓN)
    ==========================*/
    document.querySelectorAll('.submenu-toggle').forEach(btn => {

        btn.addEventListener('click', e => {
            e.preventDefault();

            const submenu = btn.parentElement;

            // cerrar todos los submenus
            document.querySelectorAll('.submenu').forEach(item => {
                if (item !== submenu) {
                    item.classList.remove('open');
                }
            });

            // abrir o cerrar el seleccionado
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
    MENU ACTIVO SEGÚN PÁGINA
    =========================*/

    const currentPage = window.location.pathname.split("/").pop();

    document.querySelectorAll(".sidebar a").forEach(link => {

        const linkPage = link.getAttribute("href");

        if (linkPage === currentPage) {

            link.classList.add("active");

            // Si pertenece a un submenu, abrirlo
            const parentSubmenu = link.closest(".submenu");

            if (parentSubmenu) {
                parentSubmenu.classList.add("open");
            }

        }

    });

});

function filtrarTabla() {
        const input = document.getElementById("buscador");
        const filtro = input.value.toLowerCase();
        const tabla = document.getElementById("tablaProductos");
        const filas = tabla.getElementsByTagName("tr");

        for (let i = 1; i < filas.length; i++) {
            const celdas = filas[i].getElementsByTagName("td");
            let mostrar = false;

            for (let j = 0; j < celdas.length; j++) {
            const texto = celdas[j].textContent.toLowerCase();
            if (texto.includes(filtro)) {
                mostrar = true;
                break;
            }
            }

            filas[i].style.display = mostrar ? "" : "none";
        }
}