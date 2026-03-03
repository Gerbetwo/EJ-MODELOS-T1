// assets/scripts/scripts.js
document.addEventListener('DOMContentLoaded', function () {
    // attach click handlers to edit buttons (works for dynamically generated rows)
    document.body.addEventListener('click', function (e) {
        if (e.target.closest('.btn-edit')) {
            const btn = e.target.closest('.btn-edit');
            const id = btn.dataset.id;
            openEditModal(id);
        }
    });
});

function openEditModal(id) {
    fetch(`index.php?module=clientes&action=get&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data || Object.keys(data).length === 0) {
                alert('No se encontró el registro');
                return;
            }
            // rellenar campos
            const container = document.getElementById('edit_fields');
            container.innerHTML = '';
            for (const [key, val] of Object.entries(data)) {
                if (key === 'id') {
                    document.getElementById('edit_id').value = val;
                    continue;
                }
                const div = document.createElement('div');
                div.className = 'col-md-6 mb-3';
                const label = document.createElement('label');
                label.className = 'form-label';
                label.textContent = key.charAt(0).toUpperCase() + key.slice(1);
                const input = document.createElement('input');
                input.className = 'form-control';
                input.name = key;
                input.value = val ?? '';
                div.appendChild(label);
                div.appendChild(input);
                container.appendChild(div);
            }
            // show modal (Bootstrap handles via data-bs-toggle but ensure modal is shown)
            const modalEl = document.getElementById('modalEditar');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        })
        .catch(err => {
            console.error(err);
            alert('Error al obtener datos del servidor');
        });
}