<?php
// includes/footer.php
?>
        </div> <!-- Cierra table-wrapper -->
           <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión Udenar | Acceso: Gebert</p>
    </div>
    </div> <!-- Cierra container -->

 

    <script>
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
    </script>

    <!-- Modales -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <div id="modalCrear" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalCrear()">&times;</span>
            <div id="modal-body-crear"></div>
        </div>
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: white;
            padding: 30px;
            width: 500px;
            max-width: 90%;
            margin: 50px auto;
            border-radius: 12px;
            position: relative;
        }
        .close {
            position: absolute;
            top: 10px; right: 15px;
            font-size: 24px;
            cursor: pointer;
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
        .btn-submit { background: #5f72e4; color: white; padding: 10px 20px; border: none; border-radius:6px; cursor: pointer; margin-right: 10px;}
        .btn-cancel { background: #ccc; color: #333; padding: 10px 20px; border:none; border-radius:6px; cursor:pointer;}
    </style>
</body>
</html>