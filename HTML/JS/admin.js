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

function addElement() {
    alert("Agregar nuevo elemento");
}

function editElement(id) {
    alert("Editar elemento con ID: " + id);
}

function deleteElement(id) {
    var confirmation = confirm("¿Estás seguro de que deseas eliminar el elemento con ID: " + id + "?");
    if (confirmation) {
        alert("Elemento con ID: " + id + " eliminado");
    }
}
