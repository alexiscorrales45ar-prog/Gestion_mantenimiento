document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const usuario = document.getElementById('usuario').value.trim();
    const password = document.getElementById('password').value.trim();
    const mensajeError = document.getElementById('mensajeError');

    mensajeError.style.display = 'none';
    mensajeError.textContent = '';

    try {
        // Petición asíncrona al enrutador central de PHP
        const response = await fetch('../index.php?cargar_archivo=7', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ usuario, password })
        });

        const resultado = await response.json();

        if (resultado.success) {
            // Redirección basada en el rol devuelto por la base de datos
            switch (resultado.rol) {
                case 'admin':
                case 'supervisor':
                    window.location.href = 'tecnico.html'; // O panel de administración
                    break;
                case 'tecnico':
                    window.location.href = 'tecnico.html';
                    break;
                case 'cliente':
                    window.location.href = 'cliente.html';
                    break;
                default:
                    window.location.href = 'solicitudes.html';
                    break;
            }
        } else {
            mensajeError.textContent = resultado.message || 'Credenciales incorrectas.';
            mensajeError.style.display = 'block';
        }
    } catch (error) {
        console.error('Error en la petición:', error);
        mensajeError.textContent = 'Ocurrió un error al conectar con el servidor.';
        mensajeError.style.display = 'block';
    }
});