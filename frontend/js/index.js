document.addEventListener("DOMContentLoaded", function () {
  const botonReservar = document.querySelector(".btn-reserve");
  botonReservar.addEventListener("click", redirigir);
  botonReservar.addEventListener("touchstart", redirigir);

  const chatbotShortcut = document.getElementById('chatbot-shortcut');
  if (chatbotShortcut) {
    chatbotShortcut.addEventListener('click', function(e) {
      e.preventDefault();
      const chatbotToggle = document.getElementById('chatbot-toggle');
      if (chatbotToggle) {
        chatbotToggle.click();
      }
    });
  }

  const menuBtn = document.getElementById('menuBtn');
  const radialMenu = document.getElementById('radialMenu');
  if (menuBtn && radialMenu) {
    menuBtn.onclick = function() {
        radialMenu.classList.toggle('open');
    };
  }
  
  document.addEventListener('click', function(e) {
      if (!radialMenu.contains(e.target)) {
          radialMenu.classList.remove('open');
      }
  });

  const searchInput = document.querySelector('.search-input');
  const searchBtn = document.querySelector('.search-btn');

  searchBtn.addEventListener('click', function() {
      searchInput.focus();
  });

  searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
          alert('Buscando: ' + this.value);
      }
  });

  (function () {
    const a = 2
    alert("Binevenido espero que te sea de ayuda este sitio web, si tienes alguna duda o sugerencia no dudes en contactarme a mi correo lucaszv2006@gmail.com o en el boton de contacto enla barra de opciones")

    function test() {
      console.console.log("test", a);
    }

    test ();
  }) ();
});

window.addEventListener('DOMContentLoaded', function() {
  setTimeout(function() {
    document.getElementById('loader').style.display = 'none';
  }, 2000); 
});
