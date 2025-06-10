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
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Get the requirements.
require_once($CFG->dirroot . '/course/edit_form.php');

/**
 *
 * Overrides the course form.
 */
class ues_course_edit_form extends course_edit_form {
    public function definition() {
        global $USER, $DB;
        parent::definition();

        $m =& $this->_form;

        // Class course_edit_form does not insert the lang element, so we do it here.
        // This prevents a failure to remove the lang element error when it is in the restricted fields list.
        // It may have been inserted from a cross-listing or some other interaction, so check first.
        if (!isset($m->_elementIndex['lang']))
        {
            $lang =$this->_customdata['lang'];
            $m->addElement('hidden', 'lang', null);
            $m->setType('lang', PARAM_LANG);
            $m->setConstant('lang', $lang);
        }

        $restricted = get_config('enrol_ues', 'course_restricted_fields');
        $restrictedfields = explode(',', $restricted);

        $system = context_system::instance();
        $canchange = has_capability('moodle/course:update', $system);

        foreach ($restrictedfields as $field) {
            if ($canchange) {
                continue;
            }
            $m->removeElement($field);
        }

        $disablegrouping = (
            in_array('groupmode', $restrictedfields) and
            in_array('groupmodeforce', $restrictedfields)
        );

        if ($disablegrouping) {
            $m->hardFreeze('defaultgroupingid');
            $m->removeElement('groups');
        }

        $roles = $DB->get_records('role');
        foreach ($roles as $id => $role) {
            $name = 'role_' . $id;
            if ($m->elementExists($name)) {
                $m->removeElement($name);
            }
        }

        $m->removeElement('rolerenaming');
    }
}
