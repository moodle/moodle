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
 * The mod_bigbluebuttonbn recording unpublished event (triggered when a recording is unpublished).
 *
 * @package     logstore_xapi
 * @copyright   Paul Walter (https://github.com/paulito-bandito)
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_bigbluebuttonbn;

/**
 * Transformer for the bigbluebutton recording unpublished event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @return array
 */
function recording_unpublished(array $config, \stdClass $event) {
    return create_statement( $config, $event, 'http://activitystrea.ms/schema/1.0/retract', 'retracted' );
}
