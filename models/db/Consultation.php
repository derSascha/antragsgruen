<?php

namespace app\models\db;

use app\models\amendmentNumbering\IAmendmentNumbering;
use app\models\exceptions\DB;
use app\models\exceptions\Internal;
use app\models\forms\SiteCreateForm;
use app\models\initiatorForms\DefaultForm;
use app\models\policies\IPolicy;
use app\models\sitePresets\ISitePreset;
use app\models\wording\PageData;
use app\models\pdfLayouts\IPDFLayout;
use yii\db\ActiveRecord;

/**
 * @package app\models\db
 *
 * @property int $id
 * @property int $siteId
 * @property int $type
 * @property int $amendmentNumbering
 *
 * @property string $urlPath
 * @property string $title
 * @property string $titleShort
 * @property string $wordingBase
 * @property string $eventDateFrom
 * @property string $eventDateTo
 * @property string $deadlineMotions
 * @property string $deadlineAmendments
 * @property string $policyMotions
 * @property string $policyAmendments
 * @property string $policyComments
 * @property string $policySupport
 * @property string $adminEmail
 * @property string $settings
 *
 * @property Site $site
 * @property Motion[] $motions
 * @property ConsultationText[] $texts
 * @property User[] $admins
 * @property ConsultationOdtTemplate[] $odtTemplates
 * @property ConsultationSubscription[] $subscriptions
 * @property ConsultationSettingsTag[] $tags
 * @property ConsultationSettingsMotionSection[] $motionSections
 * @property ConsultationSettingsMotionType[] $motionTypes
 */
class Consultation extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'consultation';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'policyMotions', 'policyAmendments', 'policyComments', 'policySupport'], 'required'],
            [['title', 'titleShort', 'eventDateFrom', 'eventDateTo'], 'safe'],
            [['adminEmail', 'wordingBase', 'amendmentNumbering'], 'safe'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'siteId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotions()
    {
        return $this->hasMany(Motion::className(), ['consultationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTexts()
    {
        return $this->hasMany(ConsultationText::className(), ['consultationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmins()
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])
            ->viaTable('consultationAdmin', ['consultationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOdtTemplates()
    {
        return $this->hasMany(ConsultationOdtTemplate::className(), ['consultationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(ConsultationSubscription::className(), ['consultationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(ConsultationSettingsTag::className(), ['consultationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotionSections()
    {
        return $this->hasMany(ConsultationSettingsMotionSection::className(), ['consultationId' => 'id'])
            ->where('status = ' . ConsultationSettingsMotionSection::STATUS_VISIBLE)
            ->orderBy('position');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotionTypes()
    {
        return $this->hasMany(ConsultationSettingsMotionType::className(), ['consultationId' => 'id']);
    }

    /** @var null|\app\models\settings\Consultation */
    private $settingsObject = null;

    /**
     * @return \app\models\settings\Consultation
     */
    public function getSettings()
    {
        if (!is_object($this->settingsObject)) {
            $this->settingsObject = new \app\models\settings\Consultation($this->settings);
        }
        return $this->settingsObject;
    }

    /**
     * @param \app\models\settings\Consultation $settings
     */
    public function setSettings($settings)
    {
        $this->settingsObject = $settings;
        $this->settings       = $settings->toJSON();
    }

    /**
     * @return IAmendmentNumbering
     */
    public function getAmendmentNumbering()
    {
        $numberings = IAmendmentNumbering::getNumberings();
        return new $numberings[$this->amendmentNumbering]();
    }


    /**
     * @param SiteCreateForm $form
     * @param Site $site
     * @param User $currentUser
     * @param ISitePreset $preset
     * @return Consultation
     * @throws DB
     */
    public static function createFromForm(SiteCreateForm $form, Site $site, User $currentUser, ISitePreset $preset)
    {
        $con                     = new Consultation();
        $con->siteId             = $site->id;
        $con->title              = $form->title;
        $con->titleShort         = $form->title;
        $con->type               = $form->preset;
        $con->urlPath            = $form->subdomain;
        $con->adminEmail         = $currentUser->email;
        $con->amendmentNumbering = 0;

        $settings                   = $con->getSettings();
        $settings->maintainanceMode = !$form->openNow;
        $con->setSettings($settings);

        $preset::setConsultationSettings($con);

        if (!$con->save()) {
            throw new DB($con->getErrors());
        }
        return $con;
    }


    /**
     * @return ConsultationSettingsTag[]
     */
    public function getSortedTags()
    {
        $tags = $this->tags;
        usort(
            $tags,
            function ($tag1, $tag2) {
                /** @var ConsultationSettingsTag $tag1 */
                /** @var ConsultationSettingsTag $tag2 */
                if ($tag1->position < $tag2->position) {
                    return -1;
                }
                if ($tag1->position > $tag2->position) {
                    return 1;
                }
                return 0;
            }
        );
        return $tags;
    }


    /**
     * @return IPolicy
     */
    public function getMotionPolicy()
    {
        return IPolicy::getInstanceByID($this->policyMotions, $this);
    }

    /**
     * @return IPolicy
     */
    public function getAmendmentPolicy()
    {
        return IPolicy::getInstanceByID($this->policyAmendments, $this);
    }

    /**
     * @return IPolicy
     */
    public function getCommentPolicy()
    {
        return IPolicy::getInstanceByID($this->policyComments, $this);
    }

    /**
     * @return IPolicy
     */
    public function getSupportPolicy()
    {
        return IPolicy::getInstanceByID($this->policySupport, $this);
    }

    /**
     * @return DefaultForm
     */
    public function getMotionInitiatorFormClass()
    {
        return new DefaultForm($this);
    }

    /**
     * @return DefaultForm
     */
    public function getAmendmentInitiatorFormClass()
    {
        return new DefaultForm($this);
    }

    /**
     * @return IPDFLayout
     */
    public function getPDFLayoutClass()
    {
        return new IPDFLayout($this);
    }

    /**
     * @return bool
     */
    public function motionDeadlineIsOver()
    {
        $normalized = str_replace(array(" ", ":", "-"), array("", "", ""), $this->deadlineMotions);
        if ($this->deadlineMotions != "" && date("YmdHis") > $normalized) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function amendmentDeadlineIsOver()
    {
        $normalized = str_replace(array(" ", ":", "-"), array("", "", ""), $this->deadlineAmendments);
        if ($this->deadlineAmendments != "" && date("YmdHis") > $normalized) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return int[]
     */
    public function getInvisibleMotionStati()
    {
        if ($this->getSettings()->screeningMotionsShown) {
            return [Motion::STATUS_DELETED, Motion::STATUS_UNCONFIRMED];
        } else {
            return [Motion::STATUS_DELETED, Motion::STATUS_UNCONFIRMED, Motion::STATUS_SUBMITTED_UNSCREENED];
        }
    }

    /**
     * @return int[]
     */
    public function getInvisibleAmendmentStati()
    {
        return $this->getInvisibleMotionStati();
    }

    /**
     * @param int $motionTypeId
     * @return string
     */
    public function getNextAvailableStatusString($motionTypeId)
    {
        $max_rev = 0;
        /** @var ConsultationSettingsMotionType $motionType */
        $motionType = null;
        foreach ($this->motionTypes as $t) {
            if ($t->id == $motionTypeId) {
                $motionType = $t;
            }
        }
        $prefix = $motionType->motionPrefix;
        if ($prefix == '') {
            $prefix = 'A';
        }
        foreach ($this->motions as $motion) {
            if ($motion->status != Motion::STATUS_DELETED) {
                if (mb_substr($motion->titlePrefix, 0, mb_strlen($prefix)) !== $prefix) {
                    continue;
                }
                $revs  = mb_substr($motion->titlePrefix, mb_strlen($prefix));
                $revnr = IntVal($revs);
                if ($revnr > $max_rev) {
                    $max_rev = $revnr;
                }
            }
        }
        return $prefix . ($max_rev + 1);
    }

    /**
     *
     */
    public function flushCaches()
    {
        // @TODO
    }
}
