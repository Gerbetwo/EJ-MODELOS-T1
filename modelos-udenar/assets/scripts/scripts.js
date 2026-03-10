/**
 * scripts.js - Motor de Interfaz Dinámica v5.0
 * Soluciona la intercepción de creación y actualización.
 */

document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. BUSCADOR DINÁMICO ---
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const val = this.value.toLowerCase();
            document.querySelectorAll('#tableBody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    }

    // --- 2. APERTURA DE MODALES (DELEGACIÓN) ---
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-js, .btn-new-js');
        if (btn) {
            e.preventDefault();
            const table = btn.dataset.table;
            const id = btn.dataset.id || null;
            
            const modalBody = $('#modalBody');
            modalBody.html('<div class="text-center p-5"><i class="fas fa-sync fa-spin fa-3x text-brand"></i></div>');
            $('#modalGenérico').modal('show');

            fetch(`${CONFIG.baseUrl}${table}/get${id ? '/' + id : ''}`)
                .then(r => r.text())
                .then(html => modalBody.html(html))
                .catch(() => modalBody.html('<div class="alert alert-danger">Error al cargar.</div>'));
        }
    });

    // --- 3. INTERCEPTOR MAESTRO DE FORMULARIOS (AJAX) ---
    // Atrapa TODO formulario con la clase 'form-ajax'
    document.body.addEventListener('submit', function (e) {
        if (e.target && e.target.classList.contains('form-ajax')) {
            e.preventDefault(); // EVITA LA REDIRECCIÓN A LA PÁGINA BLANCA
            
            const form = e.target;
            const isUpdate = form.id === 'form-update-dinamico';
            procesarFormulario(form, isUpdate);
        }
    });
});

async function procesarFormulario(form, isUpdate) {
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    const box = document.getElementById('exception-container');
    const list = document.getElementById('exception-list');

    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> ${isUpdate ? 'Actualizando...' : 'Creando...'}`;
    if (box) box.classList.add('d-none');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const rawText = await response.text();
        const jsonStart = rawText.indexOf('{');
        if(jsonStart === -1) throw new Error("Respuesta no válida del servidor");
        
        const data = JSON.parse(rawText.substring(jsonStart));

        if (response.status === 422) {
            // ERROR DE VALIDACIÓN
            list.innerHTML = Object.values(data.errors).map(m => `<li>${m}</li>`).join('');
            box.classList.remove('d-none');
            box.classList.add('animate__animated', 'animate__shakeX');
            setTimeout(() => box.classList.remove('animate__shakeX'), 1000);
            
            btn.disabled = false;
            btn.innerHTML = originalText;
            document.querySelector('.modal-body').scrollTop = 0;
            
        } else if (response.ok && data.success) {
            // ÉXITO TOTAL: Mostrar animación en el modal
            const icon = isUpdate ? 'fa-check-double text-brand' : 'fa-check-circle text-success';
            form.innerHTML = `
                <div class="text-center py-5 animate__animated animate__zoomIn">
                    <i class="fas ${icon} fa-5x mb-3"></i>
                    <h3 class="text-white font-weight-bold">${data.title || '¡Éxito!'}</h3>
                    <p class="text-muted">${data.message}</p>
                </div>`;
            
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(data.message || "Error desconocido");
        }
    } catch (e) {
        console.error("Error crítico:", e);
        alert("Fallo de comunicación con el servidor.");
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}