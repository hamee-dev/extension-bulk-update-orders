<?php
/**
 * Googleタグマネージャー
 * このテンプレートファイルは、開始タグ <body> の直後に貼り付けてください。
 */
$gtm_container_id = Config::get('gtm.container_id');
?>
<?php if(!empty($gtm_container_id)) { ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $gtm_container_id ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php } ?>