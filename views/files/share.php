<?php $this->registerJsFile('web/js/files/share.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<div class="alert alert alert-warning">Press the button Copy this for share</div>
<div class="alert alert-success" style="display: none">Share link successfully copied to your buffer!</div>
<div class="well well-lg">
    <span class="js-textlink">
        <?=$url?>
    </span>
    <button class="js-textcopybtn btn btn-warning" type="button">Copy this</button>
</div>
