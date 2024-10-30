
document.addEventListener("DOMContentLoaded", function() {
    var resultado = "{{resultado}}"; // Este valor viene desde el controlador
    if (resultado === "incorrecta") {
        var modal = new bootstrap.Modal(document.getElementById('respuestaIncorrectaModal'));
        modal.show();

        // Redirigir al home después de cerrar el modal
        document.getElementById('closeModal').addEventListener('click', function() {
                window.location.href = 'home';
        });
    }
});
document.addEventListener("DOMContentLoaded", function () {
    let tiempoRestante = 15;
    const timerElement = document.getElementById("timer");
    const preguntaForm = document.getElementById("preguntaForm");

    const cronometro = setInterval(() => {
        tiempoRestante--;
        timerElement.textContent = "Tiempo: " + tiempoRestante + "s";

        if (tiempoRestante <= 0) {
            clearInterval(cronometro);
            mostrarModalTiempoAgotado();
        }
    }, 1000);

    preguntaForm.addEventListener("submit", function () {
        clearInterval(cronometro);
    });

    function mostrarModalTiempoAgotado() {
        const modalBody = document.getElementById("modal-body-text");
        modalBody.innerHTML = "Lo siento, se acabó el tiempo. La respuesta correcta era la opción: <strong>" + respuestaCorrecta + "</strong>";
        const modal = new bootstrap.Modal(document.getElementById('respuestaIncorrectaModal'));
        modal.show();

        document.getElementById('closeModal').addEventListener('click', function () {
            window.location.href = 'home';
        });
    }

    if (resultado === "incorrecta") {
        const modalBody = document.getElementById("modal-body-text");
        modalBody.innerHTML = "Lo siento, tu respuesta es incorrecta. La respuesta correcta era la opción: <strong>" + respuestaCorrecta + "</strong>";
        const modal = new bootstrap.Modal(document.getElementById('respuestaIncorrectaModal'));
        modal.show();

        document.getElementById('closeModal').addEventListener('click', function () {
            window.location.href = 'home';
        });
    }
});
