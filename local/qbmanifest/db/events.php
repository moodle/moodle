<?php
// List of observers.
$observers = array(

    array(
        'eventname' => '\core\event\course_module_created',
        'callback'  => 'local_qbmanifest_observer::course_module_created',
    ),
);