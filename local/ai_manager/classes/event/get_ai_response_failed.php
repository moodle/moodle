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

namespace local_ai_manager\event;

use local_ai_manager\local\prompt_response;

/**
 * An event fired when the request to an external AI endpoint fails.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_ai_response_failed extends \core\event\base {

    /**
     * Init function for this event, setting some basic attributes.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns the lang string of the event's name.
     *
     * @return string the localized name of the event
     */
    public static function get_name(): string {
        return get_string('get_ai_response_failed', 'local_ai_manager');
    }

    /**
     * Gets the localized description of the event.
     *
     * @return string the description string
     */
    public function get_description(): string {
        return get_string('get_ai_response_failed_desc', 'local_ai_manager');
    }

    /**
     * Creates the event with the proper information.
     *
     * @param array $promptdata the final prompt data being passed to the external endpoint
     * @param prompt_response $promptresponse The object containing the information about the prompt response
     * @param float $duration The duration of the request
     */
    public static function create_from_prompt_response(array $promptdata, prompt_response $promptresponse,
            float $duration): \core\event\base {
        $data = [
                'contextid' => \context_system::instance()->id,
                'other' => [
                        'code' => $promptresponse->get_code(),
                        'promptdata' => json_encode($promptdata),
                        'errormessage' => $promptresponse->get_errormessage(),
                        'debuginfo' => $promptresponse->get_debuginfo(),
                        'duration' => $duration,
                ],
        ];

        return self::create($data);
    }
}
