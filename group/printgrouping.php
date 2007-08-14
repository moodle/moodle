<?php
/**
 * Print groups in groupings, and members of groups.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once('../config.php');
require_once('lib.php');

//TODO: fix me
die;die;die;
$success = true;

$courseid   = required_param('courseid', PARAM_INT);
$groupingid = required_param('groupingid', PARAM_INT);

// Get the course information so we can print the header and
// check the course id is valid
$course = groups_get_course_info($courseid);
if (! $course) {
    $success = false;
    print_error('invalidcourse');
}


if ($success) {
    // Make sure that the user has permissions to manage groups.
    require_login($courseid);

    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    if (! has_capability('moodle/course:managegroups', $context)) {
        redirect();
    }

    //( confirm_sesskey checks that this is a POST request.)

    // Print the page and form
    $strgroups = get_string('groups');
    $strparticipants = get_string('participants');
    print_header("$course->shortname: $strgroups", $course->fullname,
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
                 "-> <a href=\"$CFG->wwwroot/group/index.php?id=$courseid\">$strgroups</a>".
                 "-> ".get_string('printerfriendly', 'group'), "", "", true, '', user_login_string($course, $USER));

    $groupingname = groups_get_grouping_name($groupingid);
    if (! $groupingname) {
        print_error('errorinvalidgrouping', 'group', groups_home_url($courseid));
    } else {
       // Print the name of the grouping
        if (!empty($CFG->enablegroupings)) {
            // NO GROUPINGS YET!
           echo "<h1>$groupingname</h1>\n";
        }
    }

    // Get the groups and group members for the grouping.
    if (GROUP_NOT_IN_GROUPING == $groupingid) {
        $groupids = groups_get_groups_not_in_any_grouping($courseid);
    } else {
        $groupids = groups_get_groups_in_grouping($groupingid);
    }

    if ($groupids) {
        // Make sure the groups are in the right order
        $group_names = groups_groupids_to_group_names($groupids);

        // Go through each group in turn and print the group name and then the members
        foreach ($group_names as $group) {

            echo "<h2>{$group->name}</h2>\n";
            $userids = groups_get_members($group->id);
            if ($userids != false) {
                // Make sure the users are in the right order
                $user_names = groups_userids_to_user_names($userids, $courseid);

                echo "<ol>\n";
                foreach ($user_names as $user) {

                    echo "<li>{$user->name}</li>\n";
                }
                echo "</ol>\n";
            }
        }
    }

    print_footer($course);
}

?>
