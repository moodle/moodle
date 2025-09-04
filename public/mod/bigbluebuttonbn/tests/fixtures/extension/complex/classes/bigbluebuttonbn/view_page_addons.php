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

namespace bbbext_complex\bigbluebuttonbn;

use stdClass;
use mod_bigbluebuttonbn\instance;

/**
 * View Page template renderable.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2025 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class view_page_addons extends \mod_bigbluebuttonbn\local\extension\view_page_addons {
    /** @var instance The instance being rendered */
    protected $instance;

    /**
     * Constructor for the View Page.
     *
     * @param instance $instance
     */
    public function __construct(instance $instance) {
        $this->instance = $instance;
    }

    /**
     * Export the content required to render the template.
     *
     * @param mixed $renderer The renderer instance (matches parent signature).
     * @return stdClass
     */
    public function export_for_template($renderer): \stdClass {
        return (object) [
            'message' => 'Hello from complex::renderer!',
            'meetingname' => $this->instance->get_meeting_name(),
            'description' => $this->instance->get_meeting_description(true),
        ];
    }
}
