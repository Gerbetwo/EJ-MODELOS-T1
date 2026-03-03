<div class="row">
    <div class="col-12">
        <div class="card card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i> Listado de Clientes</h3>
                <div class="card-tools">
                    <button
                        class="btn btn-success btn-sm"
                        data-toggle="modal"
                        data-target="#modalCrear"
                    >
                        <i class="fas fa-plus"></i> Nuevo Cliente
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <?php foreach($columnsMeta as $col): ?>
                            <th><?= strtoupper($col['Field']) ?></th>
                            <?php endforeach; ?>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clientes as $c): ?>
                        <tr>
                            <?php foreach($columnsMeta as $col): ?>
                            <td><?= $c[$col['Field']] ?></td>
                            <?php endforeach; ?>
                            <td class="text-center">
                                <button
                                    class="btn btn-info btn-xs btn-editar"
                                    data-id="<?= $c['id'] ?>"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a
                                    href="index.php?module=clientes&action=delete&id=<?= $c['id'] ?>"
                                    class="btn btn-danger btn-xs"
                                    onclick="return confirm('¿Eliminar registro?');"
                                >
                                    <i class="fas fa-trash"></i>
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
