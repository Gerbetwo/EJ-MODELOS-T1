<?php
// view/clientes/Form.php

$rules = TableRegistry::getRules($tableName);
$isEdit = isset($rowData);
$actionUrl = BASE_URL . $tableName . ($isEdit ? '/update' : '/create');
// Definimos IDs distintos para posible uso futuro, pero la clase 'form-ajax' es la que manda
$formId = $isEdit ? 'form-update-dinamico' : 'form-create-dinamico';
?>

<form id="<?= $formId ?>" class="form-ajax p-2" action="<?= $actionUrl ?>" method="POST">
    <?= UI::ExceptionBox() ?>

    <div class="row mt-4">
        <?php if ($isEdit): ?> 
            <input type="hidden" name="id" value="<?= $rowData['id'] ?>"> 
        <?php endif; ?>

        <?php foreach ($columnsMeta as $col):

            $rawName = $col['name'];
            if (strtolower($rawName) === 'id') {
                continue;
            }

            $ruleKey = strtolower($rawName);
            $rule = $rules[$ruleKey] ?? [];
            $val = $isEdit ? htmlspecialchars($rowData[$rawName]) : '';

            // Lógica para tipos de input (Arregla el calendario)
            $type = $rule['type'] ?? 'text';
            $htmlType = 'text';

            if ($type === 'email') {
                $htmlType = 'email';
            } elseif ($type === 'number') {
                $htmlType = 'number';
            } elseif ($type === 'date') {
                $htmlType = 'date';
                if (!empty($val)) {
                    $val = date('Y-m-d', strtotime($val));
                } // Formato para el input date
            }
            ?>
            <div class="col-md-6">
                <div class="form-floating-custom">
                    
                    <?php if ($type === 'relation'): ?>
                        <select name="<?= $rawName ?>" class="form-control" required>
                            <option value="" disabled <?= !$isEdit
                                ? 'selected'
                                : '' ?>>Seleccione...</option>
                            <?php
                            // LÓGICA LIMPIA: Pedimos los datos al controlador mediante la función que agregamos
                            $displayCol = $rule['display'];
                            $options = $controller->getExternalData(
                                $rule['references'],
                                $displayCol,
                            );

                            foreach ($options as $opt): ?>
                                <option value="<?= $opt['id'] ?>" <?= $val == $opt['id']
    ? 'selected'
    : '' ?>>
                                    <?= htmlspecialchars($opt[$displayCol]) ?>
                                </option>
                            <?php endforeach;
                            ?>
                        </select>
                    <?php else: ?>
                        <input type="<?= $htmlType ?>" name="<?= $rawName ?>" value="<?= $val ?>" class="form-control" placeholder=" " required>
                    <?php endif; ?>
                    
                    <label><?= ucfirst($rawName) ?></label>
                    <small class="text-danger error-msg" id="error-<?= $rawName ?>"></small>
                </div>
            </div>
        <?php
        endforeach; ?>
    </div>

    <div class="modal-footer border-0 px-0 pb-0 mt-2">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
        <?= UI::Button(
            $isEdit ? 'Actualizar Registro' : 'Crear Registro',
            'submit',
            'btn-brand',
            'fas fa-save',
        ) ?>
    </div>
</form>