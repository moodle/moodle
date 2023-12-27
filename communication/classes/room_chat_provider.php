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

namespace core_communication;

/**
 * Class communication_room_base to manage the room operations of communication providers.
 *
 * Every plugin that supports room operation must implement/extend this class in the plugin.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface room_chat_provider {
    /**
     * Create a provider room when a instance is created.
     */
    public function create_chat_room(): bool;

    /**
     * Update a provider room when a instance is updated.
     */
    public function update_chat_room(): bool;

    /**
     * Delete a provider room when a instance is deleted.
     */
    public function delete_chat_room(): bool;

    /**
     * Generate a room url if there is a room.
     *
     * @return string|null
     */
    public function get_chat_room_url(): ?string;
}
