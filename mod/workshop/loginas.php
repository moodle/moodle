<?php

/**
 * Temporary script to log-in as a random workshop participant - useful for testing
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$cmid       = required_param('cmid', PARAM_INT); // course_module ID, or

$cm         = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$workshop   = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
require_capability('moodle/user:loginas', get_context_instance(CONTEXT_COURSE, $course->id));

$workshop = new workshop($workshop, $cm, $course);

$authors = $workshop->get_potential_authors(false);
$reviewers = $workshop->get_potential_reviewers(false);
$participants = array_intersect_key($authors, $reviewers);
$randomid = array_rand($participants);

redirect("{$CFG->wwwroot}/course/loginas.php?id={$course->id}&user={$randomid}&return=1&sesskey=" . sesskey());
