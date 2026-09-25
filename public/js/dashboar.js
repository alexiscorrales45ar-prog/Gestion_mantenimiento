document.addEventListener('DOMContentLoaded', () => {
    cargarDatosDashboard();

    // Evento para cerrar sesión
    document.getElementById('btnLogout').addEventListener('click', cerrarSesion);
});

/**
 * Consulta los datos del Dashboard al backend mediante Fetch
 */
async function cargarDatosDashboard() {
    try {
        // Ajusta el 'cargar_archivo' o la ruta según el caso de tu enrutador index.php para el Dashboard
        const response = await fetch('../index.php?cargar_archivo=8', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const resultado = await response.json();

        if (resultado.success) {
            renderizarDashboard(resultado.data);
        } else {
            console.error('Error del servidor:', resultado.message);
            alert('Error al cargar la información: ' + resultado.message);
        }
    } catch (error) {
        console.error('Error de conexión:', error);
    }
}

/**
 * Pinta la información en las tarjetas y en la tabla HTML
 */
function renderizarDashboard(data) {
    // 1. Mostrar nombre de usuario en la barra superior si existe en sesión
    if (data.usuario_nombre) {
        document.getElementById('nombreUsuario').textContent = data.usuario_nombre;
    }

    // 2. Actualizar Tarjetas (Kpis)
    document.getElementById('totalSolicitudes').textContent = data.total_solicitudes ?? 0;
    document.getElementById('solicitudesPendientes').textContent = data.pendientes ?? 0;
    document.getElementById('solicitudesCompletadas').textContent = data.completadas ?? 0;

    // 3. Llenar la Tabla de Solicitudes
    const tbody = document.getElementById('tablaSolicitudes');
    tbody.innerHTML = '';

    const solicitudes = data.ultimas_solicitudes || data.solicitudes || [];

    if (solicitudes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No hay solicitudes registradas.</td></tr>';
        return;
    }

    solicitudes.forEach(item => {
        const tr = document.createElement('tr');
        
        // Lee 'descripcion_problema' gracias al alias SQL que configuramos en el Repository
        const descripcion = item.descripcion_problema || item.descripcion_falla || 'Sin descripción';
        
        tr.innerHTML = `
            <td><strong>${item.codigo_seguimiento || item.id}</strong></td>
            <td>${item.cliente_nombre || 'N/A'}</td>
            <td>${item.equipo_nombre || 'N/A'}</td>
            <td>${descripcion}</td>
            <td><span class="badge prioridad-${(item.prioridad || '').toLowerCase()}">${item.prioridad || 'Media'}</span></td>
            <td><span class="badge estado-${(item.estado || '').toLowerCase()}">${item.estado || 'Pendiente'}</span></td>
        `;
        tbody.appendChild(tr);
    });
}

/**
 * Cierra la sesión activa y redirige al Login
 */
async function cerrarSesion() {
    try {
        const response = await fetch('../index.php?cargar_archivo=7&action=logout', {
            method: 'POST'
        });
        const res = await response.json();
        
        if (res.success) {
            window.location.href = 'login.html';
        }
    } catch (error) {
        window.location.href = 'login.html';
    }
}