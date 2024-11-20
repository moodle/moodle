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
 * Load batch of events.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\loader\utils;

/**
 * Load a batch of events.
 *
 * @param array $config An array of configuration settings.
 * @param array $transformedevents An array of events.
 * @param callable $loader
 * @return array
 */
function load_batch(array $config, array $transformedevents, callable $loader) {
    try {
        $statements = array_reduce($transformedevents, function ($result, $transformedevent) {
            $eventstatements = $transformedevent['statements'];
            return array_merge($result, $eventstatements);
        }, []);
        $loader($config, $statements);
        $loadedevents = construct_loaded_events($transformedevents, true);
        return $loadedevents;
    } catch (\Exception $e) {
        $batchsize = count($transformedevents);
        $logerror = $config['log_error'];
        $logerror("Failed load batch (" . $batchsize . " events)" . $e->getMessage());
        $logerror($e->getTraceAsString());

        // Add error code.
        $errorcode = $e->getCode();
        $errormessage = $e->getMessage();
        foreach ($transformedevents as $event) {
            if ($event["transformed"] == true) {
                $event["event"]->response = $errormessage;
                $event["event"]->errortype = $errorcode;
            }
        }

        // In the event of a 400 error, recursively retry sending statements in increasingly
        // smaller batches so that only the actual bad data fails.
        if ($batchsize === 1 || $e->getCode() !== 400 || $config['lrs_resend_failed_batches'] !== '1') {
            $loadedevents = construct_loaded_events($transformedevents, false);
        } else {
            $newconfig = $config;
            $newconfig['lrs_max_batch_size'] = round($batchsize / 2);
            $batches = get_event_batches($newconfig, $transformedevents);
            $loadedevents = array_reduce($batches, function ($result, $batch) use ($newconfig, $loader) {
                $loadedbatchevents = load_batch($newconfig, $batch, $loader);
                return array_merge($result, $loadedbatchevents);
            }, []);
        }
        return $loadedevents;
    }
}
