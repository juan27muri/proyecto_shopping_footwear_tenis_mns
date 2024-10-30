document.addEventListener("DOMContentLoaded", function() {
    const cartItems = document.querySelector(".cart-items");
    const totalItems = document.getElementById("total-items");
    const totalPrice = document.getElementById("total-price");

    cartItems.addEventListener("change", updateCart);
    cartItems.addEventListener("click", function(event) {
        if (event.target.classList.contains("remove-item")) {
            removeItem(event.target.closest(".cart-item"));
        }
    });

    function updateCart() {
        let items = cartItems.querySelectorAll(".cart-item");
        let total = 0;
        let itemCount = 0;

        items.forEach(function(item) {
            let quantity = item.querySelector("input[name='quantity']").value;
            let price = parseFloat(item.querySelector(".cart-item-details p:nth-child(3)").textContent.replace('$', ''));
            total += quantity * price;
            itemCount += parseInt(quantity);
        });

        totalItems.textContent = itemCount;
        totalPrice.textContent = "$" + total.toFixed(2);
    }

    function removeItem(item) {
        item.remove();
        updateCart();
    }
});
