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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

class search extends \moodleform {

    /**
     * Form definition.
     *
     * @return void
     */
    function definition() {
        global $CFG;

        $mform =& $this->_form;
        $mform->disable_form_change_checker();
        $mform->addElement('header', 'search', get_string('search', 'search'));

        // Help info depends on the selected search engine.
        $mform->addElement('text', 'q', get_string('enteryoursearchquery', 'search'));
        $mform->addHelpButton('q', 'searchinfo', $this->_customdata['searchengine']);
        $mform->setType('q', PARAM_TEXT);
        $mform->addRule('q', get_string('required'), 'required', null, 'client');

        $mform->addElement('header', 'filtersection', get_string('filterheader', 'search'));
        $mform->setExpanded('filtersection', false);

        $mform->addElement('text', 'title', get_string('title', 'search'));
        $mform->setType('title', PARAM_TEXT);

        $search = \core_search\manager::instance();

        $searchareas = \core_search\manager::get_search_areas_list(true);
        $areanames = array();
        foreach ($searchareas as $areaid => $searcharea) {
            $areanames[$areaid] = $searcharea->get_visible_name();
        }
        $options = array(
            'multiple' => true,
            'noselectionstring' => get_string('allareas', 'search'),
        );
        $mform->addElement('autocomplete', 'areaids', get_string('searcharea', 'search'), $areanames, $options);

        $options = array(
            'multiple' => true,
            'limittoenrolled' => !is_siteadmin(),
            'noselectionstring' => get_string('allcourses', 'search'),
        );
        $mform->addElement('course', 'courseids', get_string('courses', 'core'), $options);
        $mform->setType('courseids', PARAM_INT);

        $mform->addElement('date_time_selector', 'timestart', get_string('fromtime', 'search'), array('optional' => true));
        $mform->setDefault('timestart', 0);

        $mform->addElement('date_time_selector', 'timeend', get_string('totime', 'search'), array('optional' => true));
        $mform->setDefault('timeend', 0);

        $this->add_action_buttons(false, get_string('search', 'search'));
    }
}
