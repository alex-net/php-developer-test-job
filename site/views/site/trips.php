<?php

use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\Html;
use \yii\web\JsExpression;
use app\models\AirportName;

use app\models\TripService;
use app\models\Trip;

$f = ActiveForm::begin();
$formatJs = <<<JS
var formatRepoSelection = function (repo) {
    console.log(repo);
    return repo.text || 'кув';
    //return repo.full_name || repo.text;
}
JS;
$this->registerJs($formatJs, $this::POS_HEAD);

?>
<div class="row">
    <div class="col col-6">
        <?= $f->field($form, 'airPortId', ['enableClientValidation' => false])->widget(Select2::class, [
            'data' => $form->airPortId ? [$form->airPortId => AirportName::getAiroportNameById($form->airPortId)] : [],
            'options' => ['placeholder' => 'Выберите аэропорт'],
            'pluginOptions' => [
                'allowClear' => true,
                'ajax' => [
                    'url' => Url::to(['site/air-port-list']),
                    'dataType' => 'json',
                ],
                'templateSelection' => new JsExpression('formatRepoSelection'),
                'escapeMarkup' => new JsExpression('function (markup) {console.log("markup = ", markup);  return markup; }')
            ],
        ]) ?>
    </div>
    <div class="col-2">
        <?= $f->field($form, 'serviceId')->dropDownList(TripService::getServicesListForOptions(), [
            'prompt' => 'Выберите '. $form->getAttributeLabel('serviceId'),
        ]) ?>
    </div>
    <div class="col-2">
        <?= $f->field($form, 'coprorateId')->dropDownList(Trip::getCoprorateListForOptions(), [
            'prompt' => 'Выберите ' . $form->getAttributeLabel('coprorateId'),
        ]) ?>

    </div>
    <div class="col">
        <div>
        <label class="form-label" >&nbsp;</label>
        </div>
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?></div>
</div>

<?php ActiveForm::end(); ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $form->searchResult,
]) ?>