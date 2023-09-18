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

namespace tool_cohortroles\form;
defined('MOODLE_INTERNAL') || die();

use moodleform;
use context_system;

require_once($CFG->libdir . '/formslib.php');

/**
 * Assign role to cohort form.
 *
 * @package    tool_cohortroles
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_role_cohort extends moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $PAGE;

        $mform = $this->_form;
        $roles = get_roles_for_contextlevels(CONTEXT_USER);

        if (empty($roles)) {
            $output = $PAGE->get_renderer('tool_cohortroles');
            $warning = $output->notify_problem(get_string('noassignableroles', 'tool_cohortroles'));
            $mform->addElement('html', $warning);
            return;
        }

        $options = array(
            'ajax' => 'core_user/form_user_selector',
            'multiple' => true
        );
        $mform->addElement('autocomplete', 'userids', get_string('selectusers', 'tool_cohortroles'), array(), $options);
        $mform->addRule('userids', null, 'required');

        $names = role_get_names();
        $options = array();
        foreach ($roles as $idx => $roleid) {
            $options[$roleid] = $names[$roleid]->localname;
        }

        $mform->addElement('select', 'roleid', get_string('selectrole', 'tool_cohortroles'), $options);
        $mform->addRule('roleid', null, 'required');

        $context = context_system::instance();
        $options = array(
            'multiple' => true,
            'data-contextid' => $context->id,
            'data-includes' => 'all'
        );
        $mform->addElement('cohort', 'cohortids', get_string('selectcohorts', 'tool_cohortroles'), $options);
        $mform->addRule('cohortids', null, 'required');
        $mform->addElement('submit', 'submit', get_string('assign', 'tool_cohortroles'));
    }

}
