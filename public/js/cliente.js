document.addEventListener('DOMContentLoaded', () => {
    // Referencias a elementos del DOM
    const formBuscar = document.getElementById('formBuscarCliente');
    const inputBuscar = document.getElementById('buscarDocumento');
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

    const seccionNuevo = document.getElementById('seccionClienteNuevo');
    const seccionExistente = document.getElementById('seccionClienteExistente');

    const formClienteNuevo = document.getElementById('formClienteNuevo');
    const formEquipoAdicional = document.getElementById('formEquipoAdicional');

    const infoClienteEncontrado = document.getElementById('infoClienteEncontrado');
    const inputClienteIdHidden = document.getElementById('existente_cliente_id');
    const mensajeSistema = document.getElementById('mensajeSistema');

    // Función auxiliar para mostrar alertas en pantalla
    function mostrarMensaje(texto, esExito) {
        if (!mensajeSistema) return;
        mensajeSistema.style.display = 'block';
        mensajeSistema.style.backgroundColor = esExito ? '#d4edda' : '#f8d7da';
        mensajeSistema.style.color = esExito ? '#155724' : '#721c24';
        mensajeSistema.style.border = esExito ? '1px solid #c3e6cb' : '1px solid #f5c6cb';
        mensajeSistema.innerText = texto;
        setTimeout(() => { mensajeSistema.style.display = 'none'; }, 5000);
    }

    // 1. BUSCAR CLIENTE POR CÉDULA O TELÉFONO (Case 10)
    if (formBuscar) {
        formBuscar.addEventListener('submit', (e) => {
            e.preventDefault();
            const documento = inputBuscar.value.trim();
            if (!documento) return;

            fetch(`index.php?cargar_archivo=10&documento=${encodeURIComponent(documento)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        const c = data.data;
                        // Mostrar sección de cliente existente
                        if (seccionNuevo) seccionNuevo.style.display = 'none';
                        if (seccionExistente) seccionExistente.style.display = 'block';
                        if (btnLimpiar) btnLimpiar.style.display = 'inline-block';

                        if (inputClienteIdHidden) inputClienteIdHidden.value = c.id;
                        if (infoClienteEncontrado) {
                            infoClienteEncontrado.innerHTML = `
                                <p style="margin:0; color:#31708f;">
                                    <strong>Cliente Registrado:</strong> ${c.nombre} | 
                                    <strong>Cédula:</strong> ${c.cedula || 'N/A'} | 
                                    <strong>Teléfono:</strong> ${c.telefono} | 
                                    <strong>Dirección:</strong> ${c.direccion || 'N/A'}
                                </p>
                            `;
                        }
                        mostrarMensaje('Cliente encontrado en el sistema.', true);
                    } else {
                        // Si no existe, sugerir el formulario de registro completo
                        if (seccionExistente) seccionExistente.style.display = 'none';
                        if (seccionNuevo) seccionNuevo.style.display = 'block';
                        if (btnLimpiar) btnLimpiar.style.display = 'inline-block';
                        mostrarMensaje('Cliente no registrado. Ingrese los datos a continuación.', false);
                    }
                })
                .catch(err => {
                    console.error('Error al buscar cliente:', err);
                    mostrarMensaje('Error al conectar con el servidor.', false);
                });
        });
    }

    // Botón Limpiar Búsqueda / Volver a estado inicial
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', () => {
            if (inputBuscar) inputBuscar.value = '';
            if (seccionExistente) seccionExistente.style.display = 'none';
            if (seccionNuevo) seccionNuevo.style.display = 'block';
            btnLimpiar.style.display = 'none';
            if (formClienteNuevo) formClienteNuevo.reset();
            if (formEquipoAdicional) formEquipoAdicional.reset();
        });
    }

    // 2. REGISTRAR CLIENTE NUEVO + PRIMER EQUIPO (Case 1)
    if (formClienteNuevo) {
        formClienteNuevo.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(formClienteNuevo);
            formData.append('cargar_archivo', '1');

            fetch('index.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarMensaje(data.message, true);
                        formClienteNuevo.reset();
                    } else {
                        mostrarMensaje('Error: ' + data.message, false);
                    }
                })
                .catch(err => {
                    console.error('Error al guardar cliente:', err);
                    mostrarMensaje('Error de conexión o fallo en el servidor.', false);
                });
        });
    }

    // 3. REGISTRAR EQUIPO ADICIONAL A CLIENTE EXISTENTE (Case 9)
    if (formEquipoAdicional) {
        formEquipoAdicional.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(formEquipoAdicional);
            formData.append('cargar_archivo', '9');

            fetch('index.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarMensaje(data.message, true);
                        formEquipoAdicional.reset();
                    } else {
                        mostrarMensaje('Error: ' + data.message, false);
                    }
                })
                .catch(err => {
                    console.error('Error al guardar equipo adicional:', err);
                    mostrarMensaje('Error de conexión o fallo en el servidor.', false);
                });
        });
    }
});