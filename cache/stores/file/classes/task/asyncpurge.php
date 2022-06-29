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

namespace cachestore_file\task;

/**
 * Task deletes old cache revision directory.
 *
 * @package   cachestore_file
 * @copyright Catalyst IT Europe Ltd 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jackson D'Souza <jackson.dsouza@catalyst-eu.net>
 */
class asyncpurge extends \core\task\adhoc_task {

    /**
     * Executes the scheduled task.
     *
     * @return boolean True if old cache revision directory exists and is deleted. False otherwise.
     */
    public function execute(): bool {

        $returnvar = true;
        $output = 'Cleaning up file store old cache revision directory:' . PHP_EOL;

        $data = $this->get_custom_data();
        if (is_dir($data->path)) {
            remove_dir($data->path);
            $output .= 'Directory deleted: ' . $data->path;
        } else {
            $output .= 'Directory not found: ' . $data->path;
            $returnvar = false;
        }
        if (!PHPUNIT_TEST) {
            mtrace($output);
        }
        return $returnvar;
    }

}
