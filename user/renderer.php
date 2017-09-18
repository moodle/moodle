<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Provides user rendering functionality such as printing private files tree and displaying a search utility
 *
 * @package    core_user
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Provides user rendering functionality such as printing private files tree and displaying a search utility
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_user_renderer extends plugin_renderer_base {

    /**
     * Prints user files tree view
     * @return string
     */
    public function user_files_tree() {
        return $this->render(new user_files_tree);
    }

    /**
     * Render user files tree
     *
     * @param user_files_tree $tree
     * @return string HTML
     */
    public function render_user_files_tree(user_files_tree $tree) {
        if (empty($tree->dir['subdirs']) && empty($tree->dir['files'])) {
            $html = $this->output->box(get_string('nofilesavailable', 'repository'));
        } else {
            $htmlid = 'user_files_tree_'.uniqid();
            $module = array('name' => 'core_user', 'fullpath' => '/user/module.js');
            $this->page->requires->js_init_call('M.core_user.init_tree', array(false, $htmlid), false, $module);
            $html = '<div id="'.$htmlid.'">';
            $html .= $this->htmllize_tree($tree, $tree->dir);
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     * @param user_files_tree $tree
     * @param array $dir
     * @return string HTML
     */
    protected function htmllize_tree($tree, $dir) {
        global $CFG;
        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $image = $this->output->pix_icon(file_folder_icon(), $subdir['dirname'], 'moodle', array('class' => 'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.s($subdir['dirname']).'</div> '.
                $this->htmllize_tree($tree, $subdir).'</li>';
        }
        foreach ($dir['files'] as $file) {
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'.$tree->context->id.'/user/private'.
                $file->get_filepath().$file->get_filename(), true);
            $filename = $file->get_filename();
            $image = $this->output->pix_icon(file_file_icon($file), $filename, 'moodle', array('class' => 'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.html_writer::link($url, $filename).
                '</div></li>';
        }
        $result .= '</ul>';

        return $result;
    }

    /**
     * Prints user search utility that can search user by first initial of firstname and/or first initial of lastname
     * Prints a header with a title and the number of users found within that subset
     * @param string $url the url to return to, complete with any parameters needed for the return
     * @param string $firstinitial the first initial of the firstname
     * @param string $lastinitial the first initial of the lastname
     * @param int $usercount the amount of users meeting the search criteria
     * @param int $totalcount the amount of users of the set/subset being searched
     * @param string $heading heading of the subset being searched, default is All Participants
     * @return string html output
     */
    public function user_search($url, $firstinitial, $lastinitial, $usercount, $totalcount, $heading = null) {
        global $OUTPUT;

        if ($firstinitial !== 'all') {
            set_user_preference('ifirst', $firstinitial);
        }
        if ($lastinitial !== 'all') {
            set_user_preference('ilast', $lastinitial);
        }

        if (!isset($heading)) {
            $heading = get_string('allparticipants');
        }

        $content = html_writer::start_tag('form', array('action' => new moodle_url($url)));
        $content .= html_writer::start_tag('div');

        // Search utility heading.
        $content .= $OUTPUT->heading($heading.get_string('labelsep', 'langconfig').$usercount.'/'.$totalcount, 3);

        // Initials bar.
        $prefixfirst = 'sifirst';
        $prefixlast = 'silast';
        $content .= $OUTPUT->initials_bar($firstinitial, 'firstinitial', get_string('firstname'), $prefixfirst, $url);
        $content .= $OUTPUT->initials_bar($lastinitial, 'lastinitial', get_string('lastname'), $prefixlast, $url);

        $content .= html_writer::end_tag('div');
        $content .= html_writer::tag('div', '&nbsp;');
        $content .= html_writer::end_tag('form');

        return $content;
    }

    /**
     * Displays the list of tagged users
     *
     * @param array $userlist
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @return string
     */
    public function user_list($userlist, $exclusivemode) {
        $tagfeed = new core_tag\output\tagfeed();
        foreach ($userlist as $user) {
            $userpicture = $this->output->user_picture($user, array('size' => $exclusivemode ? 100 : 35));
            $fullname = fullname($user);
            if (user_can_view_profile($user)) {
                $profilelink = new moodle_url('/user/view.php', array('id' => $user->id));
                $fullname = html_writer::link($profilelink, $fullname);
            }
            $tagfeed->add($userpicture, $fullname);
        }

        $items = $tagfeed->export_for_template($this->output);

        if ($exclusivemode) {
            $output = '<div><ul class="inline-list">';
            foreach ($items['items'] as $item) {
                $output .= '<li><div class="user-box">'. $item['img'] . $item['heading'] ."</div></li>\n";
            }
            $output .= "</ul></div>\n";
            return $output;
        }

        return $this->output->render_from_template('core_tag/tagfeed', $items);
    }

    /**
     * Renders the unified filter element for the course participants page.
     *
     * @param stdClass $course The course object.
     * @param context $context The context object.
     * @param array $filtersapplied Array of currently applied filters.
     * @return bool|string
     */
    public function unified_filter($course, $context, $filtersapplied) {
        global $CFG, $DB, $USER;

        $filteroptions = [];
        $isfrontpage = ($course->id == SITEID);

        // Get the list of fields we have to hide.
        $hiddenfields = array();
        if (!has_capability('moodle/course:viewhiddenuserfields', $context)) {
            $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
        }
        $haslastaccess = !isset($hiddenfields['lastaccess']);
        // Filter options for last access.
        if ($haslastaccess) {
            // Get minimum lastaccess for this course and display a dropbox to filter by lastaccess going back this far.
            // We need to make it diferently for normal courses and site course.
            if (!$isfrontpage) {
                $params = ['courseid' => $course->id, 'timeaccess' => 0];
                $select = 'courseid = :courseid AND timeaccess != :timeaccess';
                $minlastaccess = $DB->get_field_select('user_lastaccess', 'MIN(timeaccess)', $select, $params);
                $lastaccess0exists = $DB->record_exists('user_lastaccess', $params);
            } else {
                $params = ['lastaccess' => 0];
                $select = 'lastaccess != :lastaccess';
                $minlastaccess = $DB->get_field_select('user', 'MIN(lastaccess)', $select, $params);
                $lastaccess0exists = $DB->record_exists('user', $params);
            }
            $now = usergetmidnight(time());
            $timeoptions = [];
            $criteria = get_string('usersnoaccesssince');

            // Days.
            for ($i = 1; $i < 7; $i++) {
                $timestamp = strtotime('-' . $i . ' days', $now);
                if ($timestamp >= $minlastaccess) {
                    $value = get_string('numdays', 'moodle', $i);
                    $timeoptions += $this->format_filter_option(USER_FILTER_LAST_ACCESS, $criteria, $timestamp, $value);
                }
            }
            // Weeks.
            for ($i = 1; $i < 10; $i++) {
                $timestamp = strtotime('-'.$i.' weeks', $now);
                if ($timestamp >= $minlastaccess) {
                    $value = get_string('numweeks', 'moodle', $i);
                    $timeoptions += $this->format_filter_option(USER_FILTER_LAST_ACCESS, $criteria, $timestamp, $value);
                }
            }
            // Months.
            for ($i = 2; $i < 12; $i++) {
                $timestamp = strtotime('-'.$i.' months', $now);
                if ($timestamp >= $minlastaccess) {
                    $value = get_string('nummonths', 'moodle', $i);
                    $timeoptions += $this->format_filter_option(USER_FILTER_LAST_ACCESS, $criteria, $timestamp, $value);
                }
            }
            // Try a year.
            $timestamp = strtotime('-'.$i.' year', $now);
            if ($timestamp >= $minlastaccess) {
                $value = get_string('lastyear', 'moodle');
                $timeoptions += $this->format_filter_option(USER_FILTER_LAST_ACCESS, $criteria, $timestamp, $value);
            }
            if (!empty($lastaccess0exists)) {
                $value = get_string('never', 'moodle');
                $timeoptions += $this->format_filter_option(USER_FILTER_LAST_ACCESS, $criteria, $timestamp, $value);
            }
            if (count($timeoptions) > 1) {
                $filteroptions += $timeoptions;
            }
        }

        require_once($CFG->dirroot . '/enrol/locallib.php');
        $manager = new course_enrolment_manager($this->page, $course);

        $canreviewenrol = has_capability('moodle/course:enrolreview', $context);

        // Filter options for enrolment methods.
        if ($canreviewenrol && $enrolmentmethods = $manager->get_enrolment_instance_names(true)) {
            $criteria = get_string('enrolmentinstances', 'enrol');
            $enroloptions = [];
            foreach ($enrolmentmethods as $id => $enrolname) {
                $enroloptions += $this->format_filter_option(USER_FILTER_ENROLMENT, $criteria, $id, $enrolname);
            }
            $filteroptions += $enroloptions;
        }

        // Filter options for groups, if available.
        if ($course->groupmode != NOGROUPS) {
            if (has_capability('moodle/site:accessallgroups', $context) || $course->groupmode == VISIBLEGROUPS) {
                // List all groups if the user can access all groups, or we are in visible group mode.
                $groups = $manager->get_all_groups();
            } else {
                // Otherwise, just list the groups the user belongs to.
                $groups = groups_get_all_groups($course->id, $USER->id);
            }
            $criteria = get_string('group');
            $groupoptions = [];
            foreach ($groups as $id => $group) {
                $groupoptions += $this->format_filter_option(USER_FILTER_GROUP, $criteria, $id, $group->name);
            }
            $filteroptions += $groupoptions;
        }

        // Filter options for role.
        $roles = role_fix_names(get_profile_roles($context), $context, ROLENAME_ALIAS, true);
        $criteria = get_string('role');
        $roleoptions = [];
        foreach ($roles as $id => $role) {
            $roleoptions += $this->format_filter_option(USER_FILTER_ROLE, $criteria, $id, $role);
        }
        $filteroptions += $roleoptions;

        // Filter options for status.
        if ($canreviewenrol) {
            $criteria = get_string('status');
            // Add statuses.
            $filteroptions += $this->format_filter_option(USER_FILTER_STATUS, $criteria, ENROL_USER_ACTIVE, get_string('active'));
            $filteroptions += $this->format_filter_option(USER_FILTER_STATUS, $criteria, ENROL_USER_SUSPENDED,
                get_string('inactive'));
        }

        $indexpage = new \core_user\output\unified_filter($filteroptions, $filtersapplied);
        $context = $indexpage->export_for_template($this->output);

        return $this->output->render_from_template('core_user/unified_filter', $context);
    }

    /**
     * Returns a formatted filter option.
     *
     * @param int $filtertype The filter type (e.g. status, role, group, enrolment, last access).
     * @param string $criteria The string label of the filter type.
     * @param int $value The value for the filter option.
     * @param string $label The string representation of the filter option's value.
     * @return array The formatted option with the ['filtertype:value' => 'criteria: label'] format.
     */
    protected function format_filter_option($filtertype, $criteria, $value, $label) {
        $optionlabel = get_string('filteroption', 'moodle', (object)['criteria' => $criteria, 'value' => $label]);
        $optionvalue = "$filtertype:$value";
        return [$optionvalue => $optionlabel];
    }
}

/**
 * User files tree
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_files_tree implements renderable {

    /**
     * @var context_user $context
     */
    public $context;

    /**
     * @var array $dir
     */
    public $dir;

    /**
     * Create user files tree object
     */
    public function __construct() {
        global $USER;
        $this->context = context_user::instance($USER->id);
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, 'user', 'private', 0);
    }
}
