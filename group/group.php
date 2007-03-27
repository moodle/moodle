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
$newgrouping= optional_param('newgrouping', false, PARAM_INT);
$groupid    = optional_param('group', false, PARAM_INT);

$groupsettings->name       = optional_param('name', false, PARAM_TEXT);
$groupsettings->description= optional_param('description', '', PARAM_TEXT);
$groupsettings->enrolmentkey= optional_param('enrolmentkey', '', PARAM_TEXT);
$groupsettings->hidepicture= optional_param('hidepicture', true, PARAM_BOOL);

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
            if (! $groupid) { //OK, new group.
                // Allow groups to be created outside of groupings
                /*
                if (GROUP_NOT_IN_GROUPING == $groupingid) {
                    print_error('errornotingrouping', 'group', groups_home_url($courseid), get_string('notingrouping', 'group'));
                }
                */
                $success = (bool)$groupid = groups_create_group($courseid); //$groupsettings);
                
            }
            elseif ($groupingid != $newgrouping) { //OK, move group.
                // Allow groups to be created outside of groupings
                /*
                if (GROUP_NOT_IN_GROUPING == $newgrouping) {
                    print_error('errornotingrouping', 'group', groups_home_url($courseid), get_string('notingrouping', 'group'));
                }
                */
                if ($groupingid != GROUP_NOT_IN_GROUPING) {
                    $success = $success && groups_remove_group_from_grouping($groupid, $groupingid);
                }

                $success = $success && groups_add_group_to_grouping($groupid, $newgrouping);
            }
            if ($success) {
                //require_once($CFG->dirroot.'/lib/uploadlib.php');

                $um = new upload_manager('imagefile',false,false,$course=null,false,$modbytes=0,$silent=false,$allownull=true);
                if ($um->preprocess_files()) {
                    require_once("$CFG->libdir/gdlib.php");

                    if (save_profile_image($groupid, $um, 'groups')) {
                        $groupsettings->picture = 1;
                    }
                } else {
                    $success = false;
                }
            }
            $success = $success && groups_set_group_settings($groupid, $groupsettings);

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
                 $course->fullname, 
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
                 "-> $strgroups", '', '', true, '', user_login_string($course, $USER));

    $usehtmleditor = false;

    echo '<h3 class="main">' . $strheading . '</h3>'."\n";

    echo '<form action="group.php" method="post" enctype="multipart/form-data" class="mform notmform" id="groupform">'."\n";
    echo '<div>'."\n";
    echo '<input type="hidden" name="sesskey" value="' . s(sesskey()) . '" />'."\n";
    echo '<input type="hidden" name="courseid" value="' . s($courseid) . '" />'."\n";
    echo '<input type="hidden" name="grouping" value="' . s($groupingid) . '" />'."\n";

    if ($groupid) {
        echo '<input type="hidden" name="group" value="'. $groupid .'" />'."\n";
    }

    if ($delete) {
        /*echo 'Are you sure you want to delete group X ?';
        choose_from_menu_yesno('confirmdelete', false, '', true);*/

        echo '<p>' . get_string('deletegroupconfirm', 'group', $strname) . '</p>'."\n";
        echo '<input type="hidden" name="delete" value="1" />'."\n";
        echo '<input type="submit" name="confirmdelete" value="' . get_string('yes') . '" />'."\n";
        echo '<input type="submit" name="cancel" value="' . get_string('no') . '" />'."\n";
    } else {
        echo '<div class="fitem">'."\n";
        echo '<p><label for="groupname">' . get_string('groupname', 'group');
        
        if (isset($err['name'])) {
            echo ' ';
            formerr($err['name']);
        } 
        
        echo '&nbsp; </label></p>'."\n";
        echo '<p class="felement"><input id="groupname" name="name" type="text" size="40" value="' . $strname . '" /></p>'."\n";
        echo '</div>'."\n";

        echo '<p><label for="edit-description">' . get_string('groupdescription', 'group') . '&nbsp;</label></p>'."\n";
        echo '<p>' . print_textarea($usehtmleditor, 5, 45, 200, 400, 'description', $strdesc, 0, true) . '</p>'."\n";

        echo '<p><label for="enrolmentkey">' . get_string('enrolmentkey', 'group') . '&nbsp;</label></p>'."\n";
        echo '<p><input id="enrolmentkey" name="enrolmentkey" type="text" size="25" /></p>'."\n";

        if ($printuploadpicture) {
            echo '<p><label for="menuhidepicture">' . get_string('hidepicture', 'group') . '&nbsp;</label></p>'."\n";
            echo '<p>';
            $options = array();
            $options[0] = get_string('no');
            $options[1] = get_string('yes');
            choose_from_menu($options, 'hidepicture', isset($group)? $group->hidepicture: 1, '');
            echo '</p>'."\n";

            echo '<p><label >' . get_string('newpicture', 'group');
            helpbutton('picture', get_string('helppicture'));
            echo get_string('maxsize', '', display_size($maxbytes), 'group');

            if (isset($err['imagefile'])) {
                formerr($err['imagefile']);
            }

            echo '&nbsp;</label></p>'."\n";
            echo '<p>' . upload_print_form_fragment(1, array('imagefile'), null,false,null,0,0,true) . '</p>'."\n";
        }

        if ($groupid) { //OK, editing - option to move grouping.

            echo '<p><label for="groupings">' . get_string('addgroupstogrouping', 'group'). '</label></p>'."\n";
            echo '<select name="newgrouping" id="groupings" class="select">'."\n";

            $groupingids = groups_get_groupings($courseid);
            if (GROUP_NOT_IN_GROUPING == $groupingid) {
                $groupingids[] = GROUP_NOT_IN_GROUPING;
            }
            if ($groupingids) {    
                // Put the groupings into a hash and sort them
                foreach($groupingids as $id) {
                    $listgroupings[$id] = groups_get_grouping_displayname($id, $courseid);
                }
                natcasesort($listgroupings);
                
                // Print out the HTML
                $count = 1;
                foreach($listgroupings as $id => $name) {
                    $select = '';
                    if ($groupingid == $id) {
                        $select = ' selected="selected"';
                    }
                    echo "<option value=\"$id\"$select>$name</option>\n";
                    $count++;
                }
            }

            echo '</select>'."\n";
        } //IF($groupid)

        echo '<p class="fitem">'."\n";
        echo '<label for="id_submit">&nbsp;</label>'."\n";
        echo '<span class="f--element fsubmit">'."\n";
        echo '<input type="submit" name="update" id="id_submit" value="' . $strbutton . '" />'."\n";
        echo '<input type="submit" name="cancel" value="' . get_string('cancel', 'group') . '" />'."\n";
        echo '</span>'."\n";
        echo '</p>'."\n";

    } //IF($delete)

    echo '<span class="clearer">&nbsp;</span>'."\n";
    echo '</div>';
    echo '</form>'."\n";

    print_footer($course);
}

?>
