<?php

function local_qubitscourse_output_fragment_enrol_users_form($args) {
    $args = (object) $args;
    $context = $args->context;
    $o = '';

    require_capability('enrol/manual:enrol', $context);
    $mform = new local_qubitscourse_enrol_users_form(null, $args);

    ob_start();
    $mform->display();
    $o .= ob_get_contents();
    ob_end_clean();

    return $o;
}