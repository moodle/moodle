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
 * Install script for tool_moodlenet.
 *
 * @package   tool_moodlenet
 * @copyright 2022 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

/**
 * Perform the post-install procedures.
 */
function xmldb_tool_moodlenet_install() {
    // Set the active activity chooser footer plugin to tool_moodlenet.
    // We couldn't do this in admin/settings/courses.php for 2 reasons:
    // First, because it would be a breach of component communications principles to do so there.
    // Second, because we can't call get_plugins_with_function() during install and upgrade (or it will return []).
    set_config('activitychooseractivefooter', 'tool_moodlenet');
}
