<?php

namespace app\models\policies;

use app\models\wording\IWording;

class LoggedIn extends IPolicy
{
    /**
     * @static
     * @return string
     */
    public static function getPolicyID()
    {
        return "loggedin";
    }

    /**
     * @static
     * @param IWording $wording
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function getPolicyName(IWording $wording)
    {
        return "Eingeloggte";
    }

    /**
     * @static
     * @return bool
     */
    public function checkCurUserHeuristically()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getOnCreateDescription()
    {
        return "Eingeloggte";
    }

    /**
     * @param IWording $wording
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getPermissionDeniedMsg(IWording $wording)
    {
        return "Du musst dich einloggen.";
    }

    /**
     * @return bool
     */
    public function checkMotionSubmit()
    {
        return (!\Yii::$app->user->isGuest);
    }

    /**
     * @return bool
     */
    public function checkAmendmentSubmit()
    {
        return (!\Yii::$app->user->isGuest);
    }
}