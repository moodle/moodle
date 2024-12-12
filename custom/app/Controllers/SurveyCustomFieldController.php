<?php

require_once('/var/www/html/moodle/custom/app/Models/BaseModel.php');
require_once('/var/www/html/moodle/custom/app/Models/SurveyCustomFieldModel.php');

class SurveyCustomFieldController
{

    private $surveyCustomFieldModel;

    public function __construct()
    {
        $this->surveyCustomFieldModel = new surveyCustomFieldModel();
    }

    public function getSurveyCustomField($eventId)
    {
        $fieldList = $this->surveyCustomFieldModel->getSurveyCustomFieldByEventId($eventId);
        $passage = '';
        foreach ($fieldList as $fields) {
            $passage .= '<label for="name">' . $fields['field_name'] . ':</label>';
            if ($fields['field_type'] == 'checkbox' || $fields['field_type'] == 'radio') {
                $options = explode(",", $fields['field_options']);
                foreach ($options as $i => $option) {
                    if ($fields['field_type'] == 'radio') {
                        $checked = ($i == 0) ? 'checked' : '';
                    }
                    $passage .= '<label class="label_d_flex"><input type="' . $fields['field_type'] . '" name="' . $fields['name'] . '" value="' . $option . '"' . $checked . '>' . $option . '</label>';
                }
                continue;
            }
            if ($fields['field_type'] == 'textarea') {
                $passage .= '<textarea name="' . $fields['name'] . '" rows="4" cols="50"></textarea>';
                continue;
            }
            $passage .= '<input type="' . $fields['field_type'] . '" name="' . $fields['name'] . '">';
        }

        return ['passage' => $passage];
    }

    public function getSurveyCustomFieldBackend($eventId)
    {
        $fieldList = $this->surveyCustomFieldModel->getSurveyCustomFieldByEventId($eventId);

        return ['fieldList' => $fieldList];
    }
}
