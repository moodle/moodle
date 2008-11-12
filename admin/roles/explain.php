<?php // $Id$
      // Script to assign users to contexts

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/user/selector/lib.php');

    $contextid = required_param('contextid',PARAM_INT);
    $contextuserid = optional_param('userid', 0, PARAM_INT); // needed for user tabs
    $courseid = optional_param('courseid', 0, PARAM_INT); // needed for user tabs

    if (! $context = get_context_instance_by_id($contextid)) {
        print_error('wrongcontextid', 'error');
    }
    $isfrontpage = $context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID;
    $contextname = print_context_name($context);

    if ($context->contextlevel == CONTEXT_COURSE) {
        $courseid = $context->instanceid;
        if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
            print_error('invalidcourse', 'error');
        }

    } else if (!empty($courseid)){ // we need this for user tabs in user context
        if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
            print_error('invalidcourse', 'error');
        }

    } else {
        $courseid = SITEID;
        $course = clone($SITE);
    }

/// Check login and permissions.
    require_login($course);
    $canview = has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride',
            'moodle/role:override', 'moodle/role:manage'), $context);
    if (!$canview) {
        print_error('nopermissions', 'error', '', get_string('explainpermissions', 'role'));
    }

/// These are needed early because of tabs.php
    $assignableroles = get_assignable_roles($context, ROLENAME_BOTH);
    $overridableroles = get_overridable_roles($context, ROLENAME_BOTH);

/// Get the user_selector we will need.
/// Teachers within a course just get to see the same list of people they can
/// assign roles to. Admins (people with moodle/role:manage) can run this report for any user.
    $options = array('context' => $context, 'roleid' => 0);
    if ($context->contextlevel > CONTEXT_COURSE && !is_inside_frontpage($context) && !has_capability('moodle/role:manage', $context)) {
        $userselector = new potential_assignees_below_course('reportuser', $options);
    } else {
        $userselector = new potential_assignees_course_and_above('reportuser', $options);
    }
    $userselector->set_multiselect(false);
    $userselector->set_rows(10);

/// Work out an appropriate page title.
    $title = get_string('explainpermissionsin', 'role', $contextname);
    $straction = get_string('explainpermissions', 'role'); // Used by tabs.php

/// Print the header and tabs
    if ($context->contextlevel == CONTEXT_USER) {
        $contextuser = $DB->get_record('user', array('id' => $contextuserid));
        $fullname = fullname($contextuser, has_capability('moodle/site:viewfullnames', $context));

        /// course header
        $navlinks = array();
        if ($courseid != SITEID) {
            if (has_capability('moodle/course:viewparticipants', get_context_instance(CONTEXT_COURSE, $courseid))) {
                $navlinks[] = array('name' => get_string('participants'), 'link' => "$CFG->wwwroot/user/index.php?id=$courseid", 'type' => 'misc');
            }
            $navlinks[] = array('name' => $fullname, 'link' => "$CFG->wwwroot/user/view.php?id=$contextuserid&amp;course=$courseid", 'type' => 'misc');
            $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
            $navigation = build_navigation($navlinks);

            print_header($title, $fullname, $navigation, '', '', true, '&nbsp;', navmenu($course));

        /// site header
        } else {
            $navlinks[] = array('name' => $fullname, 'link' => "$CFG->wwwroot/user/view.php?id=$contextuserid&amp;course=$courseid", 'type' => 'misc');
            $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
            $navigation = build_navigation($navlinks);
            print_header($title, $course->fullname, $navigation, "", "", true, "&nbsp;", navmenu($course));
        }

        $showroles = 1;
        $currenttab = 'explain';
        include_once($CFG->dirroot.'/user/tabs.php');

    } else if ($context->contextlevel == CONTEXT_SYSTEM) {
        admin_externalpage_setup('assignroles');
//        admin_externalpage_setup('explainpermissions');
        admin_externalpage_print_header();

    } else if ($context->contextlevel == CONTEXT_COURSE and $context->instanceid == SITEID) {
        admin_externalpage_setup('explainfrontpagepermissions');
        admin_externalpage_print_header();
        $currenttab = 'explain';
        include_once('tabs.php');

    } else {
        $currenttab = 'explain';
        include_once('tabs.php');
    }

/// Print heading.
    print_heading_with_help($title, 'explainpermissions');

/// If a user has been chosen, show all the permissions for this user.
    $user = $userselector->get_selected_user();
    if (!is_null($user)) {

    /// Class for rendering the table.
        class explain_cabability_table extends cabability_table_base {
            protected $user;
            protected $fullname;
            protected $baseurl;
            protected $stryes;
            protected $strno;
            protected $strexplanation;
            private $hascap;
            public function __construct($context, $user) {
                global $CFG;
                parent::__construct($context, 'explaincaps');
                $this->user = $user;
                $this->fullname = fullname($user);
                $this->baseurl = $CFG->wwwroot . '/' . $CFG->admin .
                        '/roles/explainhascapabiltiy.php?user=' . $user->id .
                        '&amp;contextid=' . $context->id . '&amp;capability=';
                $this->stryes = get_string('yes');
                $this->strno = get_string('no');
                $this->strexplanation = get_string('explanation');
            }
            protected function add_header_cells() {
                echo '<th>' . get_string('allowed', 'role') . '</th>';
                echo '<th>' . $this->strexplanation . '</th>';
            }
            protected function num_extra_columns() {
                return 2;
            }
            protected function skip_row($capability) {
                return $capability->name != 'moodle/site:doanything' && is_legacy($capability->name);
            }
            protected function get_row_classes($capability) {
                $this->hascap = has_capability($capability->name, $this->context, $this->user->id);
                if ($this->hascap) {
                    return array('yes');
                } else {
                    return array('no');
                }
            }
            protected function add_row_cells($capability) {
                if ($this->hascap) {
                    $result = $this->stryes;
                    $tooltip = 'explainwhyhascap';
                } else {
                    $result = $this->strno;
                    $tooltip = 'explainwhyhasnotcap';
                }
                $a = new stdClass;
                $a->username = $this->fullname;
                $a->capability = $capability->name;
                echo '<td>' . $result . '</td>';
                echo '<td>';
                link_to_popup_window($this->baseurl . $capability->name, 'hascapabilityexplanation',
                        $this->strexplanation, 600, 600, get_string($tooltip, 'role', $a));
                echo '</td>';
            }
        }

        require_js(array('yui_yahoo', 'yui_dom', 'yui_event'));
        require_js($CFG->admin . '/roles/roles.js');
        print_box_start('generalbox boxaligncenter');
        print_heading(get_string('permissionsforuser', 'role', fullname($user)), '', 3);

        $table = new explain_cabability_table($context, $user);
        $table->display();
        print_box_end();

        $selectheading = get_string('selectanotheruser', 'role');
    } else {
        $selectheading = get_string('selectauser', 'role');
    }

/// Show UI for choosing a user to report on.
    print_box_start('generalbox boxwidthnormal boxaligncenter', 'chooseuser');
    echo '<form method="get" action="' . $CFG->wwwroot . '/' . $CFG->admin . '/roles/explain.php" >';

/// Hidden fields.
    echo '<input type="hidden" name="contextid" value="' . $context->id . '" />';
    if (!empty($contextuserid)) {
        echo '<input type="hidden" name="userid" value="' . $contextuserid . '" />';
    }
    if ($courseid && $courseid != SITEID) {
        echo '<input type="hidden" name="courseid" value="' . $courseid . '" />';
    }

/// User selector.
    print_heading('<label for="reportuser">' . $selectheading . '</label>', '', 3);
    $userselector->display(); 

/// Submit button and the end of the form.
    echo '<p id="chooseusersubmit"><input type="submit" value="' . get_string('showthisuserspermissions', 'role') . '" /></p>';
    echo '</form>';
    print_box_end();

/// Appropriate back link.
    if (!$isfrontpage && ($url = get_context_url($context))) {
        echo '<div class="backlink"><a href="' . $url . '">' .
            get_string('backto', '', $contextname) . '</a></div>';
    }

    print_footer($course);
?>
