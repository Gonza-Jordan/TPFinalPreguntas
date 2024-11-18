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
        modalBody.innerHTML = "Lo siento, se acabó el tiempo.";
        const modal = new bootstrap.Modal(document.getElementById('respuestaIncorrectaModal'));
        modal.show();
    }

    if (resultado === "incorrecta") {
        const modalBody = document.getElementById("modal-body-text");
        modalBody.innerHTML = "La respuesta correcta era la opción: <strong>" + respuestaCorrecta + "</strong>";
        const modalElement = document.getElementById('respuestaIncorrectaModal');

        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });

        modal.show();
    }

    document.getElementById('closeModal').addEventListener('click', function () {
        window.location.href = '/TPFinalPreguntas';
    });
});

