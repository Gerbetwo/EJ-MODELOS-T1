<div class="row">
    <div class="col-12">
        <div class="card bg-glass-custom border-glass shadow-primary">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title text-accent-custom mb-0">
                    <i class="fas fa-list-ul mr-2"></i>Gestión de Clientes
                </h3>
                <div class="card-tools ml-auto">
                    <button class="btn btn-primary-custom shadow-sm" data-toggle="modal" data-target="#modalCrear">
                        <i class="fas fa-user-plus mr-1"></i>Nuevo Registro
                    </button>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-white-custom mb-0">
                    <thead class="bg-glass-dark-custom">
                        <tr>
                            <?php foreach ($columnsMeta as $col): ?>
                                <th class="text-accent-custom border-bottom-0"><?= strtoupper($col['Field']) ?></th>
                            <?php endforeach; ?>
                            <th class="text-center border-bottom-0">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $c): ?>
                            <tr>
                                <?php foreach ($columnsMeta as $col): ?>
                                    <td><?= htmlspecialchars($c[$col['Field']]) ?></td>
                                <?php endforeach; ?>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-custom mr-1 btn-editar" data-id="<?= $c['id'] ?>">
                                        <i class="fas fa-pen-nib"></i>
                                    </button>
                                    <a href="index.php?module=clientes&action=delete&id=<?= $c['id'] ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('¿Confirmar eliminación?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>