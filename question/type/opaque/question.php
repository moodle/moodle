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
 * Opaque question definition class.
 *
 * @package    qtype
 * @subpackage opaque
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Represents an Opaque question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_opaque_question extends question_definition {
    /** @var integer the ID of the question engine that serves this question. */
    public $engineid;
    /** @var string the id by which the question engine knows this question. */
    public $remoteid;
    /** @var string the version number of this question to use. */
    public $remoteversion;

    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        question_engine::load_behaviour_class('opaque');
        return new qbehaviour_opaque($qa, $preferredbehaviour);
    }

    public function get_expected_data() {
        return question_attempt::USE_RAW_DATA;
    }

    public function get_correct_response() {
        // Not possible to say, so just return nothing.
        return array();
    }
}
