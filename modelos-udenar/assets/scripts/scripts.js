document.addEventListener('DOMContentLoaded', function () {
    // 1. Buscador Dinámico
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(row => {
                // Filtra por todo el texto de la fila
                row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        });
    }

    // 2. Delegación de clics para abrir Modales
    document.body.addEventListener('click', function (e) {
        const btnEdit = e.target.closest('.btn-edit-js');
        const btnNew = e.target.closest('.btn-new-js');

        if (btnEdit) {
            loadModalForm(btnEdit.dataset.table, btnEdit.dataset.id);
        } else if (btnNew) {
            loadModalForm(btnNew.dataset.table, null);
        }
    });
});

function loadModalForm(table, id) {
    const modal = $('#modalGenérico');
    const url = id ? `${CONFIG.baseUrl}${table}/get/${id}` : `${CONFIG.baseUrl}${table}/get`;

    modal.modal('show');
    $('#modalBody').html('<div class="text-center p-5"><i class="fas fa-sync fa-spin fa-2x text-brand"></i></div>');

    fetch(url)
        .then(r => r.text())
        .then(html => {
            $('#modalBody').html(html);
        })
        .catch(err => {
            console.error(err);
            $('#modalBody').html('<div class="alert alert-danger">Error al cargar el formulario.</div>');
        });
}