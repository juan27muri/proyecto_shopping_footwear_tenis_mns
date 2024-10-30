document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector(".update-form form");

    form.addEventListener("submit", function(event) {
        event.preventDefault();
        alert("Información actualizada correctamente!");
        // Aquí puedes agregar la lógica para enviar el formulario al servidor
    });
});
