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
 * Load events that were transformed successfully in batches.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\loader\utils;

/**
 * Load events that were transformed successfully in batches.
 *
 * @param array $config An array of configuration settings.
 * @param array $events An array of events.
 * @param callable $loader
 * @return array
 */
function load_in_batches(array $config, array $events, callable $loader) {
    $successfultransformevents = filter_transformed_events($events, true);
    $batches = get_event_batches($config, $successfultransformevents);
    $loadedevents = array_reduce($batches, function ($result, $batch) use ($config, $loader) {
        $loadedbatchevents = load_batch($config, $batch, $loader);
        return array_merge($result, $loadedbatchevents);
    }, []);

    // Flags events that weren't transformed successfully as events that didn't load.
    $failedtransformevents = filter_transformed_events($events, false);

    // Add error code.
    foreach ($failedtransformevents as $failed) {
        $failed["event"]->errortype = XAPI_REPORT_ERRORTYPE_TRANSFORM;
    }

    $nonloadedevents = construct_loaded_events($failedtransformevents, false);

    // Returns loaded and non-loaded events to avoid re-processing.
    return array_merge($loadedevents, $nonloadedevents);
}
