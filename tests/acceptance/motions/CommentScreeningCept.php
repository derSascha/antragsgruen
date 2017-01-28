<?php

/** @var \Codeception\Scenario $scenario */
$I = new AcceptanceTester($scenario);
$I->populateDBData1();

$I->wantTo('write a comment (logged out)');
$I->gotoConsultationHome(true, 'bdk', 'bdk')->gotoMotionView(4);

$I->wait(0.5);
$I->dontSee('Kommentar schreiben');
$I->click('#section_21_1 .comment .shower');
$I->dontSee('Kommentar schreiben', '#section_21_1');
$I->dontSeeElement('.commentForm');
$I->see('Logge dich ein, um kommentieren zu können');


$I->wantTo('write a comment (without screening)');
$I->loginAsStdUser();
$I->gotoConsultationHome(true, 'bdk', 'bdk')->gotoMotionView(4);

$I->wait(0.5);
$I->dontSee('Kommentar schreiben');
$I->click('#section_21_1 .comment .shower');
$I->see('Kommentar schreiben', '#section_21_1');
$I->fillField('#comment_21_1_name', 'My Name');
$I->fillField('#comment_21_1_email', '');
$I->fillField('#comment_21_1_text', 'Some Text');
$I->submitForm('#section_21_1 .commentForm', [], 'writeComment');

$I->see('My Name', '#section_21_1 .motionComment');
$I->see('Some Text', '#section_21_1 .motionComment');
$I->dontSee('#section_21_1 .motionComment .delLink');




$I->wantTo('enable screening and force e-mails');
$I->logout();
$I->loginAsStdAdmin();
$I->dontSeeElement('#adminTodo');
$I->gotoStdAdminPage('bdk', 'bdk')->gotoConsultation();
$I->dontSeeCheckboxIsChecked('#screeningComments');
$I->dontSeeCheckboxIsChecked('#commentNeedsEmail');
$I->checkOption('#screeningComments');
$I->checkOption('#commentNeedsEmail');
$I->submitForm('#consultationSettingsForm', [], 'save');
$I->seeCheckboxIsChecked('#screeningComments');
$I->seeCheckboxIsChecked('#commentNeedsEmail');
$I->logout();



$I->wantTo('write a comment (with screening)');
$I->gotoConsultationHome(true, 'bdk', 'bdk')->gotoMotionView(4);
$I->loginAsStdUser();

$I->dontSee('Kommentar schreiben');
$I->click('#section_21_1 .comment .shower');
$I->see('Kommentar schreiben', '#section_21_1');
$I->fillField('#comment_21_1_name', 'Mein Name 2');
$I->fillField('#comment_21_1_email', '');
$I->fillField('#comment_21_1_text', 'Noch ein zweiter Kommentar');
$I->submitForm('#section_21_1 .commentForm', [], 'writeComment');

$I->see('Keine E-Mail-Adresse angegeben');
$I->fillField('#comment_21_1_email', 'testuser@example.org');
$I->submitForm('#section_21_1 .commentForm', [], 'writeComment');

$I->dontSee('Mein Name 2', '#section_21_1 .motionComment');
$I->dontSee('Noch ein zweiter Kommentar', '#section_21_1 .motionComment');
$I->see('1 Kommentar wartet auf Freischaltung', '#section_21_1');


$I->fillField('#comment_21_1_name', 'Mein Name 3');
$I->fillField('#comment_21_1_email', 'testuser@example.org');
$I->fillField('#comment_21_1_text', 'Noch ein dritter Kommentar');
$I->submitForm('#section_21_1 .commentForm', [], 'writeComment');

$I->dontSee('Mein Name 3', '#section_21_1 .motionComment');
$I->dontSee('Noch ein dritter Kommentar', '#section_21_1 .motionComment');
$I->see('2 Kommentare warten auf Freischaltung', '#section_21_1');

$I->logout();



$I->wantTo('screen the comment');
$I->gotoConsultationHome(true, 'bdk', 'bdk');
$I->loginAsStdAdmin();
$I->click('#adminTodo');
$I->seeElement('.adminTodo .motionCommentScreen' . (AcceptanceTester::FIRST_FREE_COMMENT_ID + 1));
$I->seeElement('.adminTodo .motionCommentScreen' . (AcceptanceTester::FIRST_FREE_COMMENT_ID + 2));
$I->click('.adminTodo .motionCommentScreen' . (AcceptanceTester::FIRST_FREE_COMMENT_ID + 2) . ' a');

$I->see('Mein Name 2', '#section_21_1 .motionComment');
$I->see('Noch ein zweiter Kommentar', '#section_21_1 .motionComment');
$I->see('2 Kommentare warten auf Freischaltung', '#section_21_1');
$commId = (AcceptanceTester::FIRST_FREE_COMMENT_ID + 1);
$I->see('noch nicht freigeschaltet', '#comment' . $commId);
$I->submitForm('#comment' . $commId . ' form.screening', [], 'commentScreeningAccept');

$I->see('1 Kommentar wartet auf Freischaltung', '#section_21_1');
$commId = (AcceptanceTester::FIRST_FREE_COMMENT_ID + 2);
$I->see('noch nicht freigeschaltet', '#comment' . $commId);
$I->submitForm('#comment' . $commId . ' form.screening', [], 'commentScreeningReject');

$I->dontSeeElement('.commentScreeningQueue');
$I->see('Noch ein zweiter Kommentar');
$I->dontSee('Noch ein dritter Kommentar');


$I->gotoConsultationHome(true, 'bdk', 'bdk');
$I->dontSeeElement('#adminTodo');
