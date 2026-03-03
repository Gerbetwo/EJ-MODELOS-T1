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
    <script src="assets/scripts/scripts.js"></script>
</html>