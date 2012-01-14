<?php

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('toinodb');

    $confirm = optional_param('confirm', 0, PARAM_BOOL);

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    echo $OUTPUT->header();
    echo $OUTPUT->heading('Convert all MySQL tables from MYISAM to InnoDB');

    if ($DB->get_dbfamily() != 'mysql') {
        notice('This function is for MySQL databases only!', 'index.php');
    }

    if (data_submitted() and $confirm and confirm_sesskey()) {

        echo $OUTPUT->notification('Please be patient and wait for this to complete...', 'notifysuccess');

        if ($tables = $DB->get_tables()) {
            $DB->set_debug(true);
            foreach ($tables as $table) {
                $fulltable = $DB->get_prefix().$table;
                $DB->change_database_structure("ALTER TABLE $fulltable ENGINE=INNODB");
            }
            $DB->set_debug(false);
        }
        echo $OUTPUT->notification('... done.', 'notifysuccess');
        echo $OUTPUT->continue_button('index.php');
        echo $OUTPUT->footer();

    } else {
        $optionsyes = array('confirm'=>'1', 'sesskey'=>sesskey());
        $formcontinue = new single_button(new moodle_url('/admin/innodb.php', $optionsyes), get_string('yes'));
        $formcancel = new single_button(new moodle_url('/admin/index.php'), get_string('no'), 'get');
        echo $OUTPUT->confirm('Are you sure you want convert all your tables to the InnoDB format?', $formcontinue, $formcancel);
        echo $OUTPUT->footer();
    }


