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
 * Information about the student's progress, to pass on to the progress bar output
 *
 * @package   mod_checklist
 * @copyright 2016 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_checklist\local;

defined('MOODLE_INTERNAL') || die();

class progress_info {
    public $totalitems;
    public $requireditems;
    public $allcompleteitems;
    public $requiredcompleteitems;

    /**
     * progress_info constructor.
     * @param $totalitems
     * @param $requireditems
     * @param $allcompleteitems
     * @param $requiredcompleteitems
     */
    public function __construct($totalitems, $requireditems, $allcompleteitems, $requiredcompleteitems) {
        $this->totalitems = $totalitems;
        $this->requireditems = $requireditems;
        $this->allcompleteitems = $allcompleteitems;
        $this->requiredcompleteitems = $requiredcompleteitems;
    }
}
