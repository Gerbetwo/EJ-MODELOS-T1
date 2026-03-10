<?php
// view/clientes/Form.php
$rules = TableRegistry::getRules($tableName);
?>
<form action="<?= $actionUrl ?>" method="POST" class="p-3" id="dynamicForm">
    <div class="row">
        <?php foreach ($columnsMeta as $col): 
            $name = $col['name'];
            if ($name === 'id') continue;
            
            $rule = $rules[$name] ?? [];
            $val = isset($rowData) ? htmlspecialchars($rowData[$name]) : '';
            
            // Renderizado condicional por tipo de regla
            if (($rule['type'] ?? '') === 'relation'): ?>
                <div class="col-md-6 form-group">
                    <label class="text-accent"><?= ucfirst($name) ?></label>
                    <select name="<?= $name ?>" class="form-control form-control-custom" required>
                        <option value="">Seleccione...</option>
                        <?php 
                        $ref = $rule['references'];
                        $db = (new Database())->getConnection();
                        $realRef = TableRegistry::getRealTableName($ref);
                        $res = $db->query("SELECT id, nombre FROM $realRef");
                        while($opt = $res->fetch_assoc()): ?>
                            <option value="<?= $opt['id'] ?>" <?= $val == $opt['id'] ? 'selected' : '' ?>>
                                <?= $opt['nombre'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            <?php else: ?>
                <div class="col-md-6 form-group">
                    <label class="text-accent"><?= ucfirst($name) ?></label>
                    <input 
                        type="<?= $rule['type'] ?? 'text' ?>" 
                        name="<?= $name ?>" 
                        value="<?= $val ?>"
                        pattern="<?= $rule['pattern'] ?? '.*' ?>"
                        title="<?= $rule['title'] ?? '' ?>"
                        min="<?= $rule['min'] ?? '' ?>"
                        class="form-control form-control-custom"
                        required
                    >
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    </form>