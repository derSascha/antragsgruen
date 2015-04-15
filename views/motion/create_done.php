<?php

use app\components\UrlHelper;
use app\models\db\Motion;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var Motion $motion
 * @var string $mode
 */

$this->title = Yii::t('motion', $mode == 'create' ? 'Antrag stellen' : 'Antrag bearbeiten');

$params->breadcrumbs[] = $this->title;
$params->breadcrumbs[] = 'Bestätigen';


echo '<h1>' . Yii::t('motion', 'Antrag eingereicht') . '</h1>';

// @TODO
//echo $text = $antrag->veranstaltung->getStandardtext("antrag_eingereicht")->getHTMLText();

echo Html::beginForm(UrlHelper::createUrl('consultation/index'), 'post', ['id' => 'motionConfirmedForm']);
echo '<p><button type="submit" class="btn btn-success">Zurück zur Startseite</button></p>';
echo Html::endForm();
