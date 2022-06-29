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
 * @package    moodlecore
 * @subpackage backup-xml
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class implements one @xml_output able to store and return output in memory
 *
 * Although possible to use, has been defined as not supporting buffering for
 * testing purposes. get_allcontents() will return the contents after ending.
 *
 * TODO: Finish phpdocs
 */
class memory_xml_output extends xml_output{

    protected $allcontents; // Here we'll store all the written contents

    public function __construct() {
        $this->allcontents = '';
        parent::__construct(false); // disable buffering
    }

    public function get_allcontents() {
        if ($this->running !== false) {
            throw new xml_output_exception('xml_output_not_stopped');
        }
        return $this->allcontents;
    }

// Private API starts here

    protected function init() {
        // Easy :-)
    }

    protected function finish() {
        // Trivial :-)
    }

    protected function send($content) {
        // Accumulate contents
        $this->allcontents .= $content;
    }

}
