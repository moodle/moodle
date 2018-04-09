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
 * Contains class used to prepare a message processor for display.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/message/lib.php');

use renderable;
use templatable;

/**
 * Class to prepare a message processor for display.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processor implements templatable, renderable {

    /**
     * @var \stdClass The message processor
     */
    protected $processor;

    /**
     * @var \stdClass The user
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param \stdClass $processor
     * @param \stdClass $user
     */
    public function __construct($processor, $user) {
        $this->processor = $processor;
        $this->user = $user;
    }

    public function export_for_template(\renderer_base $output) {
        $processor = $this->processor;
        $user = $this->user;

        $context = [
            'systemconfigured' => $processor->is_system_configured(),
            'userconfigured' => $processor->is_user_configured($user),
        ];

        return $context;
    }
}
