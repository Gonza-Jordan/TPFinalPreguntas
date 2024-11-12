document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const errors = urlParams.get('errors');

    if (status === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Registro Exitoso',
            text: '¡Tu cuenta ha sido registrada exitosamente!',
            confirmButtonText: 'Iniciar Sesión',
            timer: 3000,
            timerProgressBar: true,
            allowOutsideClick: false
        }).then((result) => {
            window.location.href = 'http://localhost/TPFinalPreguntas/auth/show';
        });
    } else if (status === 'error_imagen') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al procesar la imagen. Por favor, intenta nuevamente.'
        });
    } else if (status === 'error_registro') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al registrar al usuario. Inténtalo nuevamente.'
        });
    } else if (status === 'error_validacion') {
        Swal.fire({
            icon: 'error',
            title: 'Errores de validación',
            html: decodeURIComponent(errors.replace(/,/g, '<br>'))
        });
    }
});