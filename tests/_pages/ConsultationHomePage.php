<?php

namespace app\tests\_pages;

use yii\codeception\BasePage;

/**
 * Represents contact page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ConsultationHomePage extends BasePage
{
    public $route = 'consultation/index';

    /**
     * @param int $motionTypeId
     * @param bool $check
     * @return MotionCreatePage
     */
    public function gotoMotionCreatePage($motionTypeId = 1, $check = true)
    {
        $page = MotionCreatePage::openBy(
            $this->actor,
            [
                'subdomain'        => 'stdparteitag',
                'consultationPath' => 'std-parteitag',
                'motionTypeId'     => $motionTypeId,
            ]
        );
        if ($check) {
            $this->actor->see(mb_strtoupper('Antrag stellen'), 'h1');
        }
        return $page;
    }
}