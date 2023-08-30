<?php

function local_qubitscourse_assign_courses($data, $editoroptions = NULL){
    global $DB, $CFG;

    // Check if timecreated is given.
    $data->timecreated  = !empty($data->timecreated) ? $data->timecreated : time();
    $data->timemodified = $data->timecreated;

    $newqubitsassgncourseid = $DB->insert_record('local_qubits_course', $data);
    $qbitsassgncourses = $DB->get_record("local_qubits_course", array('id' => $newqubitsassgncourseid));
    return $qbitsassgncourses;
}

function local_qubitscourse_update_courses($data, $editoroptions = NULL){
    global $DB, $CFG;

    $data->timemodified = time();

    $updatequbitsassgncourseid = $DB->update_record('local_qubits_course', $data);
    $qbitsassgncourses = $DB->get_record("local_qubits_course", array('id' => $updatequbitsassgncourseid));
    return $qbitsassgncourses;
}

function local_qubitscourse_get_ccdata($course_id){
    $course_customdata = [];
    $handler = core_course\customfield\course_handler::create();
    if ($customfields = $handler->export_instance_data($course_id)) {
        foreach ($customfields as $data) {
            $shortname = $data->get_shortname();
            $value = $data->get_data_controller()->get_value();
            $course_customdata[$shortname] = $value;
        }
    }
    return $course_customdata;
}