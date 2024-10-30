function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    var mainContent = document.getElementById("main-content");

    if (sidebar.style.width === "250px") {
        sidebar.style.width = "0";
        mainContent.style.marginLeft = "0";
    } else {
        sidebar.style.width = "250px";
        mainContent.style.marginLeft = "250px";
    }
}

function showSection(sectionId) {
    var sections = document.getElementsByClassName('section');
    for (var i = 0; i < sections.length; i++) {
        sections[i].classList.remove('active');
    }
    var section = document.getElementById(sectionId);
    if (section) {
        section.classList.add('active');
    }
}

document.getElementById('logout-button').addEventListener('click', function(event) {
    event.preventDefault(); // Prevenir el envío del formulario por defecto
    if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        document.getElementById('logout-form').submit(); // Enviar el formulario si el usuario confirma
    }
    
});

