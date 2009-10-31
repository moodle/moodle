<?php

/**
* enrolment_factory is used to "manufacture" an instance of required enrolment plugin.
*/

class enrolment_factory {
    function factory($enrol = '') {
        global $CFG, $OUTPUT;
        if (!$enrol) {
            $enrol = $CFG->enrol;
        }
        if (file_exists("$CFG->dirroot/enrol/$enrol/enrol.php")) {
            require_once("$CFG->dirroot/enrol/$enrol/enrol.php");
            $class = "enrolment_plugin_$enrol";
            return new $class;
        } else {
            error_log("$CFG->dirroot/enrol/$enrol/enrol.php does not exist");
            echo $OUTPUT->notification("Enrolment file $enrol/enrol.php does not exist");
        }
    }
}
