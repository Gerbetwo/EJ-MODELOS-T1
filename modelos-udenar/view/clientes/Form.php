<?php
// view/clientes/Form.php

// 1. SOLUCIÓN AL ERROR 403: Definir la URL de acción
$actionUrl = BASE_URL . $tableName . ($isEdit ? "/update" : "/create");

$rules = TableRegistry::getRules($tableName);
$isEdit = isset($rowData);
?>

<form id="form-registro-dinamico" action="<?= $actionUrl ?>" method="POST" class="p-2">
    <?= UI::ExceptionBox() ?>

    <div class="row mt-4">
        <?php if($isEdit): ?> <input type="hidden" name="id" value="<?= $rowData['id'] ?>"> <?php endif; ?>

        <?php foreach ($columnsMeta as $col): 
            $rawName = $col['name']; // Nombre real de DB: "Nombre"
            if (strtolower($rawName) === 'id') continue;
            
            // 2. SOLUCIÓN AL FALLO DE VALIDACIÓN: Normalizar llave para el TableRegistry
            $ruleKey = strtolower($rawName);
            $rule = $rules[$ruleKey] ?? [];
            
            $val = $isEdit ? htmlspecialchars($rowData[$rawName]) : '';
            $placeholder = $rule['placeholder'] ?? 'Escriba aquí...';
        ?>
            <div class="col-md-6">
                <div class="form-floating-custom">
                    
                    <?php if (($rule['type'] ?? '') === 'relation'): ?>
                        <select name="<?= $rawName ?>" id="field-<?= $rawName ?>" class="form-control" required>
                            <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>></option>
                            <?php 
                            $db = (new Database())->getConnection();
                            $realTarget = TableRegistry::getRealTableName($rule['references']);
                            $displayCol = $rule['display'];
                            $res = $db->query("SELECT id, $displayCol FROM $realTarget");
                            while($opt = $res->fetch_assoc()): ?>
                                <option value="<?= $opt['id'] ?>" <?= ($val == $opt['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($opt[$displayCol]) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <label for="field-<?= $rawName ?>"><?= str_replace('_', ' ', $rawName) ?></label>

                    <?php else: ?>
                        <input 
                            type="<?= $rule['type'] ?? 'text' ?>" 
                            name="<?= $rawName ?>" 
                            id="field-<?= $rawName ?>"
                            value="<?= $val ?>"
                            class="form-control"
                            placeholder=" " 
                            pattern="<?= $rule['regex'] ?? '.*' ?>"
                            title="<?= $rule['error'] ?? '' ?>"
                            <?= ($col['null'] === 'NO') ? 'required' : '' ?>
                        >
                        <label for="field-<?= $rawName ?>"><?= str_replace('_', ' ', $rawName) ?></label>
                    <?php endif; ?>
                    
                    <small class="text-danger error-msg" id="error-<?= $rawName ?>"></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="modal-footer border-0 px-0 pb-0 mt-2">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
        <?= UI::Button($isEdit ? "Actualizar Registro" : "Crear Registro", "submit", "btn-brand", "fas fa-check-circle") ?>
    </div>
</form>