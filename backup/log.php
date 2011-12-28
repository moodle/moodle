<?php
       // log.php - old scheduled backups report. Now redirecting
       // to the new admin one

    require_once("../config.php");

    require_login();

    require_capability('moodle/backup:backupcourse', get_context_instance(CONTEXT_SYSTEM));

    redirect("$CFG->wwwroot/report/backups/index.php", '', 'admin', 1);
