document.getElementById('formSolicitud').addEventListener('submit', async function(e) {
    e.preventDefault();

    const datos = {
        cliente_id: document.getElementById('cliente_id').value,
        equipo_id: document.getElementById('equipo_id').value,
        problema: document.getElementById('problema').value,
        prioridad: document.getElementById('prioridad').value,
        fecha_solicitud: document.getElementById('fecha_solicitud').value
    };

    const divMensaje = document.getElementById('mensajeSolicitud');

    try{
        const respuesta = await fetch('index.php?cargar_archivo=3',{ // Ajustar el indice segun tu enrutador central
            method:'POST',
            headers:{'Content-Type': 'application/json'},
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        divMensaje.style.display = 'block';
        if (resultado.success){
            divMensaje.className ='success';
            divMensaje.textContent= resultado.message || 'Solicitud registrada correctamente¡';
            document.getElementById('formSolicitud').reset();
        } else {
            divMensaje.className = 'error';
            DevMensaje.textContent = resultado.message || 'No se puedo registrar la solicitud.';   
        }
    } catch (error){
        divMensaje.style.display='block';
        divMensaje.className = 'error';
        divMensaje.textContent = 'Error de conexión con el servidor.';
    }
});