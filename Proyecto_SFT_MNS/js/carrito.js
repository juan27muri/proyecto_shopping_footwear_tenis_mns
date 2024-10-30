document.getElementById('logout-button').addEventListener('click', function(event) {
    event.preventDefault(); // Prevenir el envío del formulario por defecto
    if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        document.getElementById('logout-form').submit(); // Enviar el formulario si el usuario confirma
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const removeButtons = document.querySelectorAll('.remove-item');

    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productoId;
            const newQuantity = parseInt(this.value);
            const pricePerUnit = parseFloat(this.dataset.precio);
            const size = this.dataset.talla;

            if (newQuantity > 0) {
                updateCart(productId, newQuantity, pricePerUnit, size, this);
            } else {
                this.value = 1; // Establecer a 1 si el usuario ingresa un valor no válido
            }
        });
    });

    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productoId;
            const size = this.dataset.talla;
            removeFromCart(productId, size);
        });
    });

    function removeFromCart(productId, size) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../functions_cliente/eliminar_del_carrito.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.error) {
                    alert(response.error);
                } else {
                    // Recargar la página para actualizar los totales
                    window.location.reload();
                }
            } else {
                alert('Error en la eliminación del producto');
            }
        }
    };

    xhr.send(`producto_id=${productId}&talla=${size}`);
}
});
