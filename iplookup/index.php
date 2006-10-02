<?php // $Id$
      // Do an IP lookup of a user, using selected plugin

    require('../config.php');

    require_login();

    $ip   = optional_param('ip', getremoteaddr());
    $user = optional_param('user', $USER->id);

    if (empty($CFG->iplookup)) {
        set_config('iplookup', 'hostip');
    }

    require("$CFG->dirroot/iplookup/$CFG->iplookup/lib.php");

    iplookup_display($ip, $user);

?>
