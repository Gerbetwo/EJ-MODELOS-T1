document.addEventListener('DOMContentLoaded', function () {

  // 1. Delegación de eventos para botones dinámicos
  document.body.addEventListener('click', function (e) {
    // Lógica para EDITAR
    const btnEdit = e.target.closest('.btn-edit-js');
    if (btnEdit) {
      const id = btnEdit.dataset.id;
      const table = btnEdit.dataset.table;
      loadModalForm(table, id);
    }

    // Lógica para NUEVO
    const btnNew = e.target.closest('.btn-new-js');
    if (btnNew) {
      const table = btnNew.dataset.table;
      loadModalForm(table, null);
    }
  });

  // 2. Buscador en tiempo real
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
});

function loadModalForm(table, id) {
  const modal = $('#modalGenérico');
  const modalBody = $('#modalBody');
  const modalTitle = $('#modalTitle');

  modalTitle.text(id ? 'Actualizar Registro' : 'Crear Nuevo Registro');
  modalBody.html('<div class="text-center p-5"><i class="fas fa-circle-notch fa-spin fa-2x text-brand"></i></div>');

  // Mostramos el modal de inmediato con el loader
  modal.modal('show');

  // Construimos la URL profesional: /modelos-udenar/tabla/get/id
  // Si id es null, la URL queda: /modelos-udenar/tabla/get
  // El Router ahora interpreta esto correctamente como 'Crear'
  const url = id ? `${CONFIG.baseUrl}${table}/get/${id}` : `${CONFIG.baseUrl}${table}/get`;

  fetch(url)
    .then(response => {
      if (!response.ok) throw new Error('Error ' + response.status);
      return response.text();
    })
    .then(html => {
      modalBody.html(html);
    })
    .catch(err => {
      console.error('Error AJAX:', err);
      modalBody.html('<div class="alert alert-danger">No se pudo cargar el formulario.</div>');
    });
}