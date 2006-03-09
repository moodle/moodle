<?php // $Id$

/**
* enrolment_factory is used to "manufacture" an instance of required enrolment plugin.
*/

class enrolment_factory {
    function factory($enrol = '') {
        global $CFG;
        if (!$enrol) {
            $enrol = $CFG->enrol;
        }
        require_once("$CFG->dirroot/enrol/$enrol/enrol.php");
        $class = "enrolment_plugin_$enrol";
        return new $class;
    }
}

?>