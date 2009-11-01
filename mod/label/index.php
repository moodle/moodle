<?php

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id',PARAM_INT);   // course

$PAGE->set_url(new moodle_url($CFG->wwwroot.'/mod/label/index.php', array('id'=>$id)));

redirect("$CFG->wwwroot/course/view.php?id=$id");


