function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    if (sidebar.style.width === "250px") {
        sidebar.style.width = "0";
    } else {
        sidebar.style.width = "250px";
    }
}

function addElement() {
    // Lógica para agregar un nuevo elemento
    alert("Agregar nuevo elemento");
}

function editElement(id) {
    // Lógica para editar un elemento existente
    alert("Editar elemento con ID: " + id);
}

function deleteElement(id) {
    // Lógica para eliminar un elemento existente
    var confirmation = confirm("¿Estás seguro de que deseas eliminar el elemento con ID: " + id + "?");
    if (confirmation) {
        alert("Elemento con ID: " + id + " eliminado");
    }
}
