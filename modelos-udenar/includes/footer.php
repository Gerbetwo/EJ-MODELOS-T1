<?php
// includes/footer.php
?>
        </div> <!-- Cierre de table-wrapper -->
        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión Udenar | Acceso: Gebert</p>
        </div>
    </div> <!-- Cierre de container -->

    <script>
        // Funciones de ejemplo para editar/eliminar (reemplazar con lógica real)
        // En includes/footer.php
        function editarRegistro(id) {
            window.location.href = 'editar.php?id=' + encodeURIComponent(id);
        }

        function eliminarRegistro(id) {
            if (confirm('¿Estás seguro de eliminar este cliente?')) {
                window.location.href = 'eliminar.php?id=' + encodeURIComponent(id);
            }
        }

        // Animación ripple para los botones
        document.querySelectorAll('.btn-edit, .btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                let ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                let x = e.clientX - e.target.getBoundingClientRect().left;
                let y = e.clientY - e.target.getBoundingClientRect().top;
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

    </script>
</body>
</html>