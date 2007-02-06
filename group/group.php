<?php
/**
 * Create group OR edit group settings.
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
require_once($CFG->libdir.'/uploadlib.php');

$success = true;
$err = array();

$courseid   = required_param('courseid', PARAM_INT);         
$groupingid = optional_param('grouping', false, PARAM_INT);
$groupid    = optional_param('group', false, PARAM_INT);

$groupsettings->name       = optional_param('name', PARAM_ALPHANUM);
$groupsettings->description= optional_param('description', PARAM_ALPHANUM);
$groupsettings->enrolmentkey= optional_param('enrolmentkey', PARAM_ALPHANUM);
$groupsettings->hidepicture= optional_param('hidepicture', PARAM_BOOL);

$delete = optional_param('delete', false, PARAM_BOOL);

// Get the course information so we can print the header and
// check the course id is valid
$course = groups_get_course_info($courseid);
if (! $course) {
    $success = false;
    print_error('invalidcourse'); //'The course ID is invalid'
}
if ($delete && !$groupid) {
    $success = false;
    print_error('errorinvalidgroup', 'group', groups_home_url($courseid));
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
            redirect(groups_home_url($courseid, $groupid, $groupingid, false));
        }
        elseif (isset($frm->confirmdelete)) {
            if ($success = groups_delete_group($groupid)) {
                redirect(groups_home_url($courseid, null, $groupingid, false));
            } else {
                print_error('erroreditgroup', 'group', groups_home_url($courseid));
            }
        }
        elseif (empty($frm->name)) {
            $err['name'] = get_string('missingname');
        }
        elseif (isset($frm->update)) {
            if (GROUP_NOT_IN_GROUPING == $groupingid) {
                print_error('errornotingrouping', 'group', groups_home_url($courseid), get_string('notingrouping', 'group'));
            }
            if (! $groupid) {
                $success = (bool)$groupid = groups_create_group($courseid); //$groupsettings);
                $success = groups_add_group_to_grouping($groupid, $groupingid);
            }
            if ($success) {
                //require_once($CFG->dirroot.'/lib/uploadlib.php');

                $um = new upload_manager('imagefile',false,false,null,false,0,true,true);
                if ($um->preprocess_files()) {
                    require_once("$CFG->libdir/gdlib.php");
                
                    if (save_profile_image($groupid, $um, 'groups')) {
                        $groupsettings->picture = 1;
                    } 
                }

                $success = (bool)groups_set_group_settings($groupid, $groupsettings);
            }
            if ($success) {
                redirect(groups_home_url($courseid, $groupid, $groupingid, false));
            }
            else {
                print_error('erroreditgroup', 'group', groups_home_url($courseid));
            }
        }
    }

/// OR, prepare the form.

    if ($groupid) {
        // Form to edit existing group.
        $group = groups_get_group_settings($groupid);
        if (! $group) {
            print_error('errorinvalidgroup', 'group', groups_home_url($courseid));
        }
        $strname = s($group->name);
        $strdesc = s($group->description);

        $strbutton  = get_string('save', 'group'); 
        $strheading = get_string('editgroupsettings', 'group');
    } else {
        // Form to create a new one.
        $strname = get_string('defaultgroupname', 'group');
        $strdesc = '';
        $strbutton = $strheading = get_string('creategroup', 'group');
    }
    $strgroups = get_string('groups');
    $strparticipants = get_string('participants');
    if ($delete) {
        $strheading = get_string('deleteselectedgroup', 'group');
    } //else { $strheader = get_string('groupinfoedit'); }

    $maxbytes = get_max_upload_file_size($CFG->maxbytes, $course->maxbytes);
    if (!empty($CFG->gdversion) and $maxbytes) {
        $printuploadpicture = true;
    } else {
        $printuploadpicture = false;
    }

/// Print the page and form

    print_header("$course->shortname: ". $strheading,
                 "$course->fullname", 
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
                 "-> $strgroups", '', '', true, '', user_login_string($course, $USER));

    $usehtmleditor = false;
?>
<h3 class="main"><?php echo $strheading ?></h3>

<form action="group.php" method="post" class="mform notmform" id="groupform">

<input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
<input type="hidden" name="courseid" value="<?php p($courseid); ?>" />
<input type="hidden" name="grouping" value="<?php p($groupingid); ?>" />
<?php
    if ($groupid) {
        echo '<input type="hidden" name="group" value="'. $groupid .'" />';
    }

    if ($delete) {
        /*echo 'Are you sure you want to delete group X ?';
        choose_from_menu_yesno('confirmdelete', false, '', true);*/
?>

        <p><?php print_string('deletegroupconfirm', 'group', $strname); ?></p>
        <input type="hidden" name="delete" value="1" />
        <input type="submit" name="confirmdelete" value="<?php print_string('yes'); ?>" />
        <input type="submit" name="cancel" value="<?php print_string('no'); ?>" />
<?php
    } else {
?>

<div class="fitem">
<p><label for="groupname"><?php
   print_string('groupname', 'group');
   if (isset($err['name'])) {
       echo' ';
       formerr($err['name']);
   } ?>&nbsp; </label></p>
<p class="felement"><input id="groupname" name="name" type="text" size="40" value="<?php echo $strname; ?>" /></p>
</div>

<p><label for="edit-description"><?php print_string('groupdescription', 'group'); ?>&nbsp;</label></p>
<p><?php print_textarea($usehtmleditor, 5, 45, 200, 400, 'description', $strdesc); ?></p>

<p><label for="enrolmentkey"><?php print_string('enrolmentkey', 'group'); ?>&nbsp;</label></p>
<p><input id="enrolmentkey" name="enrolmentkey" type="text" size="25" /></p>

<?php if ($printuploadpicture) {  ?>
    <p><label for="menuhidepicture"><?php print_string('hidepicture', 'group'); ?>&nbsp;</label></p>
    <p><?php $options = array();
             $options[0] = get_string('no');
             $options[1] = get_string('yes');
             choose_from_menu($options, 'hidepicture', isset($group)? $group->hidepicture: 1, '');?></p>

    <p><label ><?php /* for="groupicon" */ print_string('newpicture', 'group');
        helpbutton('picture', get_string('helppicture'));
        print_string('maxsize', '', display_size($maxbytes), 'group');
        if (isset($err['imagefile'])) formerr($err['imagefile']);
     ?>&nbsp;</label></p>
    <p><?php upload_print_form_fragment(1, array('groupicon'), null,false,null,0,0,false); ?></p>
<?php 
    }  
?>

<p class="fitem">
  <label for="id_submit">&nbsp;</label>
  <span class="f--element fsubmit">
    <input type="submit" name="update" id="id_submit" value="<?php echo $strbutton; ?>" />
    <input type="submit" name="cancel" value="<?php print_string('cancel', 'group'); ?>" />
  </span>
</p>

<?php } //IF($delete) ?>

<span class="clearer">&nbsp;</span>

</form>
<?php
    print_footer($course);
}

?>
