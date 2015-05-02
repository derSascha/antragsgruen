<?php

namespace app\models\db;

use app\models\exceptions\FormError;
use app\models\sectionTypes\ISectionType;
use app\models\sectionTypes\TabularData;
use yii\db\ActiveRecord;

/**
 * @package app\models\db
 *
 * @property int $id
 * @property int $consultationId
 * @property int $motionTypeId
 * @property int $type
 * @property int $position
 * @property int $status
 * @property string $title
 * @property string $data
 * @property int $fixedWidth
 * @property int $maxLen
 * @property int $required
 * @property int $lineNumbers
 * @property int $hasComments
 * @property int $hasAmendments
 *
 * @property Consultation $consultation
 * @property MotionSection[] $sections
 * @property ConsultationSettingsMotionType $motionType
 */
class ConsultationSettingsMotionSection extends ActiveRecord
{
    const COMMENTS_NONE       = 0;
    const COMMENTS_SECTION    = 1;
    const COMMENTS_PARAGRAPHS = 2;

    const STATUS_VISIBLE = 0;
    const STATUS_DELETED = -1;

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'consultationSettingsMotionSection';
    }

    /**
     * @return string[]
     */
    public static function getCommentTypes()
    {
        return [
            static::COMMENTS_NONE       => 'Keine Kommentare',
            static::COMMENTS_SECTION    => 'Abschnitt als ganzes kommentierbar',
            static::COMMENTS_PARAGRAPHS => 'Einzelne Absätze sind kommentierbar'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsultation()
    {
        return $this->hasOne(Consultation::className(), ['id' => 'consultationId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSections()
    {
        return $this->hasMany(MotionSection::className(), ['sectionId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotionType()
    {
        return $this->hasOne(ConsultationSettingsMotionType::className(), ['id' => 'motionTypeId']);
    }

    /**
     * @param array $data
     * @throws FormError
     */
    public function setAdminAttributes($data)
    {
        $this->setAttributes($data);

        if ($data['motionType'] > 0) {
            $motionType = IntVal($data['motionType']);
            /** @var ConsultationSettingsMotionSection $motionTypeObj */
            $motionTypeObj = $this->consultation->getMotionTypes()->andWhere('id = ' . $motionType)->one();
            if (!$motionTypeObj) {
                throw new FormError("MotionType not found: " . $motionType);
            }
            $this->motionTypeId = $motionType;
        } else {
            $this->motionTypeId = null;
        }

        $this->required      = (isset($data['required']) ? 1 : 0);
        $this->fixedWidth    = (isset($data['fixedWidth']) ? 1 : 0);
        $this->lineNumbers   = (isset($data['lineNumbers']) ? 1 : 0);
        $this->hasAmendments = (isset($data['hasAmendments']) ? 1 : 0);

        if ($this->type == ISectionType::TYPE_TABULAR) {
            $this->data = TabularData::saveTabularDataSettingsFromPost($this->data, $data);
        } else {
            $this->data = null;
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['consultationId', 'title', 'type', 'position', 'status', 'required'], 'required'],
            [['id', 'consultationId', 'type', 'motionTypeId', 'status', 'required'], 'number'],
            [['position', 'fixedWidth', 'maxLen', 'lineNumbers', 'hasComments', 'hasAmendments'], 'number'],
            [['title', 'maxLen', 'hasComments', 'hasAmendments'], 'safe'],
        ];
    }

    /**
     * @return int[]
     */
    public function getAvailableCommentTypes()
    {
        if ($this->type == ISectionType::TYPE_TEXT_SIMPLE) {
            return [static::COMMENTS_NONE, static::COMMENTS_SECTION, static::COMMENTS_PARAGRAPHS];
        }
        if ($this->type == ISectionType::TYPE_TEXT_HTML) {
            return [static::COMMENTS_NONE, static::COMMENTS_SECTION];
        }
        return [static::COMMENTS_NONE];
    }
}