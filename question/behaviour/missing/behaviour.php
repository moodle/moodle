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
 * Fake question behaviour that is used when the actual qim was not
 * available.
 *
 * @package    qbehaviour
 * @subpackage missing
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Fake question behaviour that is used when the actual behaviour
 * is not available.
 *
 * Imagine, for example, that a quiz attempt has been restored from another
 * Moodle site with more behaviours installed, or an behaviour
 * that used to be available in this site has been uninstalled. Obviously all we
 * can do is have some code to prevent fatal errors.
 *
 * The approach we take is: The rendering code is still implemented, as far as
 * possible. A warning is shown that behaviour specific bits may be missing.
 * Any attempt to process anything causes an exception to be thrown.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_missing extends question_behaviour {
    public function required_question_definition_type() {
        return 'question_definition';
    }

    public function summarise_action(question_attempt_step $step) {
        return '';
    }

    public function init_first_step(question_attempt_step $step, $variant) {
        throw new coding_exception('The behaviour used for this question is not available. ' .
                'No processing is possible.');
    }

    public function process_action(question_attempt_pending_step $pendingstep) {
        throw new coding_exception('The behaviour used for this question is not available. ' .
                'No processing is possible.');
    }

    public function get_min_fraction() {
        throw new coding_exception('The behaviour used for this question is not available. ' .
                'No processing is possible.');
    }
}
