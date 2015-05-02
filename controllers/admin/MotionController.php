<?php

namespace app\controllers\admin;

use app\components\UrlHelper;
use app\models\db\ConsultationSettingsMotionSection;
use app\models\db\Motion;
use app\models\exceptions\FormError;
use app\models\sectionTypes\ISectionType;
use app\models\sectionTypes\TabularData;

class MotionController extends AdminBase
{
    /**
     * @throws FormError
     */
    private function sectionsSave()
    {
        $position = 0;
        foreach ($_POST['sections'] as $sectionId => $data) {
            if (preg_match('/^new[0-9]+$/', $sectionId)) {
                $section                 = new ConsultationSettingsMotionSection();
                $section->consultationId = $this->consultation->id;
                $section->type           = $data['type'];
                $section->status         = ConsultationSettingsMotionSection::STATUS_VISIBLE;
            } else {
                /** @var ConsultationSettingsMotionSection $section */
                $section = $this->consultation->getMotionSections()->andWhere('id = ' . IntVal($sectionId))->one();
                if (!$section) {
                    throw new FormError("Section not found: " . $sectionId);
                }
            }
            $section->setAdminAttributes($data);
            $section->position = $position;

            $section->save();

            $position++;
        }
    }

    /**
     * @throws FormError
     */
    private function sectionsDelete()
    {
        if (!isset($_POST['sectionsTodelete'])) {
            return;
        }
        foreach ($_POST['sectionsTodelete'] as $sectionId) {
            if ($sectionId > 0) {
                $sectionId = IntVal($sectionId);
                /** @var ConsultationSettingsMotionSection $section */
                $section = $this->consultation->getMotionSections()->andWhere('id = ' . $sectionId)->one();
                if (!$section) {
                    throw new FormError("Section not found: " . $sectionId);
                }
                $section->status = ConsultationSettingsMotionSection::STATUS_DELETED;
                $section->save();
            }
        }
    }

    /**
     * @return string
     * @throws FormError
     */
    public function actionSections()
    {
        if (isset($_POST['save'])) {
            $this->sectionsSave();
            $this->sectionsDelete();

            \yii::$app->session->setFlash('success', 'Gespeichert.');
        }

        return $this->render('sections', ['consultation' => $this->consultation]);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $motions = $this->consultation->motions;
        return $this->render('index', ['motions' => $motions]);
    }

    /**
     * @param int $motionId
     * @return string
     */
    public function actionUpdate($motionId)
    {
        $motionId = IntVal($motionId);

        /** @var Motion $motion */
        $motion = Motion::findOne($motionId);
        if (!$motion) {
            $this->redirect(UrlHelper::createUrl("admin/motion/index"));
        }

        $this->checkConsistency($motion);

        return $this->render('update', ['motion' => $motion]);
    }
}