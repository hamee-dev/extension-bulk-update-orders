<?php
/**
 * 項目を追加した際に最初にできる項目
 */
?>
<tr class="form-group add">
    <?= $this->fast_render('updatesetting/templates/_target', ['is_first_add' => isset($is_first_add) ? $is_first_add : false]) ?>
    <td class="setting-list-data">
        <p class="text-danger"><?= __p('no_select_column') ?></p>
    </td>
    <td class="setting-list-button"></td>
</tr>