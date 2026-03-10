document.body.addEventListener('submit', function (e) {
  if (e.target.id === 'form-registro-dinamico') {
    e.preventDefault();
    const form = e.target;
    const btnSubmit = form.querySelector('button[type="submit"]');
    const originalBtnText = btnSubmit.innerHTML;

    // Visual: Deshabilitar botón y mostrar carga
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(async response => {
        const data = await response.json();

        if (response.ok) {
          // ÉXITO TOTAL
          form.innerHTML = `
                    <div class="text-center p-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3 animate__animated animate__bounceIn"></i>
                        <h4 class="text-white">${data.message}</h4>
                        <p class="text-muted">La tabla se actualizará en un momento...</p>
                    </div>`;
          setTimeout(() => window.location.reload(), 1500);
        } else {
          // ERROR O EXCEPCIÓN
          const exceptionBox = document.getElementById('exception-container');
          const exceptionList = document.getElementById('exception-list');

          exceptionList.innerHTML = '';
          const errors = data.errors || ['Error desconocido'];
          Object.values(errors).forEach(msg => {
            exceptionList.innerHTML += `<li>${msg}</li>`;
          });

          exceptionBox.classList.remove('d-none');
          btnSubmit.disabled = false;
          btnSubmit.innerHTML = originalBtnText;
          document.querySelector('.modal-body').scrollTop = 0;
        }
      })
      .catch(err => {
        alert("Error crítico de conexión.");
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalBtnText;
      });
  }
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