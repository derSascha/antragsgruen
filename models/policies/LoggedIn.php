<?php

namespace app\models\policies;

use app\models\db\User;

class LoggedIn extends IPolicy
{
    /**
     * @static
     * @return int
     */
    public static function getPolicyID()
    {
        return 2;
    }

    /**
     * @static
     * @return string
     */
    public static function getPolicyName()
    {
        return "Eingeloggte";
    }

    /**
     * @return bool
     */
    protected function isWriteForbidden()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            return false;
        }
        if (!$this->motionType->consultation->site->getSettings()->managedUserAccounts) {
            return false;
        }
        $privilege = $this->motionType->consultation->getUserPrivilege($user);
        return ($privilege->privilegeCreate == 0);
    }

    /**
     * @static
     * @param bool $allowAdmins
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function checkCurUserHeuristically($allowAdmins = true)
    {
        return !$this->isWriteForbidden();
    }

    /**
     * @return string
     */
    public function getOnCreateDescription()
    {
        return 'Eingeloggte';
    }

    /**
     * @return string
     */
    public function getPermissionDeniedMotionMsg()
    {
        if ($this->isWriteForbidden()) {
            return 'Nur von den AdministratorInnen explizit zugelassene BenutzerInnen können Anträge stellen.';
        }
        return 'Du musst dich einloggen, um Anträge stellen zu können.';
    }

    /**
     * @return string
     */
    public function getPermissionDeniedAmendmentMsg()
    {
        if ($this->isWriteForbidden()) {
            return 'Nur von den AdministratorInnen explizit zugelassene BenutzerInnen können Änderungsanträge stellen.';
        }
        return 'Du musst dich einloggen, um Änderungsanträge stellen zu können.';
    }

    /**
     * @return string
     */
    public function getPermissionDeniedSupportMsg()
    {
        if ($this->isWriteForbidden()) {
            return 'Nur von den AdministratorInnen explizit zugelassene BenutzerInnen können Anträge unterstützen.';
        }
        return 'Du musst dich einloggen, um Anträge unterstützen zu können.';
    }

    /**
     * @return string
     */
    public function getPermissionDeniedCommentMsg()
    {
        if ($this->isWriteForbidden()) {
            return 'Nur von den AdministratorInnen explizit zugelassene BenutzerInnen können Kommentare schreiben.';
        }
        return 'Du musst dich einloggen, um Kommentare schreiben zu können.';
    }


    /**
     * @param bool $allowAdmins
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function checkMotionSubmit($allowAdmins = true)
    {
        if ($this->isWriteForbidden()) {
            return false;
        }
        return (!\Yii::$app->user->isGuest);
    }
}
