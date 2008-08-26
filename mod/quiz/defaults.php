<?php // $Id$

// This file is generally only included from upgrade_activity_modules()
// It defines default values for any important configuration variables

    $defaults = array (
        '_use_config_plugins' => true,

        'review' => 0x3fffffff,
        'attemptonlast' => 0,
        'attempts' => 0,
        'grademethod' => QUIZ_GRADEHIGHEST,
        'decimalpoints' => 2,
        'maximumgrade' => 10,
        'password' => '',
        'popup' => 0,
        'questionsperpage' => 0,
        'shuffleanswers' => 1,
        'shufflequestions' => 0,
        'subnet' => '',
        'timelimit' => 0,
        'optionflags' => 1,
        'penaltyscheme' => 1,
        'delay1' => 0,
        'delay2' => 0,

        'fix_review' => 0,
        'fix_attemptonlast' => 0,
        'fix_attempts' => 0,
        'fix_grademethod' => 0,
        'fix_decimalpoints' => 0,
        'fix_password' => 0,
        'fix_popup' => 0,
        'fix_questionsperpage' => 0,
        'fix_shuffleanswers' => 0,
        'fix_shufflequestions' => 0,
        'fix_subnet' => 0,
        'fix_timelimit' => 0,
        'fix_optionflags' => 0,
        'fix_penaltyscheme' => 0,
        'fix_delay1' => 0,
        'fix_delay2' => 0,
    );
?>
