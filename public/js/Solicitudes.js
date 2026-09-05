document.addEventListener('DOMContentLoaded', async () => {
    const selectEquipo = document.getElementById('equipo_id');
    const form = document.getElementById('formSolicitud');
    const mensajeDiv = document.getElementById('mensaje');

    // cargar la lista de equipos disponibles al abrir la pagina (Usando acargar_archivo =3)

    try {
        const response = await fetch('index.php?cargar_archivo=3');
        const equipos = await response.json();

        selectEquipo.innerHTML = '<option value="">-- Seleccione un euqipo --</option>';

        if (equipos.length === 0){
            selectEquipo.innerHTML = '<option value=""> No hay equipos registrados</option>';
        }else{
            equipos.forEach(eq => {
                const option = document.createElement('option');
                option.value = eq.id;
                option.textContent = `${eq.equipo_nombre} (Cliente: ${eq.cliente_nombre})`;
                selectEquipo.appendChild(option);

            });
        }
    } catch (error){
        console.error('Error al cargar equipos:',error);
        selectEquipo.innerHTML = '<option value="">Error al cargar equipos </option>';
    }

    // envair la solicitud de mantenimiento (Usando cargar_archivo = 2)
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const payload = {
            cargar_archivo: 2, // parametro de enrutamiento para RF02
            equipo_id: selectEquipo.value,
            descripcion_problema: document.getElementById('descripcion_problema'). value.trim(),
            prioridad: document.getElementById('prioridad').value
        };

        try{
            const response = await fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'applicaction/json'
                },
                body:JSON.stringify(payload)
            });
            const result = await response.json();

            mensajeDiv.style.display = 'block';
            if (result.success) {
                mensajeDiv.classNme = 'exito';
                mensajeDiv.textContent = result.message;
                form.reset();
                selectEquipo.selectedIndex = 0;
            } else {
                mensajeDiv.classNme = 'error';
                mensajeDiv.textContent = result.message;
            }
        } catch (error){
            console.error('Error de red:', error );
            mensajeDiv.style.display ='block' ;
            mensajeDiv.className = 'error';
            mensajeDiv.textContent = 'Error al comunicarse con el servidor.';
        }
    });


});