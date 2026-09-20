// Esperar a que todo el HTML cargue
document.addEventListener('DOMContentLoaded', function() {
    
    const formCliente = document.getElementById('formCliente');
    
    // Validar que el formulario exista en esta página
    if (formCliente) {
        formCliente.addEventListener('submit', async function(e){
            e.preventDefault(); // ¡Aquí detenemos la recarga exitosamente!

            const datos = {
                nombre: document.getElementById('nombre').value,
                telefono: document.getElementById('contacto').value, // <-- Cambio aquí
                direccion: document.getElementById('direccion').value 
            };

            const divMensaje = document.getElementById('mensajeCliente');

            try {
                const respuesta = await fetch('index.php?cargar_archivo=1', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(datos)
                });

                const resultado = await respuesta.json();

                divMensaje.style.display = 'block';
                if(resultado.success) {
                    divMensaje.className = 'success';
                    divMensaje.textContent = resultado.message || '¡Cliente guardado con éxito!';
                    formCliente.reset();
                } else {
                    divMensaje.className = 'error';
                    divMensaje.textContent = resultado.message || 'Error al registrar el cliente';
                }
            } catch(error) {
                divMensaje.style.display = 'block';
                divMensaje.className = 'error';
                divMensaje.textContent = 'Error de conexión con el servidor.';
            }
        });
    }
});