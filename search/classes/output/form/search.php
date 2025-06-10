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
 * Global search search form definition
 *
 * @package   core_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search\output\form;

use core_search\manager;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/externallib.php');

class search extends \moodleform {

    /**
     * Form definition.
     *
     * @return void
     */
    function definition() {
        global $USER, $DB, $OUTPUT;

        $mform =& $this->_form;

        if (\core_search\manager::is_search_area_categories_enabled() && !empty($this->_customdata['cat'])) {
            $mform->addElement('hidden', 'cat');
            $mform->setType('cat', PARAM_NOTAGS);
            $mform->setDefault('cat', $this->_customdata['cat']);
        }

        $mform->disable_form_change_checker();
        $mform->addElement('header', 'search', get_string('search', 'search'));

        // Help info depends on the selected search engine.
        $mform->addElement('text', 'q', get_string('enteryoursearchquery', 'search'));
        $mform->addHelpButton('q', 'searchinfo', $this->_customdata['searchengine']);
        $mform->setType('q', PARAM_TEXT);
        $mform->addRule('q', get_string('required'), 'required', null, 'client');

        // Show the 'search within' option if the user came from a particular context.
        if (!empty($this->_customdata['searchwithin'])) {
            $mform->addElement('select', 'searchwithin', get_string('searchwithin', 'search'),
                    $this->_customdata['searchwithin']);
            $mform->setDefault('searchwithin', '');
        }

        // If the search engine provides multiple ways to order results, show options.
        if (!empty($this->_customdata['orderoptions']) &&
                count($this->_customdata['orderoptions']) > 1) {

            $mform->addElement('select', 'order', get_string('order', 'search'),
                    $this->_customdata['orderoptions']);
            $mform->setDefault('order', 'relevance');
        }

        $mform->addElement('header', 'filtersection', get_string('filterheader', 'search'));
        $mform->setExpanded('filtersection', false);

        $mform->addElement('text', 'title', get_string('title', 'search'));
        $mform->setType('title', PARAM_TEXT);

        $search = \core_search\manager::instance(true);
        $enabledsearchareas = \core_search\manager::get_search_areas_list(true);
        $areanames = array();

        if (\core_search\manager::is_search_area_categories_enabled() && !empty($this->_customdata['cat'])) {
            $searchareacategory = \core_search\manager::get_search_area_category_by_name($this->_customdata['cat']);
            $searchareas = $searchareacategory->get_areas();
            foreach ($searchareas as $areaid => $searcharea) {
                if (key_exists($areaid, $enabledsearchareas)) {
                    $areanames[$areaid] = $searcharea->get_visible_name();
                }
            }
        } else {
            foreach ($enabledsearchareas as $areaid => $searcharea) {
                $areanames[$areaid] = $searcharea->get_visible_name();
            }
        }

        // Sort the array by the text.
        \core_collator::asort($areanames);

        $options = array(
            'multiple' => true,
            'noselectionstring' => get_string('allareas', 'search'),
        );
        $mform->addElement('autocomplete', 'areaids', get_string('searcharea', 'search'), $areanames, $options);

        if (is_siteadmin()) {
            $limittoenrolled = false;
        } else {
            $limittoenrolled = !manager::include_all_courses();
        }

        $options = array(
            'multiple' => true,
            'limittoenrolled' => $limittoenrolled,
            'noselectionstring' => get_string('allcourses', 'search'),
        );
        $mform->addElement('course', 'courseids', get_string('courses', 'core'), $options);
        $mform->setType('courseids', PARAM_INT);

        if (manager::include_all_courses() || !empty(get_config('core', 'searchallavailablecourses'))) {
            $mform->addElement('checkbox', 'mycoursesonly', get_string('mycoursesonly', 'search'));
            $mform->setType('mycoursesonly', PARAM_INT);
        }

        // If the search engine can search by user, and the user is logged in (so we have
        // permission to call the user-listing web service) then show the user selector.
        if ($search->get_engine()->supports_users() && isloggedin()) {
            $options = [
                'ajax' => 'core_search/form-search-user-selector',
                'multiple' => true,
                'noselectionstring' => get_string('allusers', 'search'),
                'valuehtmlcallback' => function($value) {
                    global $DB, $OUTPUT;
                    $user = $DB->get_record('user', ['id' => (int)$value], '*', IGNORE_MISSING);
                    if (!$user || !user_can_view_profile($user)) {
                        return false;
                    }
                    $details = user_get_user_details($user);
                    return $OUTPUT->render_from_template(
                            'core_search/form-user-selector-suggestion', $details);
                }
            ];
            if (!empty($this->_customdata['withincourseid'])) {
                $options['withincourseid'] = $this->_customdata['withincourseid'];
            }

            $mform->addElement('autocomplete', 'userids', get_string('users'), [], $options);
        }

        if (!empty($this->_customdata['searchwithin'])) {
            // Course options should be hidden if we choose to search within a specific location.
            $mform->hideIf('courseids', 'searchwithin', 'ne', '');

            // Get groups on course (we don't show group selector if there aren't any).
            $courseid = $this->_customdata['withincourseid'];
            $allgroups = groups_get_all_groups($courseid);
            if ($allgroups && $search->get_engine()->supports_group_filtering()) {
                $groupnames = [];
                foreach ($allgroups as $group) {
                    $groupnames[$group->id] = $group->name;
                }

                // Create group autocomplete option.
                $options = array(
                        'multiple' => true,
                        'noselectionstring' => get_string('allgroups'),
                );
                $mform->addElement('autocomplete', 'groupids', get_string('groups'), $groupnames, $options);

                // Is the second 'search within' option a cm?
                if (!empty($this->_customdata['withincmid'])) {
                    // Find out if the cm supports groups.
                    $modinfo = get_fast_modinfo($courseid);
                    $cm = $modinfo->get_cm($this->_customdata['withincmid']);
                    if ($cm->effectivegroupmode != NOGROUPS) {
                        // If it does, group ids are available when you have course or module selected.
                        $mform->hideIf('groupids', 'searchwithin', 'eq', '');
                    } else {
                        // Group ids are only available if you have course selected.
                        $mform->hideIf('groupids', 'searchwithin', 'ne', 'course');
                    }
                } else {
                    $mform->hideIf('groupids', 'searchwithin', 'eq', '');
                }
            }
        }

        $mform->addElement('date_time_selector', 'timestart', get_string('fromtime', 'search'), array('optional' => true));
        $mform->setDefault('timestart', 0);

        $mform->addElement('date_time_selector', 'timeend', get_string('totime', 'search'), array('optional' => true));
        $mform->setDefault('timeend', 0);

        // Source context i.e. the page they came from when they clicked search.
        $mform->addElement('hidden', 'context');
        $mform->setType('context', PARAM_INT);

        $this->add_action_buttons(false, get_string('search', 'search'));
    }
}
