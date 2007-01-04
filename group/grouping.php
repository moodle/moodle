<?php
/**
 * Create grouping OR edit grouping settings.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @author J.White AT open.ac.uk 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once('../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/moodlelib.php');

$success = true;
 
$courseid   = required_param('courseid', PARAM_INT);         
$groupingid = optional_param('grouping', false, PARAM_INT);

$groupingsettings->name       = optional_param('name', PARAM_ALPHANUM);
$groupingsettings->description= optional_param('description', PARAM_ALPHANUM);

// Get the course information so we can print the header and
// check the course id is valid
$course = groups_get_course_info($courseid);
if (! $course) {
    $success = false;
    print_error('The course ID is invalid');
}

if ($success) {
    // Make sure that the user has permissions to manage groups.
    require_login($courseid);

    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    if (! has_capability('moodle/course:managegroups', $context)) {
        redirect();
    }
    
/// If data submitted, then process and store.

    if ($frm = data_submitted() and confirm_sesskey()) { 

        if (isset($frm->cancel)) {
            redirect(groups_home_url($courseid, null, $groupingid, false));
        }
        elseif (empty($frm->name)) {
            $err['name'] = get_string('missingname');
        }
        elseif (isset($frm->update)) {
        
            if ($groupingid) {
                $success = (bool)groups_set_grouping_settings($groupingid, $groupingsettings);
            }
            else {
                $success = (bool)$groupingid = groups_create_grouping($courseid, $groupingsettings);
            }
            if ($success) {
                redirect(groups_home_url($courseid, null, $groupingid, false));
            }
            else {
                print_error('Error creating/updating grouping.');
            }
        }
    }

/// OR, prepare the form.

    if ($groupingid) {
        // Form to edit existing grouping.
        $grouping = groups_get_grouping_settings($groupingid);
        if (! $grouping) {
            print_error('errorinvalidgrouping', 'group', groups_home_url($courseid));
        }
        $strname = s($grouping->name);
        $strdesc = s($grouping->description);

        $strbutton  = get_string('save', 'group'); 
        $strheading = get_string('editgroupingsettings', 'group');
    } else {
        // Form to create a new one.
        $strname = get_string('defaultgroupingname', 'group');
        $strdesc = '';
        $strbutton = $strheading = get_string('creategrouping', 'group');
    }
    $strgroups = get_string('groups');
    $strparticipants = get_string('participants');

/// Print the page and form

    print_header("$course->shortname: $strgroups", 
                 "$course->fullname", 
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
                 "-> $strgroups", '', '', true, '', user_login_string($course, $USER));

    $usehtmleditor = false;
?>
<h3 class="main"><?php echo $strheading ?></h3>

<form action="grouping.php" method="post" class="mform notmform" id="groupingform">
<input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
<input type="hidden" name="courseid" value="<?php p($courseid); ?>" />
<?php
    if ($groupingid) {
        echo '<input type="hidden" name="grouping" value="'. $groupingid .'" />';
    }
?>

<div class="f-item">
<p><label for="groupingname"><?php print_string('groupingname', 'group'); ?>&nbsp;</label></p>
<p><input id="groupingname" name="name" type="text" size="40" value="<?php echo $strname; ?>" /></p>
</div>

<p><label for="edit-description"><?php print_string('groupingdescription', 'group'); ?>&nbsp;</label></p>
<p><?php print_textarea($usehtmleditor, 5, 45, 200, 400, 'description', $strdesc); ?></p>

<p class="fitem">
  <label for="id_submit">&nbsp;</label>
  <span class="f-element fsubmit">
    <input type="submit" name="update" id="id_submit" value="<?php echo $strbutton; ?>" />
    <input type="submit" name="cancel" value="<?php print_string('cancel', 'group'); ?>" />
  </span>
</p>
<span class="clearer">&nbsp;</span>

</form>
<?php
    print_footer($course);
}

?>
