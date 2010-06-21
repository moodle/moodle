<?php

function xmldb_enrol_self_install() {
    global $CFG;

    if (isset($CFG->sendcoursewelcomemessage)) {
        set_config('sendcoursewelcomemessage', $CFG->sendcoursewelcomemessage, 'enrol_self');
        unset_config('sendcoursewelcomemessage');
    }
}
