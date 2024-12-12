<?php

require_once('/var/www/html/moodle/custom/app/Models/BaseModel.php');
require_once('/var/www/html/moodle/custom/app/Models/EventModel.php');
require_once('/var/www/html/moodle/custom/app/Models/EventCustomFieldModel.php');

class EventCustomFieldController
{

    private $eventModel;
    private $eventCustomFieldModel;

    public function __construct()
    {
        $this->eventCustomFieldModel = new eventCustomFieldModel();
        $this->eventModel = new eventModel();
    }

    public function getEventCustomField($eventId)
    {
        $event = $this->eventModel->getEventById($eventId);
        $fieldList = $this->eventCustomFieldModel->getEventsCustomFieldByEventId($eventId);
        $passage = '';
        foreach ($fieldList as $fields) {
            $passage .= '<label class="label_name" for="name">' . $fields['field_name'] . '</label>';
            if ($fields['field_type'] == 'checkbox' || $fields['field_type'] == 'radio') {
                $options = explode(",", $fields['field_options']);
                foreach ($options as $i => $option) {
                    if ($fields['field_type'] == 'radio') {
                        $passage .= '<div class="radio-group">';
                        $checked = ($i == 0) ? 'checked' : '';
                    } else {
                        $passage .= '<div class="checkbox-group">';
                    }
                    $passage .= '<label class="label_d_flex"><input type="' . $fields['field_type'] . '" name="' . $fields['name'] . '" value="' . $option . '"' . $checked . '>' . $option . '</label></div>';
                }
                continue;
            }
            if ($fields['field_type'] == 'textarea') {
                $passage .= '<textarea name="' . $fields['name'] . '" rows="4" cols="50"></textarea>';
                continue;
            }
            $passage .= '<input type="' . $fields['field_type'] . '" name="' . $fields['name'] . '">';
        }

        return ['passage' => $passage, 'event' => $event];
    }

    public function getEventCustomFieldBackend($eventId)
    {
        $fieldList = $this->eventCustomFieldModel->getEventsCustomFieldByEventId($eventId);

        return ['fieldList' => $fieldList];
    }
}
