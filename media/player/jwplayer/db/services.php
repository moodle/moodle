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
 * List of Web Services for the media_jwplayer plugin.
 *
 * @package   media_jwplayer
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2020 Ecole hôtelière de Lausanne {@link https://www.ehl.edu/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'media_jwplayer_playback_event' => [
        'classname'       => 'media_jwplayer\external\playback',
        'methodname'      => 'playback_event',
        'description'     => 'Process playback event',
        'type'            => 'read',
        'capabilities'    => '',
        'ajax'            => true,
    ],
    'media_jwplayer_playback_failed' => [
        'classname'       => 'media_jwplayer\external\playback',
        'methodname'      => 'playback_failed',
        'description'     => 'Process playback failure',
        'type'            => 'read',
        'capabilities'    => '',
        'ajax'            => true,
    ],
];
