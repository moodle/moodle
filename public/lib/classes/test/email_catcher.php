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

namespace core\test;

/**
 * Generic email catcher interface.
 *
 * @package    core
 * @category   test
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Simey Lameze <simey@moodle.com>
 */
interface email_catcher {

    /**
     * Get a list of messages from the mailpit server.
     *
     * @param bool $showdetails Optional. Whether to include detailed information in the messages. Default is false.
     * @return iterable<message>
     */
    public function get_messages(bool $showdetails = false): iterable;

    /**
     * Delete all messages from the mailpit server.
     */
    public function delete_all();

    /**
     * Search for a message in the mailpit server.
     *
     * @param string $query The search query.
     * @return iterable
     */
    public function search(string $query): iterable;
}
