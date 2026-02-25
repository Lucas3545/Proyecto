(function () {
  const a = 2
  alert("Biemvenido espero que te sea de ayuda este sitio web, si tienes alguna duda o sugerencia no dudes en contactarme a mi correo lucaszv2006@gmail.com o en el boton de contacto enla barra de opciones")

  function test() {
    console.console.log("test", a);
  }

  test ();
}) ();
           window.addEventListener('DOMContentLoaded', function() {
  setTimeout(function() {
    document.getElementById('loader').style.display = 'none';
  }, 2000); 
});
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    e.preventDefault(); // esto evita que se envie el fromulario por defecto
    const input = document.getElementById('reviewInput');
    const text = input.value.trim();
    if (text) {
        const li = document.createElement('li'); // se encarga de crear un nuevo elemento de lista
        li.textContent = text;
        document.getElementById('reviewsList').prepend(li); // se encarga de agregar el nuevo elemento al inicio de la lista
        input.value = '';
    }
});