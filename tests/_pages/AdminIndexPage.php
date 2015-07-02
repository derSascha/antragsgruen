<?php

namespace app\tests\_pages;

use yii\codeception\BasePage;

/**
 * Represents contact page
 * @property \AntragsgruenAcceptenceTester|\FunctionalTester $actor
 */
class AdminIndexPage extends BasePage
{
    public $route = 'admin/index';

    /**
     * @param int $motionTypeId
     * @return AdminMotionTypePage
     */
    public function gotoMotionTypes($motionTypeId)
    {
        $this->actor->click('.motionType' . $motionTypeId);
        $this->actor->see(mb_strtoupper('Antragstyp bearbeiten'), 'h1');
        return new AdminMotionTypePage($this->actor);
    }


    /**
     * @return AdminMotionIndexPage
     */
    public function gotoMotionIndex()
    {
        $this->actor->click('.motionIndex');
        $this->actor->see(mb_strtoupper('Anträge'), 'h1');
        return new AdminMotionIndexPage($this->actor);
    }

    /**
     * @return AdminConsultationPage
     */
    public function gotoConsultation()
    {
        $this->actor->click('#consultationLink');
        return new AdminConsultationPage($this->actor);
    }
}