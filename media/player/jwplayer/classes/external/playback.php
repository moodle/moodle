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
 * External functions for JW Player plugin.
 *
 * @package   media_jwplayer
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2020 Ecole hôtelière de Lausanne {@link https://www.ehl.edu/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace media_jwplayer\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;

/**
 * External functions for JW Player plugin.
 *
 * @author     Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright  2020 Ecole hôtelière de Lausanne {@link https://www.ehl.edu/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class playback extends external_api {

    /**
     * Relay playback event.
     *
     * @param   int     $context    The Context ID of the current page.
     * @param   string  $event      The name of event.
     * @param   string  $title      The title of the media.
     * @param   int     $position   The position in seconds in the file on which event occured (optional).
     * @param   int     $offset     The position in seconds in the file where seek is requested (optional).
     * @return  array               As described in playback_event_returns
     */
    public static function playback_event($context, $event, $title, $position, $offset) {
        $context = \context_helper::instance_by_id($context);
        self::validate_context($context);

        $eventclass = '\\media_jwplayer\\event\\playback_' . $event;
        if (!class_exists($eventclass)) {
            throw new \invalid_parameter_exception("Event class $eventclass does not exist.");
        }

        $eventclass::create([
            'contextid' => $context->id,
            'other'     => [
                'title'    => $title,
                'position' => $position,
                'offset'   => $offset,
            ],
        ])->trigger();

        return [];
    }

    /**
     * The parameters for playback_event.
     *
     * @return external_function_parameters
     */
    public static function playback_event_parameters() {
        return new external_function_parameters([
            'context'   => new external_value(PARAM_INT,  'Context ID'),
            'event'     => new external_value(PARAM_TEXT, 'Event name'),
            'title'     => new external_value(PARAM_TEXT, 'Media title'),
            'position'  => new external_value(PARAM_INT,  'Position in the file (sec)', VALUE_DEFAULT, 0),
            'offset'    => new external_value(PARAM_INT,  'Seek offset (sec)', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * The return configuration for playback_event.
     *
     * @return external_single_structure
     */
    public static function playback_event_returns() {
        return new external_single_structure([]);
    }

    /**
     * Playback failure.
     *
     * @param   int     $context    The Context ID of the current page.
     * @param   string  $title      The title of the media.
     * @param   int     $position   The position in seconds in the file on which event occured (optional).
     * @param   string  $code       The error code.
     * @param   string  $message    The error message.
     * @return  array               As described in playback_failed_returns
     */
    public static function playback_failed($context, $title, $position, $code, $message) {
        $context = \context_helper::instance_by_id($context);
        self::validate_context($context);

        \media_jwplayer\event\playback_failed::create([
            'contextid' => $context->id,
            'other'     => [
                'title'    => $title,
                'position' => $position,
                'code'     => $code,
                'message'  => $message,
            ],
        ])->trigger();

        return [];
    }

    /**
     * The parameters for playback_failed.
     *
     * @return external_function_parameters
     */
    public static function playback_failed_parameters() {
        return new external_function_parameters([
            'context'   => new external_value(PARAM_INT,  'Context ID'),
            'title'     => new external_value(PARAM_TEXT, 'Media title'),
            'position'  => new external_value(PARAM_INT,  'Position in the file (sec)', VALUE_DEFAULT, 0),
            'code'      => new external_value(PARAM_TEXT, 'Error code'),
            'message'   => new external_value(PARAM_TEXT, 'Error message'),
        ]);
    }

    /**
     * The return configuration for playback_failed.
     *
     * @return external_single_structure
     */
    public static function playback_failed_returns() {
        return new external_single_structure([]);
    }
}
