document.addEventListener('DOMContentLoaded', function () {
    
    // 1. BUSQUEDA DINÁMICA
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        });
    }

    // 2. CARGA DE MODALES (CREAR / EDITAR)
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-js, .btn-new-js');
        if (btn) {
            const table = btn.dataset.table;
            const id = btn.dataset.id || null;
            loadModalForm(table, id);
        }
    });

    // 3. ENVÍO DE FORMULARIO AJAX
    document.body.addEventListener('submit', function (e) {
        if (e.target.id === 'form-registro-dinamico') {
            e.preventDefault();
            handleFormSubmit(e.target);
        }
    });
});

function loadModalForm(table, id) {
    const modal = $('#modalGenérico');
    const url = id ? `${CONFIG.baseUrl}${table}/get/${id}` : `${CONFIG.baseUrl}${table}/get`;

    $('#modalBody').html('<div class="text-center p-5"><i class="fas fa-sync fa-spin fa-2x text-brand"></i></div>');
    modal.modal('show');

    fetch(url)
        .then(r => r.text())
        .then(html => $('#modalBody').html(html))
        .catch(() => $('#modalBody').html('<div class="alert alert-danger">Error al cargar.</div>'));
}

async function handleFormSubmit(form) {
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    const exceptionBox = document.getElementById('exception-container');
    const exceptionList = document.getElementById('exception-list');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const text = await response.text();
        // Limpiamos posibles warnings de PHP antes de parsear JSON
        const jsonStart = text.indexOf('{');
        const data = JSON.parse(text.substring(jsonStart));

        if (response.status === 422) {
            exceptionList.innerHTML = Object.values(data.errors).map(m => `<li>${m}</li>`).join('');
            exceptionBox.classList.remove('d-none');
            btn.disabled = false;
            btn.innerHTML = originalText;
        } else {
            window.location.reload();
        }
    } catch (e) {
        console.error("Error crítico:", e);
        alert("Fallo en la respuesta del servidor.");
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}