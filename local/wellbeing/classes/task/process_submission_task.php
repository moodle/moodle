<?php

namespace local_wellbeing\task;

defined('MOODLE_INTERNAL') || die();

class process_submission_task extends \core\task\adhoc_task {

    public function execute() {

        $data = $this->get_custom_data();

        if (empty($data->submissionid)) {
            return;
        }

        \local_wellbeing\service\analysis_service::process_submission(
            (int)$data->submissionid
        );
    }
}