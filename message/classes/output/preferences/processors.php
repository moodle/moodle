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
 * Contains processors class for displaying on message preferences
 * page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output\preferences;

use renderable;
use templatable;

/**
 * Class to create context for each of the message processors settings
 * on the message preferences page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processors implements templatable, renderable {

    /**
     * A list of message processors.
     */
    protected $processors;

    /**
     * A list of message preferences.
     */
    protected $preferences;

    /**
     * A user.
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param array $processors
     * @param stdClass $preferences
     * @param stdClass $user
     */
    public function __construct($processors, $preferences, $user) {
        $this->processors = $processors;
        $this->preferences = $preferences;
        $this->user = $user;
    }

    public function export_for_template(\renderer_base $output) {
        $context = [
            'userid' => $this->user->id,
            'processors' => [],
        ];

        foreach ($this->processors as $processor) {
            $formhtml = $processor->object->config_form($this->preferences);

            if (!$formhtml) {
                continue;
            }

            $context['processors'][] = [
                'displayname' => get_string('pluginname', 'message_'.$processor->name),
                'name' => $processor->name,
                'formhtml' => $formhtml,
            ];
        }

        return $context;
    }
}
