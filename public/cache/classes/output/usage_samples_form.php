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
 * Form for usage page to select number of samples.
 *
 * @package core_cache
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_cache\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for usage page to select number of samples.
 *
 * @package core_cache
 */
class usage_samples_form extends \moodleform {
    /**
     * Constructor sets form up to use GET request to current page.
     */
    public function __construct() {
        parent::__construct(null, null, 'get');
    }

    /**
     * Adds controls to form.
     */
    protected function definition() {
        $mform = $this->_form;

        $radioarray = [];
        foreach ([50, 100, 200, 500, 1000] as $samples) {
            $radioarray[] = $mform->createElement('radio', 'samples', '', $samples, $samples);
        }
        $mform->setDefault('samples', 50);
        $mform->addGroup($radioarray, 'samplesradios', get_string('usage_samples', 'cache'), [' '], false);
        $mform->addElement('submit', 'submit', get_string('update'));
    }
}
