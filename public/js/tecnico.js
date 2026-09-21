document.addEventListener('DOMContentLoaded', () => {
    // Referencias al DOM
    const tablaSolicitudesBody = document.getElementById('tablaSolicitudesBody');
    const btnRefrescarTabla = document.getElementById('btnRefrescarTabla');

    const seccionDiagnostico = document.getElementById('seccionDiagnostico');
    const formActualizarOrden = document.getElementById('formActualizarOrden');
    const inputSolicitudId = document.getElementById('solicitud_id');
    const resumenSolicitudDiv = document.getElementById('resumenSolicitud');
    const selectEstado = document.getElementById('estado');
    const textareaDiagnostico = document.getElementById('diagnostico');
    const inputCosto = document.getElementById('costo');
    const btnCancelarDiagnostico = document.getElementById('btnCancelarDiagnostico');

    const mensajeSistema = document.getElementById('mensajeSistema');

    // Memoria local
    let solicitudesCargadas = [];

    function mostrarMensaje(texto, esExito) {
        if (!mensajeSistema) return;
        mensajeSistema.style.display = 'block';
        mensajeSistema.style.backgroundColor = esExito ? '#d4edda' : '#f8d7da';
        mensajeSistema.style.color = esExito ? '#155724' : '#721c24';
        mensajeSistema.style.border = esExito ? '1px solid #c3e6cb' : '1px solid #f5c6cb';
        mensajeSistema.innerText = texto;
        
        // Desplazar suavemente hacia el mensaje para que el usuario lo vea
        mensajeSistema.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        setTimeout(() => { mensajeSistema.style.display = 'none'; }, 5000);
    }

    // 1. CARGAR SOLICITUDES (Case 13)
    function cargarSolicitudes() {
        if (!tablaSolicitudesBody) return;
        
        fetch('index.php?cargar_archivo=13')
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    solicitudesCargadas = data.data;
                    renderizarTabla(solicitudesCargadas);
                } else {
                    tablaSolicitudesBody.innerHTML = `<tr><td colspan="6" style="padding:15px; text-align:center; color:#777;">No hay solicitudes registradas.</td></tr>`;
                }
            })
            .catch(err => {
                console.error('Error al cargar solicitudes:', err);
                mostrarMensaje('Error de conexión al obtener solicitudes.', false);
            });
    }

    // RENDERIZAR TABLA CON SEGURIDAD
    function renderizarTabla(solicitudes) {
        if (!tablaSolicitudesBody) return;

        if (solicitudes.length === 0) {
            tablaSolicitudesBody.innerHTML = `<tr><td colspan="6" style="padding:15px; text-align:center; color:#777;">No hay solicitudes para mostrar.</td></tr>`;
            return;
        }

        tablaSolicitudesBody.innerHTML = '';
        solicitudes.forEach(s => {
            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #eee';

            // Adaptar colores de badges
            let bgEstado = '#fff3cd';
            let colorEstado = '#856404';
            const estadoActual = s.estado || 'Pendiente';

            if (estadoActual === 'Finalizada' || estadoActual === 'Finalizado') { 
                bgEstado = '#d4edda'; colorEstado = '#155724'; 
            }
            if (estadoActual === 'Cancelada' || estadoActual === 'Cancelado') { 
                bgEstado = '#f8d7da'; colorEstado = '#721c24'; 
            }
            if (estadoActual === 'En Proceso') { 
                bgEstado = '#cce5ff'; colorEstado = '#004085'; 
            }

            const clienteNom = s.cliente_nombre || 'Cliente N/A';
            const clienteTel = s.cliente_telefono || s.telefono || 'N/A';
            const equipoNom = s.equipo_nombre || 'Equipo N/A';
            const equipoMarca = s.marca ? `(${s.marca} ${s.modelo || ''})` : '';

            tr.innerHTML = `
                <td style="padding:10px;"><strong>${s.codigo_seguimiento || '---'}</strong></td>
                <td style="padding:10px;">${clienteNom}<br><small style="color:#666;">Tel: ${clienteTel}</small></td>
                <td style="padding:10px;">${equipoNom} <small style="color:#555;">${equipoMarca}</small></td>
                <td style="padding:10px;"><span style="font-size:0.85rem; font-weight:bold;">${s.prioridad || 'Media'}</span></td>
                <td style="padding:10px;"><span style="background:${bgEstado}; color:${colorEstado}; padding:4px 8px; border-radius:4px; font-weight:bold; font-size:0.85rem;">${estadoActual}</span></td>
                <td style="padding:10px;">
                    <button class="btn-atender" data-id="${s.id}" style="padding:6px 12px; background-color:#f39c12; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">
                        🛠️ Atender
                    </button>
                </td>
            `;
            tablaSolicitudesBody.appendChild(tr);
        });

        // Eventos a los botones Atender
        document.querySelectorAll('.btn-atender').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = parseInt(e.currentTarget.getAttribute('data-id'));
                if (!isNaN(id)) {
                    prepararDiagnostico(id);
                }
            });
        });
    }

    // 2. PREPARAR FORMULARIO DE DIAGNÓSTICO
    function prepararDiagnostico(id) {
        const s = solicitudesCargadas.find(item => parseInt(item.id) === id);
        if (!s) return;

        inputSolicitudId.value = s.id;
        
        const clienteNom = s.cliente_nombre || 'Cliente N/A';
        const equipoNom = s.equipo_nombre || 'Equipo';

        resumenSolicitudDiv.innerHTML = `
            <strong>Ticket N°:</strong> ${s.codigo_seguimiento || '---'} | <strong>Cliente:</strong> ${clienteNom}<br>
            <strong>Equipo:</strong> ${equipoNom} (Serie: ${s.serie || 'N/A'})<br>
            <strong>Falla Reportada:</strong> <em>"${s.descripcion_falla || 'Sin detalle'}"</em>
        `;

        selectEstado.value = (s.estado === 'Pendiente') ? 'En Proceso' : (s.estado || 'En Proceso');
        textareaDiagnostico.value = s.diagnostico || '';
        
        // Asignación segura de costo numérico
        const valCosto = parseFloat(s.costo_estimado);
        inputCosto.value = (!isNaN(valCosto) && valCosto > 0) ? valCosto.toFixed(2) : '0.00';

        seccionDiagnostico.style.display = 'block';
        seccionDiagnostico.scrollIntoView({ behavior: 'smooth' });
    }

    // Cancelar diagnóstico
    if (btnCancelarDiagnostico) {
        btnCancelarDiagnostico.addEventListener('click', () => {
            seccionDiagnostico.style.display = 'none';
            if (formActualizarOrden) formActualizarOrden.reset();
        });
    }

    // 3. GUARDAR DIAGNÓSTICO Y ESTADO (Case 14)
    if (formActualizarOrden) {
        formActualizarOrden.addEventListener('submit', function(e) {
            e.preventDefault(); // Detener cualquier acción nativa

            // Validación JS directa de campos obligatorios
            const idVal = inputSolicitudId.value;
            const diagVal = textareaDiagnostico.value.trim();

            if (!idVal || !diagVal) {
                mostrarMensaje('Por favor seleccione una solicitud e ingrese un diagnóstico técnico.', false);
                return;
            }

            // Construir el FormData explícitamente para asegurar compatibilidad total con PHP
            const formData = new FormData();
            formData.append('cargar_archivo', '14');
            formData.append('solicitud_id', idVal);
            formData.append('estado', selectEstado.value);
            formData.append('diagnostico', diagVal);
            formData.append('costo', inputCosto.value || '0');

            // Enviar petición con la API Fetch
            fetch('index.php', { 
                method: 'POST', 
                body: formData 
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Respuesta HTTP no válida: ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, true);
                    seccionDiagnostico.style.display = 'none';
                    formActualizarOrden.reset();
                    cargarSolicitudes(); // Recargar la lista de la tabla
                } else {
                    mostrarMensaje('Error: ' + data.message, false);
                }
            })
            .catch(err => {
                console.error('Error al guardar diagnóstico:', err);
                mostrarMensaje('Error de conexión o respuesta inválida del servidor.', false);
            });
        });
    }

    // Evento Refrescar
    if (btnRefrescarTabla) {
        btnRefrescarTabla.addEventListener('click', cargarSolicitudes);
    }

    // Carga inicial
    cargarSolicitudes();
});