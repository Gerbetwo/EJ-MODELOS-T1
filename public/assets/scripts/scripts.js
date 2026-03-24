/**
 * scripts.js - Motor de Interfaz Dinámica v6.0
 * Usa SweetAlert2 para notificaciones y confirmaciones.
 */

document.addEventListener('DOMContentLoaded', function () {
  // --- Configuración SweetAlert2 para tema oscuro ---
  const SwalThemed = Swal.mixin({
    background: '#12141d',
    color: '#f1f2f6',
    confirmButtonColor: '#7d5fff',
    cancelButtonColor: '#6c757d',
    customClass: {
      popup: 'border-glass shadow-glow',
    },
  });

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
    background: '#12141d',
    color: '#f1f2f6',
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    },
  });

  // --- 1. BUSCADOR DINÁMICO ---
  const searchInput = document.getElementById('tableSearch');
  if (searchInput) {
    searchInput.addEventListener('keyup', function () {
      const val = this.value.toLowerCase();
      document.querySelectorAll('#tableBody tr').forEach((row) => {
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
      modalBody.html(
        '<div class="text-center p-5"><i class="fas fa-sync fa-spin fa-3x text-brand"></i></div>'
      );
      $('#modalGenérico').modal('show');

      fetch(`${CONFIG.baseUrl}${table}/get${id ? '/' + id : ''}`)
        .then((r) => r.text())
        .then((html) => modalBody.html(html))
        .catch(() =>
          modalBody.html(
            '<div class="text-center p-5"><i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i><p class="text-muted">Error al cargar el formulario.</p></div>'
          )
        );
    }
  });

  // --- 3. ELIMINACIÓN CON SWEETALERT2 ---
  document.body.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-delete-js');
    if (!btn) return;

    e.preventDefault();
    const id = btn.dataset.id;
    const table = btn.dataset.table;

    SwalThemed.fire({
      title: '¿Eliminar registro?',
      html: '<p style="color: #a4b0be;">Esta acción <strong style="color: #ff4d4d;">no se puede deshacer</strong>. El registro será eliminado permanentemente.</p>',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Sí, eliminar',
      cancelButtonText: 'Cancelar',
      reverseButtons: true,
      focusCancel: true,
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `${CONFIG.baseUrl}${table}/delete/${id}`;
      }
    });
  });

  // --- 4. INTERCEPTOR MAESTRO DE FORMULARIOS (AJAX) ---
  document.body.addEventListener('submit', function (e) {
    if (e.target && e.target.classList.contains('form-ajax')) {
      e.preventDefault();
      const form = e.target;
      const isUpdate = form.id === 'form-update-dinamico';
      procesarFormulario(form, isUpdate, SwalThemed, Toast);
    }
  });
});

async function procesarFormulario(form, isUpdate, SwalThemed, Toast) {
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
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });

    const rawText = await response.text();
    const jsonStart = rawText.indexOf('{');
    if (jsonStart === -1) throw new Error('Respuesta no válida del servidor');

    const data = JSON.parse(rawText.substring(jsonStart));

    if (response.status === 422) {
      // ERROR DE VALIDACIÓN
      list.innerHTML = Object.values(data.errors)
        .map((m) => `<li>${m}</li>`)
        .join('');
      box.classList.remove('d-none');
      box.classList.add('animate__animated', 'animate__shakeX');
      setTimeout(() => box.classList.remove('animate__shakeX'), 1000);

      btn.disabled = false;
      btn.innerHTML = originalText;
      document.querySelector('.modal-body').scrollTop = 0;
    } else if (response.ok && data.success) {
      // ÉXITO — Animación en modal + Toast
      const icon = isUpdate ? 'fa-check-double text-brand' : 'fa-check-circle text-success';
      form.innerHTML = `
        <div class="text-center py-5 animate__animated animate__zoomIn">
            <i class="fas ${icon} fa-5x mb-3"></i>
            <h3 class="text-white font-weight-bold">${data.title || '¡Éxito!'}</h3>
            <p class="text-muted">${data.message}</p>
        </div>`;

      Toast.fire({
        icon: 'success',
        title: data.title || '¡Éxito!',
        text: data.message,
      });

      setTimeout(() => window.location.reload(), 1800);
    } else {
      throw new Error(data.message || 'Error desconocido');
    }
  } catch (e) {
    console.error('Error crítico:', e);
    SwalThemed.fire({
      icon: 'error',
      title: 'Error de comunicación',
      html: '<p style="color: #a4b0be;">No se pudo conectar con el servidor. Verifica tu conexión e intenta de nuevo.</p>',
      confirmButtonText: 'Entendido',
    });
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
}
