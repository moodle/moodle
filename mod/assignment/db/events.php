<?php

$handlers = array (

/*
 * Assignment Events - for plagiarism submission
 */
    'assignment_file_sent' => array (
        'handlerfile'      => '/lib/plagiarismlib.php',
        'handlerfunction'  => 'plagiarism_event_assignment_file_submission',
        'schedule'         => 'cron'
    ),
    'assignment_finalize_sent' => array (
        'handlerfile'      => '/lib/plagiarismlib.php',
        'handlerfunction'  => 'plagiarism_event_assignment_file_submission',
        'schedule'         => 'cron'
    ),
    'assignment_mod_created' => array (
        'handlerfile'      => '/lib/plagiarismlib.php',
        'handlerfunction'  => 'plagiarism_event_assignment_mod_created',
        'schedule'         => 'cron'
    ),
    'assignment_mod_updated' => array (
        'handlerfile'      => '/lib/plagiarismlib.php',
        'handlerfunction'  => 'plagiarism_event_assignment_mod_updated',
        'schedule'         => 'cron'
    ),
    'assignment_mod_deleted' => array (
        'handlerfile'      => '/lib/plagiarismlib.php',
        'handlerfunction'  => 'plagiarism_event_assignment_mod_deleted',
        'schedule'         => 'cron'
    ),

);