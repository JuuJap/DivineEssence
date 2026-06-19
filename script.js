
document.addEventListener('DOMContentLoaded',()=>{
// MENU
const categorias = document.querySelectorAll(".menu a");

categorias.forEach((categoria) => {
  categoria.addEventListener("click", (e) => {
    e.preventDefault();

    alert("Você clicou em: " + categoria.textContent);
  });
});

// BOTÃO VOLTAR AO TOPO
const btnTopo = document.getElementById("btnTopo");

btnTopo.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth"
  });
});

window.addEventListener("scroll", () => {

  if(window.scrollY > 300){
    btnTopo.style.display = "block";
  }else{
    btnTopo.style.display = "none";
  }

});

// TEMA

const btnTema = document.getElementById("btnTema");
const logoSite = document.getElementById("logoSite");

function trocarLogo(novaLogo){

    logoSite.style.opacity = "0";

    setTimeout(() => {
        logoSite.src = novaLogo;
        logoSite.style.opacity = "1";
    }, 150);

}

// Carrega tema salvo
if(localStorage.getItem("tema") === "dark"){

    document.body.classList.add("dark-mode");

    logoSite.src = "img/LDE-dark.png";

}else{

    logoSite.src = "img/LDE.png";

}

btnTema.addEventListener("click", () => {

    btnTema.classList.add("girando");

    document.body.classList.toggle("dark-mode");

    if(document.body.classList.contains("dark-mode")){

        localStorage.setItem("tema", "dark");

        trocarLogo("img/LDE-dark.png");

    }else{

        localStorage.setItem("tema", "light");

        trocarLogo("img/LDE.png");

    }

    setTimeout(() => {
        btnTema.classList.remove("girando");
    }, 500);

});

if(localStorage.getItem('theme')==='dark'){
 document.body.classList.add('dark-mode');
}

const icon=document.querySelector('.icon');
window.toggleTheme=function(){
 document.body.classList.toggle('dark-mode');
 const isDark=document.body.classList.contains('dark-mode');
 localStorage.setItem('theme', isDark ? 'dark':'light');
 if(icon){ icon.textContent=isDark?'☀️':'🌙'; }
}

window.toggleMenu=function(){
 const nav=document.querySelector('nav');
 if(nav){ nav.classList.toggle('active'); }
}
});
function toggleTheme() {
    const body = document.body;
    const icon = document.querySelector(".icon");
    const logo = document.getElementById("logoSite");

    body.classList.toggle("dark-mode");

    const isDark = body.classList.contains("dark-mode");
    localStorage.setItem("theme", isDark ? "dark" : "light");

    if (logo) {
        logo.src = isDark
            ? "img/LDE-dark2.png"
            : "img/LDE2.png";
    }

    if (icon) {
        icon.textContent = isDark ? "☀️" : "🌙";

        icon.classList.add("rotate");

        setTimeout(() => {
            icon.classList.remove("rotate");
        }, 300);
    }
}

/* Atualiza ícone sozinho */
function updateIcon() {
    const icon = document.querySelector(".icon");

    if (!icon) return; // evita erro em páginas sem botão

    const isDark = document.body.classList.contains("dark-mode");
    icon.textContent = isDark ? "☀️" : "🌙";
}

/* Mantém o tema ao trocar de página */
window.onload = function () {
    const savedTheme = localStorage.getItem("theme");
    const logo = document.getElementById("logoSite");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-mode");

        if (logo) {
            logo.src = "img/LDE-dark2.png";
        }
    } else {
        if (logo) {
            logo.src = "img/LDE2.png";
        }
    }

    updateIcon();
};

function toggleMenu() {
    const nav = document.querySelector(".header nav");
    const toggle = document.querySelector(".menu-toggle");

    nav.classList.toggle("active");
    toggle.classList.toggle("active");
}