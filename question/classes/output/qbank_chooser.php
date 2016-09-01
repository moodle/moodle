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
 * The qbank_chooser renderable.
 *
 * @package    core_question
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\output;
defined('MOODLE_INTERNAL') || die();

use core\output\chooser_section;
use lang_string;
use moodle_url;


/**
 * The qbank_chooser renderable class.
 *
 * @package    core_question
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbank_chooser extends \core\output\chooser {

    /**
     * Constructor.
     */
    public function __construct($real, $fake, $course, $hiddenparams, $context) {
        $sections = [];
        $sections[] = new chooser_section('questions', new lang_string('questions', 'question'),
            array_map(function($qtype) use ($context) {
                return new qbank_chooser_item($qtype, $context);
            }, $real));

        $sections[] = new chooser_section('other', new lang_string('other'),
            array_map(function($qtype) use ($context) {
                return new qbank_chooser_item($qtype, $context);
            }, $fake));

        parent::__construct(new moodle_url('/question/question.php'),
            new lang_string('chooseqtypetoadd', 'question'), $sections, 'qtype');

        $this->set_instructions(new lang_string('selectaqtypefordescription', 'question'));

        $this->set_method('get');
        $this->add_param('courseid', $course->id);
        foreach ($hiddenparams as $k => $v) {
            $this->add_param($k, $v);
        }
    }

}
