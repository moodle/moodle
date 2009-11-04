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

$PAGE->set_url(new moodle_url($CFG->wwwroot.'/mod/wiki/overridelock.php', array('id'=>$id, 'page'=>$page)));

if (! $cm = get_coursemodule_from_id('wiki', $id)) {
    print_error('invalidcoursemodule');
}
if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}
if (! $wiki = $DB->get_record("wiki", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

if(!confirm_sesskey()) {
    print_error('confirmsesskeybad');
}
if(!data_submitted()) {
    print_error('invalidformdata');
}

require_course_login($course, true, $cm);

$modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
if(!has_capability('mod/wiki:overridelock', $modcontext)) {
    print_error('nopermissiontooverride', 'wiki');
}

$actions = explode('/', $page,2);
if(count($actions)!=2) {
    print_error('invalidpageval', 'wiki');
}
$pagename=$actions[1];
$DB->delete_records('wiki_locks', array('pagename'=>$pagename, 'wikiid'=>$wiki->id));

redirect("view.php?id=$id&page=".urlencode($page));

