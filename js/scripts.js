/*!
* Start Bootstrap - Modern Business v5.0.7 (https://startbootstrap.com/template-overviews/modern-business)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-modern-business/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project
let whatsappBtn = document.getElementById("whatsappBtn");
    if (whatsappBtn) {
        whatsappBtn.addEventListener("click", function() {
            let phoneNumber = "573235606106"; // Reemplaza con tu número
            let message = encodeURIComponent("¡Hola! Quiero más información.");
            let url = `https://wa.me/${phoneNumber}?text=${message}`;
            window.open(url, "_blank");
        });
    }