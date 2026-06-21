document.addEventListener("DOMContentLoaded", () => {
    const btnTopo = document.getElementById("btnTopo");
    const btnTema = document.getElementById("btnTema");
    const themeToggle = document.getElementById("theme-toggle");
    const logoSite = document.getElementById("logoSite");
    const icon = document.querySelector(".icon");

    function aplicarLogo() {
        if (!logoSite) return;

        const isDark = document.body.classList.contains("dark-mode");

        logoSite.src = isDark
            ? "img/LDE-dark2.png"
            : "img/LDE2.png";

        logoSite.style.height = "120px";
        logoSite.style.width = "auto";
    }

    function atualizarIcone() {
        const isDark = document.body.classList.contains("dark-mode");

        if (icon) {
            icon.textContent = isDark ? "☀️" : "🌙";
        }

        if (btnTema) {
            btnTema.innerHTML = isDark
                ? '<i class="fa-solid fa-sun"></i>'
                : '<i class="fa-solid fa-moon"></i>';
        }
    }

    function aplicarTemaSalvo() {
        const temaSalvo = localStorage.getItem("tema");

        if (temaSalvo === "dark") {
            document.body.classList.add("dark-mode");
        } else {
            document.body.classList.remove("dark-mode");
        }

        aplicarLogo();
        atualizarIcone();
    }

    function alternarTema() {
        document.body.classList.toggle("dark-mode");

        const isDark = document.body.classList.contains("dark-mode");

        localStorage.setItem("tema", isDark ? "dark" : "light");

        aplicarLogo();
        atualizarIcone();

        if (btnTema) {
            btnTema.classList.add("girando");

            setTimeout(() => {
                btnTema.classList.remove("girando");
            }, 500);
        }

        if (icon) {
            icon.classList.add("rotate");

            setTimeout(() => {
                icon.classList.remove("rotate");
            }, 300);
        }
    }

    aplicarTemaSalvo();

    if (btnTema) {
        btnTema.addEventListener("click", alternarTema);
    }

    if (themeToggle) {
        themeToggle.addEventListener("click", alternarTema);
    }

    if (btnTopo) {
        btnTopo.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });

        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                btnTopo.style.display = "block";
            } else {
                btnTopo.style.display = "none";
            }
        });
    }

    window.toggleTheme = alternarTema;

    window.toggleMenu = function () {
        const nav = document.querySelector(".header nav");
        const toggle = document.querySelector(".menu-toggle");

        if (nav) {
            nav.classList.toggle("active");
        }

        if (toggle) {
            toggle.classList.toggle("active");
        }
    };
});