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
 * Contains processor class for displaying on message preferences page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output\preferences;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;

/**
 * Class to create context for one of the message processors settings on the message preferences page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processor implements templatable, renderable {

    /**
     * @var \stdClass The message processor.
     */
    protected $processor;

    /**
     * @var \stdClass list of message preferences.
     */
    protected $preferences;

    /**
     * @var \stdClass A user.
     */
    protected $user;

    /**
     * @var string The processor type.
     */
    protected $type;

    /**
     * Constructor.
     *
     * @param \stdClass $processor
     * @param \stdClass $preferences
     * @param \stdClass $user
     * @param string $type
     */
    public function __construct($processor, $preferences, $user, $type) {
        $this->processor = $processor;
        $this->preferences = $preferences;
        $this->user = $user;
        $this->type = $type;
    }

    public function export_for_template(\renderer_base $output) {
        return [
            'userid' => $this->user->id,
            'displayname' => get_string('pluginname', 'message_'.$this->type),
            'name' => $this->type,
            'formhtml' => $this->processor->config_form($this->preferences),
        ];
    }
}
