<?php
/**
 * coursetags_add.php
 * @author j.beedell@open.ac.uk June07
 */

require_once('../config.php');

require_login();

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/tag:create', $systemcontext);

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$returnurl = optional_param('returnurl', null, PARAM_TEXT);
$keyword = optional_param('coursetag_new_tag', '', PARAM_TEXT);
$courseid = optional_param('entryid', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

$keyword = trim(strip_tags($keyword));
if ($keyword and confirm_sesskey()) {

    require_once($CFG->dirroot.'/tag/coursetagslib.php');

    if ($courseid > 0 and $userid > 0) {
        $myurl = 'tag/search.php';
        $keywords = explode(',', $keyword);
        coursetag_store_keywords($keywords, $courseid, $userid, 'default', $myurl);
    }
}

// send back to originating page, where the new tag will be visible in the block
if ($returnurl) {
    redirect($returnurl);
} else {
    $myurl = $CFG->wwwroot.'/';
}

redirect($myurl);
