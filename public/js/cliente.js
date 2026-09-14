// 1. Registro de cliente
document.getElementById('formCliente').addEventListener('submit', async function(e){
    e.preventDefault();

    const datos = {
        nombre: document.getElementById('nombre').value,
        contacto:document.getElementById('contacto').value,
        direccion:document.getElementById('direccion').value 
    };

    const divMensaje = document.getElementById('mensajeCliente');

    try{
        const respuesta= await fetch('index.php?cargar_archivo=1',{ // Ajusta el indice segun tu erutador central
            method: 'POST',
            headers:{'Content-type': 'applicaction/json'},
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        divMensaje.style.display='block';
        if(resultado.success){
            divMensaje.className = 'success';
            divMensaje.textContent=resultado.message || '!cliente guardado con exito';
            document.getElementById('formCliente').reset();
        } else {
            divMensaje.className = ' error';
            divMensaje.textContent = resultado.message || 'Error al registrar el cliente';
        }
    } catch(error){
        divMensaje.style.display = 'block';
        divMensaje.classNme='error';
        divMensaje.textContent = 'Error de conexion con el servidor.';
    }

});

// 2. Registro de Equipo Asociado
document.getElementById('formEquipo').addEventListener('submit', async function(e) {
    e.preventDefault();

    const datos = {
        cliente_id: document.getElementById('cliente_id').value,
        tipo: document.getElementById('tipo').value,
        marca: document.getElementById('marca').value
    };

    const divMensaje = document.getElementById('mensajeEquipo');

    try {
        const respuesta = await fetch('index.php?cargar_archivo=2', { // Ajusta el índice según tu enrutador central
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        divMensaje.style.display = 'block';
        if (resultado.success) {
            divMensaje.className = 'success';
            divMensaje.textContent = resultado.message || '¡Equipo registrado con éxito!';
            document.getElementById('formEquipo').reset();
        } else {
            divMensaje.className = 'error';
            divMensaje.textContent = resultado.message || 'Error al registrar el equipo.';
        }
    } catch (error) {
        divMensaje.style.display = 'block';
        divMensaje.className = 'error';
        divMensaje.textContent = 'Error de conexión con el servidor.';
    }
});