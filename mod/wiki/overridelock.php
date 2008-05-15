<?php
/**
 * Handles what happens when a user with appropriate permission attempts to 
 * override a wiki page editing lock.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod-wiki
 * @category mod
 *//** */

require_once('../../config.php');

$id=required_param('id',PARAM_INT);
$page=required_param('page',PARAM_RAW);

if (! $cm = get_coursemodule_from_id('wiki', $id)) {
    error("Course Module ID was incorrect");
}
if (! $course = get_record("course", "id", $cm->course)) {
    error("Course is misconfigured");
}
if (! $wiki = get_record("wiki", "id", $cm->instance)) {
    error("Course module is incorrect");
}

if(!confirm_sesskey()) {
    error("Session key not set");
}
if(!data_submitted()) {
    error("Only POST requests accepted");
}

require_course_login($course, true, $cm);

$modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
if(!has_capability('mod/wiki:overridelock', $modcontext)) {
    error("You do not have the capability to override editing locks");
}

$actions = explode('/', $page,2);
if(count($actions)!=2) {
    error("Unsupported page value");
}
$pagename=addslashes($actions[1]);
if(!delete_records('wiki_locks','pagename',$pagename,'wikiid', $wiki->id)) {
    error('Unable to delete lock record');
}

redirect("view.php?id=$id&page=".urlencode($page));
?>