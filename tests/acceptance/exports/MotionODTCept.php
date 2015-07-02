<?php

/** @var \Codeception\Scenario $scenario */
$I = new AcceptanceTester($scenario);
$I->populateDBData1();

$scenario->incomplete('Not implemented yet');

$I->wantTo('test the single-motion-ODT from the admin interface');
$I->gotoStdConsultationHome();
$I->loginAsStdAdmin();
$I->click('#adminLink');
$I->click('.motionListAll');
$odt = $I->downloadLink('.adminMotionTable .motion3 a.odt');
if (strlen($odt) == 0) {
    $I->fail('ODT has no content');
}