<?php
/**
 * Add/remove members from group.
 *
 * @copyright &copy; 2006 The Open University and others
 * @author N.D.Freear AT open.ac.uk
 * @author J.White AT open.ac.uk and others
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once($CFG->dirroot . '/user/selector/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/filelib.php');

$groupid = required_param('group', PARAM_INT);
$cancel  = optional_param('cancel', false, PARAM_BOOL);

$group = $DB->get_record('groups', array('id'=>$groupid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$group->courseid), '*', MUST_EXIST);

$PAGE->set_url('/groups/members.php', array('id'=>$groupid));
$PAGE->set_pagelayout('standard');

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id.'&group='.$group->id;

if ($cancel) {
    redirect($returnurl);
}

$groupmembersselector = new group_members_selector('removeselect', array('groupid' => $groupid, 'courseid' => $course->id));
$groupmembersselector->set_extra_fields(array());
$potentialmembersselector = new group_non_members_selector('addselect', array('groupid' => $groupid, 'courseid' => $course->id));
$potentialmembersselector->set_extra_fields(array());

if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoadd = $potentialmembersselector->get_selected_users();
    if (!empty($userstoadd)) {
        foreach ($userstoadd as $user) {
            if (!groups_add_member($groupid, $user->id)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
            $groupmembersselector->invalidate_selected_users();
            $potentialmembersselector->invalidate_selected_users();
        }
    }
}

if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoremove = $groupmembersselector->get_selected_users();
    if (!empty($userstoremove)) {
        foreach ($userstoremove as $user) {
            if (!groups_remove_member($groupid, $user->id)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
            $groupmembersselector->invalidate_selected_users();
            $potentialmembersselector->invalidate_selected_users();
        }
    }
}

// Print the page and form
$strgroups = get_string('groups');
$strparticipants = get_string('participants');
$stradduserstogroup = get_string('adduserstogroup', 'group');
$strusergroupmembership = get_string('usergroupmembership', 'group');

$groupname = format_string($group->name);

$PAGE->requires->yui2_lib('connection');
$PAGE->requires->js('/group/clientlib.js');
$PAGE->navbar->add($strparticipants, new moodle_url('/user/index.php', array('id'=>$course->id)));
$PAGE->navbar->add($strgroups, new moodle_url('/group/index.php', array('id'=>$course->id)));
$PAGE->navbar->add($stradduserstogroup);

/// Print header
$PAGE->set_title("$course->shortname: $strgroups");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('adduserstogroup', 'group').": $groupname", 3);

/// Print group info -  TODO: remove tables for layout here
$groupinfotable = new html_table();
$groupinfotable->attributes['class'] = 'groupinfobox';
$picturecell = new html_table_cell();
$picturecell->attributes['class'] = 'left side picture';
$picturecell->text = print_group_picture($group, $course->id, true, true, false);

$contentcell = new html_table_cell();
$contentcell->attributes['class'] = 'content';

$group->description = file_rewrite_pluginfile_urls($group->description, 'pluginfile.php', $context->id, 'group', 'description', $group->id);
if (!isset($group->descriptionformat)) {
    $group->descriptionformat = FORMAT_MOODLE;
}
$options = new stdClass;
$options->overflowdiv = true;
$contentcell->text = format_text($group->description, $group->descriptionformat, $options);
$groupinfotable->data[] = new html_table_row(array($picturecell, $contentcell));
echo html_writer::table($groupinfotable);

/// Print the editing form
?>

<div id="addmembersform">
    <form id="assignform" method="post" action="<?php echo $CFG->wwwroot; ?>/group/members.php?group=<?php echo $groupid; ?>">
    <div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

    <table class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">
    <tr>
      <td id='existingcell'>
          <p>
            <label for="removeselect"><?php print_string('groupmembers', 'group'); ?></label>
          </p>
          <?php $groupmembersselector->display(); ?>
          </td>
      <td id='buttonscell'>
        <p class="arrow_button">
            <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />
            <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('remove'); ?>" />
        </p>
      </td>
      <td id='potentialcell'>
          <p>
            <label for="addselect"><?php print_string('potentialmembs', 'group'); ?></label>
          </p>
          <?php $potentialmembersselector->display(); ?>
      </td>
      <td>
        <p><?php echo($strusergroupmembership) ?></p>
        <div id="group-usersummary"></div>
      </td>
    </tr>
    <tr><td colspan="3" id='backcell'>
        <input type="submit" name="cancel" value="<?php print_string('backtogroups', 'group'); ?>" />
    </td></tr>
    </table>
    </div>
    </form>
</div>

<?php
    //outputs the JS array used to display the other groups users are in
    $potentialmembersselector->print_user_summaries($course->id);

    //this must be after calling display() on the selectors so their setup JS executes first
    $PAGE->requires->js_init_call('init_add_remove_members_page', null, false, $potentialmembersselector->get_js_module());

    echo $OUTPUT->footer();
