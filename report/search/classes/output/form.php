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
 * Global Search admin form definition
 *
 * @package   report_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_search\output;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Search report form.
 *
 * @package    report_search
 * @copyright  Prateek Sachan {@link http://prateeksachan.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class form extends \moodleform {

    /**
     * Form definition.
     *
     * @return void
     */
    public function definition() {

        $mform = $this->_form;

        $checkboxarray = array();
        $checkboxarray[] =& $mform->createElement('checkbox', 'reindex', '', get_string('indexsite', 'report_search'));
        $mform->addGroup($checkboxarray, 'reindexcheckbox', '', array(''), false);
        $mform->closeHeaderBefore('reindexcheckbox');

        $checkboxarray = array();
        $checkboxarray[] =& $mform->createElement('checkbox', 'delete', '', get_string('delete', 'report_search'));
        $mform->addGroup($checkboxarray, 'deletecheckbox', '', array(''), false);
        $mform->closeHeaderBefore('deletecheckbox');

        // Only available if delete checked.
        $areacheckboxarray = array();
        $areacheckboxarray[] =& $mform->createElement('advcheckbox', 'all', '', get_string('entireindex', 'report_search'),
            array('group' => 1));
        $mform->setDefault('all', true);

        foreach ($this->_customdata['searchareas'] as $key => $searcharea) {
            $areacheckboxarray[] =& $mform->createElement('advcheckbox', $key, '',
                $searcharea->get_visible_name(), array('group' => 2));
        }
        $mform->addGroup($areacheckboxarray, 'areasadvcheckbox', '', array(' '), false);
        $mform->closeHeaderBefore('areasadvcheckbox');
        $mform->disabledIf('areasadvcheckbox', 'delete', 'notchecked');

        $this->add_action_buttons(false, get_string('execute', 'report_search'));
    }
}
