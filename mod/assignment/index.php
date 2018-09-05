<?php

require_once("../../config.php");

$id = required_param('id', PARAM_INT);

// Rest in peace old assignment!
redirect(new moodle_url('/mod/assign/index.php', array('id' => $id)));
