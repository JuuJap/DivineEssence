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

if(localStorage.getItem("tema") === "dark"){
    document.body.classList.add("dark-mode");
}

btnTema.addEventListener("click", () => {

    btnTema.classList.add("girando");

    document.body.classList.toggle("dark-mode");

    if(document.body.classList.contains("dark-mode")){
        localStorage.setItem("tema", "dark");
    } else {
        localStorage.setItem("tema", "light");
    }

    setTimeout(() => {
        btnTema.classList.remove("girando");
    }, 500);

});