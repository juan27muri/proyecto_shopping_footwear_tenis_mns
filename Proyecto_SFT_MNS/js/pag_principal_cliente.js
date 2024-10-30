
let currentSlide = 0;

function showSlide(index) {
    const slides = document.querySelectorAll('.carousel-item');
    if (index >= slides.length) {
        currentSlide = 0;
    } else if (index < 0) {
        currentSlide = slides.length - 1;
    } else {
        currentSlide = index;
    }
    const offset = -currentSlide * 100;
    document.querySelector('.carousel-inner').style.transform = `translateX(${offset}%)`;
}

function nextSlide() {
    showSlide(currentSlide + 1);
}

function prevSlide() {
    showSlide(currentSlide - 1);
}

setInterval(nextSlide, 3000);

document.getElementById('logout-button').addEventListener('click', function(event) {
    event.preventDefault(); // Prevenir el envío del formulario por defecto
    if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        document.getElementById('logout-form').submit(); // Enviar el formulario si el usuario confirma
    }
    
});

// Buscar producto
document.getElementById('search-button').addEventListener('click', function() {
    const query = document.getElementById('search-bar').value;

    fetch('../functions_cliente/buscar_producto.php?query=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('search-results');
            resultsDiv.innerHTML = ''; // Limpiar resultados anteriores

            if (data.length > 0) {
                data.forEach(product => {
                    const productDiv = document.createElement('div');
                    productDiv.className = 'search-result';
                    productDiv.innerHTML = `
                        <br><div align="center" class="mosaic">
                            <a href="modelo_zapato.php?producto_id=${product.codigo_producto}" class="photo-container">
                                <img src="../cruds/productos/images/${product.imagen_producto}" alt="${product.nombre_producto}" width="100" height="auto">
                                <div class="photo-title">${product.nombre_producto}</div>
                                <p>$${product.precio_producto}</p>
                            </a>
                        
                        </div><br><br><br><br>
                    `;
                    resultsDiv.appendChild(productDiv);
                });
            } else {
                resultsDiv.innerHTML = '<p>No se encontraron productos.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
});

//Añadir al Carrito

document.addEventListener('DOMContentLoaded', function() {
    // Selecciona el formulario de "Añadir al carrito" en la página del producto
    const addToCartForm = document.getElementById('add-to-cart-form');

    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío del formulario por defecto

            const formData = new FormData(addToCartForm);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../functions_cliente/agregar_al_carrito.php', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('Producto añadido al carrito');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            };
            xhr.send(formData);
        });
    }
});
