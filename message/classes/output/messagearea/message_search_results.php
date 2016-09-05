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
 * Contains class used to display message search results.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output\messagearea;

use renderable;
use templatable;

/**
 * Class used to display message search results.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_search_results implements templatable, renderable {

    /**
     * @var int The id of the user that the contacts belong to.
     */
    public $userid;

    /**
     * @var \core_message\output\messagearea\contact[] The list of contacts.
     */
    public $contacts;

    /**
     * Constructor.
     *
     * @param int $userid The id of the user the search results belong to
     * @param \core_message\output\messagearea\contact[] $contacts
     */
    public function __construct($userid, $contacts) {
        $this->userid = $userid;
        $this->contacts = $contacts;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        $data->userid = $this->userid;
        $data->contacts = array();
        foreach ($this->contacts as $contact) {
            $data->contacts[] = $contact->export_for_template($output);
        }

        return $data;
    }
}