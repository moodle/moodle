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
 * Transformer handler.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer;

/**
 * Generic handler for the transformer.
 *
 * @param array $config The transformer config settings.
 * @param array $events The event to be transformed.
 * @return array
 */
function handler(array $config, array $events) {
    $eventfunctionmap = get_event_function_map();
    $transformedevents = array_map(function ($event) use ($config, $eventfunctionmap) {
        $eventobj = (object) $event;
        try {
            $eventname = $eventobj->eventname;
            if (isset($eventfunctionmap[$eventname])) {
                $eventfunctionname = $eventfunctionmap[$eventname];
                $eventfunction = '\src\transformer\events\\' . $eventfunctionname;
                $eventconfig = array_merge([
                    'event_function' => $eventfunction,
                ], $config);
                $eventstatements = $eventfunction($eventconfig, $eventobj);
            } else {
                $eventstatements = [];
            }

            // Returns successfully transformed event with its statements.
            $transformedevent = [
                'event' => $eventobj,
                'statements' => $eventstatements,
                'transformed' => true,
            ];
            return $transformedevent;
        } catch (\Exception $e) {
            $logerror = $config['log_error'];
            $errormessage = "Failed transform for event id #" . $eventobj->id . ": " .  $e->getMessage();
            $logerror($errormessage);
            $logerror($e->getTraceAsString());
            $eventobj->response = json_encode(['transfromerror' => $errormessage]);

            // Returns unsuccessfully transformed event without statements.
            $transformedevent = [
                'event' => $eventobj,
                'statements' => [],
                'transformed' => false,
            ];
            return $transformedevent;
        }
    }, $events);
    return $transformedevents;
}
