<?php

namespace app\models\db;

use yii\db\ActiveRecord;

/**
 * Class ConsultationAgendaItem
 * @package app\models\db
 *
 * @property int $id
 * @property int $consultationId
 * @property int $parentItemId
 * @property int $position
 * @property string $code
 * @property string $title
 * @property string $description
 * @property int $motionTypeId
 * @property string $deadline
 *
 * @property Consultation $consultation
 * @property ConsultationAgendaItem $parentItem
 * @property ConsultationAgendaItem[] $childItems
 * @property ConsultationMotionType $motionType
 * @property Motion[] $motions
 */
class ConsultationAgendaItem extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'consultationAgendaItem';
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
    public function getParentItem()
    {
        return $this->hasOne(ConsultationAgendaItem::className(), ['id' => 'parentItemId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildItems()
    {
        return $this->hasMany(ConsultationAgendaItem::className(), ['parentItemId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotionType()
    {
        return $this->hasOne(ConsultationMotionType::className(), ['id' => 'motionTypeId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotions()
    {
        return $this->hasMany(Motion::className(), ['agendaItemId' => 'id']);
    }

    /**
     * @return Motion[]
     */
    public function getMotionsFromConsultation()
    {
        $return = [];
        foreach ($this->consultation->motions as $motion) {
            if (in_array($motion->status, $this->consultation->getInvisibleMotionStati())) {
                continue;
            }
            if ($motion->agendaItemId === null || $motion->agendaItemId != $this->id) {
                continue;
            }
            $return[] = $motion;
        }
        return $return;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'consultationId'], 'required'],
            [['title', 'code', 'description', 'deadline', 'position'], 'safe'],
            [['id', 'consultationId', 'parentItemId', 'position', 'motionTypeId'], 'number'],
        ];
    }

    /**
     * @param Consultation $consultation
     * @param int|null $parentItemId
     * @return ConsultationAgendaItem[]
     */
    public static function getItemsByParent(Consultation $consultation, $parentItemId)
    {
        $return = [];
        foreach ($consultation->agendaItems as $item) {
            if ($item->parentItemId == $parentItemId) {
                $return[] = $item;
            }
        }
        return $return;
    }
}