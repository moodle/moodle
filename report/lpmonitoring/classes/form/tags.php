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
 * Class for managing tags related to user plans.
 *
 * @package    report_lpmonitoring
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\form;
defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

use moodleform;
use renderable;
use MoodleQuickForm;
require_once($CFG->libdir.'/formslib.php');

/**
 * Class for managing tags related to user plans.
 *
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags extends moodleform  implements renderable {
    /**
     * Tags form definition.
     */
    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $planid = $this->_customdata['planid'];

        MoodleQuickForm::registerElementType('tagautocomplete',
                "$CFG->dirroot/report/lpmonitoring/classes/form/tagautocomplete.php", 'tagautocomplete');

        $mform->addElement('tagautocomplete', 'tags', get_string('tags', 'report_lpmonitoring'),
                ['itemtype' => 'competency_plan', 'component' => 'report_lpmonitoring']);

        $this->add_action_buttons();
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHANUMEXT);
        $mform->setDefault('action', '');

        $mform->addElement('hidden', 'planid');
        $mform->setType('planid', PARAM_INT);
        $mform->setDefault('planid', $planid);
    }
}
