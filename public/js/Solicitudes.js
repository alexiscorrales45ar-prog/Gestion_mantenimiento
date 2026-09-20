document.addEventListener('DOMContentLoaded', () => {
    // Referencias del DOM
    const formBuscarCliente = document.getElementById('formBuscarClienteSolicitud');
    const inputBuscarDoc = document.getElementById('buscarDocCliente');

    const formNuevaSolicitud = document.getElementById('formNuevaSolicitud');
    const inputClienteIdHidden = document.getElementById('solicitud_cliente_id');
    const infoClienteDiv = document.getElementById('infoClienteSeleccionado');
    const selectEquipo = document.getElementById('selectEquipo');

    const formConsultarCodigo = document.getElementById('formConsultarCodigo');
    const inputCodigo = document.getElementById('buscarCodigo');
    const divResultadoConsulta = document.getElementById('resultadoConsulta');

    const mensajeSistema = document.getElementById('mensajeSistema');

    // Función auxiliar para mostrar alertas de feedback
    function mostrarMensaje(texto, esExito) {
        mensajeSistema.style.display = 'block';
        mensajeSistema.style.backgroundColor = esExito ? '#d4edda' : '#f8d7da';
        mensajeSistema.style.color = esExito ? '#155724' : '#721c24';
        mensajeSistema.style.border = esExito ? '1px solid #c3e6cb' : '1px solid #f5c6cb';
        mensajeSistema.innerText = texto;
        setTimeout(() => { mensajeSistema.style.display = 'none'; }, 5000);
    }

    // 1. PASO A: BUSCAR CLIENTE Y CARGAR SUS EQUIPOS
    if (formBuscarCliente) {
        formBuscarCliente.addEventListener('submit', (e) => {
            e.preventDefault();
            const documento = inputBuscarDoc.value.trim();
            if (!documento) return;

            // Consultar datos del cliente
            fetch(`index.php?cargar_archivo=10&documento=${encodeURIComponent(documento)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        const cliente = data.data;
                        inputClienteIdHidden.value = cliente.id;
                        infoClienteDiv.innerHTML = `👤 <strong>Cliente:</strong> ${cliente.nombre} | <strong>Cédula:</strong> ${cliente.cedula || 'N/A'} | <strong>Teléfono:</strong> ${cliente.telefono}`;

                        // Cargar equipos pertenecientes a este cliente
                        cargarEquiposCliente(cliente.id);
                    } else {
                        formNuevaSolicitud.style.display = 'none';
                        mostrarMensaje('Cliente no encontrado. Por favor regístrelo primero en el módulo de Recepción.', false);
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    mostrarMensaje('Error al conectar con el servidor.', false);
                });
        });
    }

    // Función para llenar la lista desplegable de equipos por cliente
    function cargarEquiposCliente(clienteId) {
        fetch(`index.php?cargar_archivo=11&cliente_id=${clienteId}`)
            .then(res => res.json())
            .then(data => {
                selectEquipo.innerHTML = '';
                if (data.success && data.data.length > 0) {
                    data.data.forEach(e => {
                        const opt = document.createElement('option');
                        opt.value = e.id;
                        opt.textContent = `${e.nombre} - Marca: ${e.marca || 'S/N'} | Mod: ${e.modelo || 'S/N'} | Serie: ${e.serie || 'S/N'}`;
                        selectEquipo.appendChild(opt);
                    });
                    formNuevaSolicitud.style.display = 'block';
                    mostrarMensaje('Cliente y equipos cargados correctamente.', true);
                } else {
                    formNuevaSolicitud.style.display = 'none';
                    mostrarMensaje('El cliente no tiene ningún equipo registrado. Por favor vincule un equipo en el módulo de Recepción.', false);
                }
            })
            .catch(err => console.error('Error al cargar equipos:', err));
    }

    // 2. PASO B: GUARDAR SOLICITUD Y OBTENER CÓDIGO ÚNICO
    if (formNuevaSolicitud) {
        formNuevaSolicitud.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(formNuevaSolicitud);
            formData.append('cargar_archivo', '2');

            fetch('index.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarMensaje(`${data.message} CÓDIGO DE SEGUIMIENTO: ${data.codigo}`, true);
                        formNuevaSolicitud.reset();
                        formNuevaSolicitud.style.display = 'none';
                        formBuscarCliente.reset();
                    } else {
                        mostrarMensaje('Error: ' + data.message, false);
                    }
                })
                .catch(err => console.error('Error al guardar solicitud:', err));
        });
    }

    // 3. CONSULTAR SOLICITUD POR CÓDIGO ÚNICO
    if (formConsultarCodigo) {
        formConsultarCodigo.addEventListener('submit', (e) => {
            e.preventDefault();
            const codigo = inputCodigo.value.trim();
            if (!codigo) return;

            fetch(`index.php?cargar_archivo=12&codigo=${encodeURIComponent(codigo)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        const s = data.data;
                        divResultadoConsulta.style.display = 'block';
                        divResultadoConsulta.innerHTML = `
                            <h3 style="margin-top:0; color:#2c3e50;">Ticket N°: <span style="color:#0275d8;">${s.codigo_seguimiento}</span></h3>
                            <p><strong>Cliente:</strong> ${s.cliente_nombre} (Tel: ${s.telefono})</p>
                            <p><strong>Equipo:</strong> ${s.equipo_nombre} ${s.marca ? '- ' + s.marca : ''} ${s.modelo ? '(' + s.modelo + ')' : ''}</p>
                            <p><strong>Problema Reportado:</strong> ${s.descripcion_falla}</p>
                            <p><strong>Prioridad:</strong> <span style="font-weight:bold;">${s.prioridad}</span></p>
                            <p><strong>Estado Actual:</strong> <span style="background-color:#fff3cd; color:#856404; padding:4px 8px; border-radius:4px; font-weight:bold;">${s.estado}</span></p>
                            <p><small style="color:#777;">Fecha de Registro: ${s.fecha_registro}</small></p>
                        `;
                    } else {
                        divResultadoConsulta.style.display = 'none';
                        mostrarMensaje(data.message, false);
                    }
                })
                .catch(err => console.error('Error al consultar código:', err));
        });
    }
});