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
 * Form to filter export data.
 *
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package tool_dataprivacy
 * @since Moodle 4.3
 */

namespace tool_dataprivacy\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Form to filter export data.
 *
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package tool_dataprivacy
 * @since   Moodle 4.3
 */
class exportfilter_form extends \moodleform {
    /**
     * Form definition.
     */
    public function definition(): void {
        $requestid = $this->_customdata['requestid'];
        $mform = $this->_form;
        $selectitems = [];

        $mform->addElement('hidden', 'requestid', $requestid);
        $mform->setType('requestid', PARAM_INT);
        $contexts = \tool_dataprivacy\api::get_course_contexts_for_view_filter($requestid);
        foreach ($contexts as $context) {
            $coursename = '';
            $groupname = '';
            $parentcontexts = $context->get_parent_contexts(true);
            $parentcontexts = array_reverse($parentcontexts);
            end($parentcontexts);
            $lastkey = key($parentcontexts);
            reset($parentcontexts);
            $firstkey = key($parentcontexts);

            foreach ($parentcontexts as $key => $parentcontext) {
                if ($key !== $lastkey) {
                    if ($key !== $firstkey) {
                        $groupname .= ' > ';
                    }
                    $groupname .= $parentcontext->get_context_name(false);
                } else {
                    $coursename = $parentcontext->get_context_name(false);
                }
            }

            $selectitems[$groupname][$context->id] = $coursename;
        }

        if ($contexts) {
            $mform->addElement(
                'selectgroups',
                'coursecontextids',
                get_string('selectcourses', 'tool_dataprivacy'),
                $selectitems,
            );
            $mform->getElement('coursecontextids')->setMultiple(true);
            $mform->getElement('coursecontextids')->setSize(15);
        } else {
            $mform->addElement('html', get_string('nocoursetofilter', 'tool_dataprivacy'));
        }
    }

    /**
     * Form validation.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = [];

        if (empty($data['coursecontextids'])) {
            $errors['coursecontextids'] = get_string('errornoselectedcourse', 'tool_dataprivacy');
        }

        return $errors;
    }
}
