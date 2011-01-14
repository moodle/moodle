<?php

$settings = new admin_externalpage('qtypesettingopaque',
        get_string('pluginname', 'qtype_opaque'),
        new moodle_url('/question/type/opaque/engines.php'),
        'moodle/question:config');
