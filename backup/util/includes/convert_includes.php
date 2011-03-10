<?php

// Prevent direct access to this file
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

// Include all the convert stuff needed
require_once($CFG->dirroot.'/backup/backup.class.php');
require_once($CFG->dirroot.'/backup/util/factories/convert_factory.class.php');
require_once($CFG->dirroot.'/backup/util/converter/base_converter.class.php');
require_once($CFG->dirroot.'/backup/util/converter/plan_converter.class.php');
require_once($CFG->dirroot.'/backup/util/helper/convert_helper.class.php');
require_once($CFG->dirroot.'/backup/util/plan/base_plan.class.php');
require_once($CFG->dirroot.'/backup/util/plan/base_step.class.php');
require_once($CFG->dirroot.'/backup/util/plan/base_task.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_plan.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_step.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_task.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_structure_step.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_execution_step.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_.class.php');
require_once($CFG->dirroot.'/backup/moodle2/convert_stepslib.php');

// And some moodle stuff too
require_once($CFG->libdir.'/fileslib.php');