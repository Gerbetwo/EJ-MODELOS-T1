        function editarRegistro(id) {
            fetch('editar.php?id=' + encodeURIComponent(id))
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modal-body').innerHTML = html;
                    document.getElementById('modalEditar').style.display = 'block';
                    const form = document.querySelector('#modal-body form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(form);
                            fetch(form.action, { method: form.method, body: formData })
                                .then(resp => resp.text())
                                .then(data => {
                                    alert('Registro actualizado correctamente');
                                    cerrarModal();
                                    document.getElementById('buscar').dispatchEvent(new Event('input'));
                                })
                                .catch(err => console.error(err));
                        });
                    }
                })
                .catch(err => console.error(err));
        }

        function eliminarRegistro(id) {
            if (confirm('¿Estás seguro de eliminar este cliente?')) {
                window.location.href = 'eliminar.php?id=' + encodeURIComponent(id);
            }
        }

        function cerrarModal() {
            document.getElementById('modalEditar').style.display = 'none';
        }

        function nuevoRegistro() {
            fetch('crear.php')
                .then(res => res.text())
                .then(html => {
                    document.getElementById('modal-body-crear').innerHTML = html;
                    document.getElementById('modalCrear').style.display = 'block';
                    const form = document.querySelector('#modal-body-crear form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(form);
                            fetch(form.action, { method: form.method, body: formData })
                                .then(resp => resp.text())
                                .then(data => {
                                    alert('Cliente creado correctamente');
                                    cerrarModalCrear();
                                    document.getElementById('buscar').dispatchEvent(new Event('input'));
                                })
                                .catch(err => console.error(err));
                        });
                    }
                })
                .catch(err => console.error(err));
        }

        function cerrarModalCrear() {
            document.getElementById('modalCrear').style.display = 'none';
        }

        // Búsqueda dinámica
        const inputBuscar = document.getElementById('buscar');
        let timeout = null;
        if (inputBuscar) {
            inputBuscar.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const query = inputBuscar.value.trim();
                    fetch('tabla_clientes.php?buscar=' + encodeURIComponent(query))
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('tablaResultados').innerHTML = html;
                        })
                        .catch(err => console.error(err));
                }, 300);
            });
        }

        // Animación ripple (opcional)
        document.querySelectorAll('.btn-edit, .btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                let ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                let x = e.clientX - e.target.getBoundingClientRect().left;
                let y = e.clientY - e.target.getBoundingClientRect().top;
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;
                setTimeout(() => ripple.remove(), 600);
            });
        });