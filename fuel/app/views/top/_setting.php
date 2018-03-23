<tr class="setting-data">
    <td valign="top">
        <div class="setting-name">
            <div class="inline">
                <?= trim_length($setting->name, Domain_Value_Receiveordercolumn::SETTING_NAME_DISPLAY_MAX_LENGTH) ?>
                <a href="javascript:void(0);" class="update-name" data-id="<?= $setting->id ?>" data-name="<?= $setting->name ?>">
                    <span class="glyphicon glyphicon-pencil blue-icon"></span>
                </a>
            </div>
        </div>
        <div class="setting-date">
            <div><?= __c('created_at') ?>: <?= $setting->created_at ?></div>
            <div><?= __c('created_user') ?>: <?= $setting->created_user->pic_name ?></div>
            <div><?= __c('updated_at') ?>: <?= $setting->updated_at ?></div>
            <div><?= __c('last_updated_user') ?>: <?= $setting->last_updated_user->pic_name ?></div>
        </div>
    </td>
    <td valign="top">
        <div class="setting-detail">
            <?php
            /**
             * 一括更新の内容（最大Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_COUNT件表示）
             */
            $count = 1;
            foreach ($setting->bulk_update_columns as $bulk_update_column) {
                echo $this->fast_render('top/_setting_detail', ['bulk_update_column' => $bulk_update_column]);
                if ($count >= Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_COUNT) {
                    break;
                }
                $count++;
            } ?>
        </div>
        <div class="setting-bottom">
            <div class="setting-detail-message font_bold">
                <?php
                /**
                 * Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_COUNT件を超え更新項目がある場合は更新項目数を表示する
                 */
                ?>
                <?php if (count($setting->bulk_update_columns) > Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_COUNT) { ?>
                    <?= __p('other_column_count', ['number' => count($setting->bulk_update_columns) - Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_COUNT]) ?>
                <?php } ?>
            </div>
            <div class="setting-detail-button">
                <a href="/updatesetting/edit?bulk_update_setting_id=<?= $setting->id ?>" class="btn btn-ne add-params">
                    <span class="glyphicon glyphicon-edit icon"></span><?= __c('button.edit') ?>
                </a>
                <?php if ($execution_method === EXECUTION_METHOD_EXTENSION) { ?>
                    <a href="javascript:void(0)" class="btn btn-success execution" data-id="<?= $setting->id ?>" data-name="<?= $setting->name ?>">
                        <span class="glyphicon glyphicon glyphicon-play icon"></span><?= __c('button.execute') ?>
                    </a>
                <?php }else{ ?>
                    <a href="javascript:void(0)" class="btn btn-ne copy" data-id="<?= $setting->id ?>" data-name="<?= $setting->name ?>">
                        <span class="glyphicon glyphicon-file icon"></span><?= __c('button.copy') ?>
                    </a>
                    <a href="javascript:void(0)" class="btn btn-danger delete" data-id="<?= $setting->id ?>" data-name="<?= $setting->name ?>">
                        <span class="glyphicon glyphicon-trash icon"></span><?= __c('button.delete') ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </td>
</tr>