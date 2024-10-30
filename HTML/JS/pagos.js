document.addEventListener("DOMContentLoaded", function() {
    const efectivoForm = document.getElementById("efectivo-form");
    const nequiForm = document.getElementById("nequi-form");

    function showForm(paymentMethod) {
        if (paymentMethod === "efectivo") {
            efectivoForm.style.display = "block";
            nequiForm.style.display = "none";
        } else if (paymentMethod === "nequi") {
            efectivoForm.style.display = "none";
            nequiForm.style.display = "block";
        }
    }

    document.querySelector(".payment-images .payment-image:nth-child(1)").addEventListener("click", function() {
        showForm("efectivo");
    });

    document.querySelector(".payment-images .payment-image:nth-child(2)").addEventListener("click", function() {
        showForm("nequi");
    });
});
