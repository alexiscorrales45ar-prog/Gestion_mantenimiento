document.getElementById('formEjecucionTecnico').addEventListener('submit',async function(e){
    e.preventDefault();

    const datos = {
        orden_id: document.getElementById('orden_id').value,
        estado: document.getElementById('estado').value,
        actividades:document.getElementById('actividades').value,
        costo: parseFloat(document.getElementById('costo').value) || 0.0,
        fecha_inicio:document.getElementById('fecha_inicio').value,
        fecha_fin:document.getElementById('fecha_fin').value
    };

    const divMensaje = document.getElementById('mensajeTecnico');

    try{
        const respuesta = await fetch('index.php?cargar_archivo=5', { // ajusta el indice segun enrutador central
            method: 'POST',
            headers:{'Content-type': 'application/json'},
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        divMensaje.style.display = 'block';
        if (resultado.success){
            divMensaje.className='success';
            divMensaje.textContent=resultado.message || '!Orden actualizada con éxito';
            document.getElementById('formEjecucionTecnica').reset();
        } else{
            divMensaje.className = 'error';
            divMensaje.textContent = resultado.message || 'Ocurrio un erro al actualizar la orden.' ;
        }
    } catch (error){
        divMensaje.style.display = 'block';
        divMensaje.className = 'error';
        divMensaje.textContent = 'Error de conexción con el servidor.';
    }
});