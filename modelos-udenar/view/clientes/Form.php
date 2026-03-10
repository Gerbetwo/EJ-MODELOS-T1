<?php
// view/clientes/Form.php
$rules = TableRegistry::getRules($tableName);
$isEdit = isset($rowData);
$actionUrl = BASE_URL . $tableName . ($isEdit ? "/update" : "/create");
$formId = $isEdit ? 'form-update-dinamico' : 'form-create-dinamico';
?>

<form id="<?= $formId ?>" action="<?= $actionUrl ?>" method="POST" class="p-2">
    <?= UI::ExceptionBox() ?>

    <div class="row mt-4">
        <?php if($isEdit): ?> 
            <input type="hidden" name="id" value="<?= $rowData['id'] ?>"> 
        <?php endif; ?>

        <?php foreach ($columnsMeta as $col): 
            $rawName = $col['name'];
            if (strtolower($rawName) === 'id') continue;
            
            $ruleKey = strtolower($rawName);
            $rule = $rules[$ruleKey] ?? [];
            $val = $isEdit ? htmlspecialchars($rowData[$rawName]) : '';
            
            // Lógica de tipos para activar calendario/teclado numérico
            $type = $rule['type'] ?? 'text';
            $htmlType = ($type === 'date') ? 'date' : (($type === 'number') ? 'number' : (($type === 'email') ? 'email' : 'text'));
            
            // Formatear fecha para el input date nativo
            if($type === 'date' && !empty($val)) $val = date('Y-m-d', strtotime($val));
        ?>
            <div class="col-md-6">
                <div class="form-floating-custom">
                    <?php if ($type === 'relation'): ?>
                        <select name="<?= $rawName ?>" id="field-<?= $rawName ?>" class="form-control" required>
                            <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>></option>
                            <?php 
                            $db = (new Database())->getConnection();
                            $realTarget = TableRegistry::getRealTableName($rule['references']);
                            $displayCol = $rule['display'];
                            $res = $db->query("SELECT id, $displayCol FROM $realTarget ORDER BY $displayCol ASC");
                            while($opt = $res->fetch_assoc()): ?>
                                <option value="<?= $opt['id'] ?>" <?= ($val == $opt['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($opt[$displayCol]) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    <?php else: ?>
                        <input type="<?= $htmlType ?>" name="<?= $rawName ?>" id="field-<?= $rawName ?>" 
                               value="<?= $val ?>" class="form-control" placeholder=" " required>
                    <?php endif; ?>
                    
                    <label for="field-<?= $rawName ?>"><?= ucfirst($rawName) ?></label>
                    <small class="text-danger error-msg" id="error-<?= $rawName ?>"></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="modal-footer border-0 px-0 pb-0 mt-2">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
        <?= UI::Button($isEdit ? "Actualizar" : "Guardar", "submit", "btn-brand", "fas fa-save") ?>
    </div>
</form>