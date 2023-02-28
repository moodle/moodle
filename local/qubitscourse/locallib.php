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