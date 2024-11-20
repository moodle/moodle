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
 * @package   local_iomad
 * @copyright 2024 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad\forms;

defined('MOODLE_INTERNAL') || die;

use \iomad;
use \company;
use \moodle_url;
use \moodleform;

/**
 * Course search form used on the IOMAD pages.
 *
 */
class course_search_form extends moodleform {
    protected $params = [];
    protected $customfields = [];

    public function __construct($url, $params, $title = "") {
        global $DB;
        $this->params = $params;

        $this->customfields = $DB->get_records_sql("SELECT cff.* FROM
                                                    {customfield_field} cff 
                                                    JOIN {customfield_category} cfc ON (cff.categoryid = cfc.id)
                                                    WHERE cfc.area = 'course'
                                                    AND cfc.component = 'core_course'
                                                    ORDER BY cfc.sortorder, cff.sortorder");
        $this->title = get_string("coursesearchfields", 'local_iomad');
        if ($title != "") {
            $this->title = $title;
        }
        parent::__construct();
    }

    public function definition() {
        global $CFG, $DB, $USER, $SESSION;

        $mform =& $this->_form;
        $mform->addElement('header', 'searchcourses', $this->title);
        $mform->setExpanded('searchcourses', false);

        foreach ($this->params as $param => $value) {
            if ($param == 'coursesearch' || str_contains($param, 'customfield_')) {
                continue;
            }
            $mform->addElement('hidden', $param, $value);
            $mform->setType($param, PARAM_CLEAN);
        }

        $mform->addElement('text', 'coursesearch', get_string('name'));
        $mform->setType('coursesearch', PARAM_CLEAN);

        // Add custom fields to the form.
        foreach ($this->customfields as $field) {
            $attributes = (array) json_decode($field->configdata);
            $attributes['required'] = false;
            switch ($field->type) {
                case "text":
                    $mform->addElement($field->type, 'customfield_' . $field->shortname, format_text($field->name));
                    $mform->setType('customfield_' . $field->shortname, PARAM_CLEAN);
                    break;
                case "textarea":
                    $mform->addElement('text', 'customfield_' . $field->shortname, format_text($field->name));
                    $mform->setType('customfield_' . $field->shortname, PARAM_CLEAN);
                    break;
                case "checkbox":
                    $mform->addElement('advcheckbox', 'customfield_' . $field->shortname, format_text($field->name));
                    break;
                case "date":
                    if ($attributes['includetime']) {
                        $mform->addElement('date_time_selector', 'customfield_' . $field->shortname, format_text($field->name), ['optional' => true]);
                    } else {
                        $mform->addElement('date_selector', 'customfield_' . $field->shortname, format_text($field->name), ['optional' => true]);
                    }
                    break;
                case "select":
                    $options = [0 => ''] + explode("\r\n", $attributes['options']);
                    $mform->addElement('select', 'customfield_' . $field->shortname, format_text($field->name), $options, $attributes);
                    break;
            }
        }

        // Add the button(s).
        $buttonarray=[];
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('userfilter', 'local_iomad'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
}