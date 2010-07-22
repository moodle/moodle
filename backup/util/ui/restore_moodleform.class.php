<?php

require_once($CFG->dirroot.'/backup/util/ui/backup_moodleform.class.php');

class restore_moodleform extends base_moodleform {
    public function __construct(restore_ui_stage $uistage, $action = null, $customdata = null, $method = 'post', $target = '', $attributes = null, $editable = true) {
        parent::__construct($uistage, $action, $customdata, $method, $target, $attributes, $editable);
    }
}

class restore_settings_form extends restore_moodleform {}
class restore_schema_form extends restore_moodleform {}
class restore_review_form extends restore_moodleform {};