/**
 * scripts.js - Motor de Interfaz Udenar v3.0
 * Gestión completa de Búsqueda, Modales y Persistencia AJAX
 */

document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. BUSCADOR DINÁMICO ---
    // Filtra las filas de la tabla en tiempo real sin recargar
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const val = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(val) ? '' : 'none';
            });
        });
    }

    // --- 2. GESTIÓN DE MODALES (Delegación de Clics) ---
    // Detecta clics en botones de "Nuevo" y "Editar", incluso si se cargan dinámicamente
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-js, .btn-new-js');
        if (btn) {
            e.preventDefault();
            const table = btn.dataset.table;
            const id = btn.dataset.id || null;
            abrirModalConFormulario(table, id);
        }
    });

    // --- 3. INTERCEPCIÓN DE ENVÍOS (Lógica AJAX Separada) ---
    // Evita que el navegador redireccione al JSON y maneja la respuesta en el modal
    document.body.addEventListener('submit', function (e) {
        const form = e.target;

        // Caso: Creación de Registro
        if (form.id === 'form-create-dinamico') {
            e.preventDefault();
            procesarSolicitud(form, 'create');
        }
        
        // Caso: Actualización de Registro (PUT/PATCH Logic)
        if (form.id === 'form-update-dinamico') {
            e.preventDefault();
            procesarSolicitud(form, 'update');
        }
    });
});

/**
 * FUNCIÓN: Carga el formulario en el modal mediante Fetch
 */
function abrirModalConFormulario(table, id) {
    const modal = $('#modalGenérico');
    const modalBody = $('#modalBody');
    const url = id ? `${CONFIG.baseUrl}${table}/get/${id}` : `${CONFIG.baseUrl}${table}/get`;

    // Efecto de carga inicial
    modalBody.html(`
        <div class="text-center p-5">
            <i class="fas fa-circle-notch fa-spin fa-3x text-brand"></i>
            <p class="mt-3 text-muted">Preparando entorno de datos...</p>
        </div>
    `);
    modal.modal('show');

    fetch(url)
        .then(response => response.text())
        .then(html => {
            modalBody.html(html);
        })
        .catch(err => {
            console.error("Error al cargar modal:", err);
            modalBody.html('<div class="alert alert-danger">Error crítico al conectar con el servidor.</div>');
        });
}

/**
 * FUNCIÓN MAESTRA: Procesa el envío, valida JSON y muestra Feedback Visual
 */
async function procesarSolicitud(form, modo) {
    const btn = form.querySelector('button[type="submit"]');
    const originalBtnHTML = btn.innerHTML;
    const exceptionBox = document.getElementById('exception-container');
    const exceptionList = document.getElementById('exception-list');

    // 1. Bloqueo de UI y Spinner
    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-sync fa-spin mr-2"></i> ${modo === 'create' ? 'Guardando...' : 'Actualizando...'}`;
    if (exceptionBox) exceptionBox.classList.add('d-none');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        // Leemos como texto primero para limpiar posibles warnings de PHP antes del JSON
        const rawResponse = await response.text();
        const jsonStartIndex = rawResponse.indexOf('{');
        
        if (jsonStartIndex === -1) throw new Error("La respuesta del servidor no es un JSON válido.");
        
        const data = JSON.parse(rawResponse.substring(jsonStartIndex));

        if (response.status === 422) {
            // --- ESTADO: ERROR DE VALIDACIÓN DTO ---
            exceptionList.innerHTML = Object.values(data.errors).map(err => `<li>${err}</li>`).join('');
            exceptionBox.classList.remove('d-none');
            
            // Animación de sacudida visual
            exceptionBox.classList.add('animate__animated', 'animate__shakeX');
            setTimeout(() => exceptionBox.classList.remove('animate__shakeX'), 1000);

            // Restaurar botón
            btn.disabled = false;
            btn.innerHTML = originalBtnHTML;
            document.querySelector('.modal-body').scrollTop = 0;

        } else if (response.ok && data.success) {
            // --- ESTADO: ÉXITO TOTAL ---
            const icon = modo === 'create' ? 'fa-check-circle' : 'fa-check-double';
            const accentClass = modo === 'create' ? 'text-success' : 'text-brand';

            form.innerHTML = `
                <div class="text-center py-5 animate__animated animate__zoomIn">
                    <div class="mb-4">
                        <i class="fas ${icon} ${accentClass}" style="font-size: 5.5rem; filter: drop-shadow(0 0 15px rgba(168, 85, 247, 0.3));"></i>
                    </div>
                    <h3 class="text-white font-weight-bold">${data.title || '¡Hecho!'}</h3>
                    <p class="text-muted px-4">${data.message}</p>
                    <div class="mt-4 px-5">
                        <div class="progress bg-glass-dark-custom" style="height: 4px; border-radius: 10px;">
                            <div class="progress-bar bg-brand progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                        </div>
                        <small class="text-brand mt-2 d-block">Sincronizando tabla...</small>
                    </div>
                </div>`;

            // Recarga la página tras la animación para reflejar cambios
            setTimeout(() => {
                $('#modalGenérico').modal('hide');
                window.location.reload();
            }, 2000);

        } else {
            throw new Error(data.message || "Error desconocido en el servidor.");
        }

    } catch (error) {
        console.error("Fallo en peticion:", error);
        alert("Error crítico de comunicación. Revisa la consola para más detalles.");
        btn.disabled = false;
        btn.innerHTML = originalBtnHTML;
    }
}