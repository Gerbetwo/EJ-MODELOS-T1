<?php
// includes/footer.php
?>
        </div> <!-- Cierra table-wrapper -->
           <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión Udenar | Acceso: Gebert</p>
    </div>
    </div> <!-- Cierra container -->
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
</body>
    <!-- Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <!-- Tu JS personalizado -->
    <script src="assets/scripts/scripts.js"></script>
</html>