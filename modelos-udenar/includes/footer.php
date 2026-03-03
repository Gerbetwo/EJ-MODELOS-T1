<?php
// includes/footer.php
?>
        </div> <!-- Cierra table-wrapper -->
           <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión Udenar | Acceso: Gebert</p>
    </div>
    </div> <!-- Cierra container -->

    <script src="assets/scripts/scripts.js"></script>

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