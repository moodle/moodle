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

namespace mod_questionnaire\generator;

use mod_questionnaire\responsetype\response\response;

/**
 * Question response class
 * @author    gthomas2
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod_questionnaire
 */
class question_response {
    /** @var int $questionid */
    public $questionid;
    /** @var response $response */
    public $response;

    /**
     * Class constructor.
     * @param int $questionid
     * @param response $response
     */
    public function __construct($questionid, $response) {
        $this->questionid = $questionid;
        $this->response = $response;
    }
}
