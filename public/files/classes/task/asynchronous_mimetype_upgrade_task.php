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

namespace core_files\task;

use core\task\adhoc_task;

/**
 * Ad-hoc task that performs asynchronous upgrades of a given file type.
 *
 * This ad-hoc task can be scheduled during core upgrades.
 *
 * @package    core_files
 * @copyright  2025 Daniel Ziegenberg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class asynchronous_mimetype_upgrade_task extends adhoc_task {

    /**
     * Run the adhoc task and update the mime type of files.
     */
    public function execute(): void {
        global $DB;

        // Upgrade mime type for existing files.
        $customdata = $this->get_custom_data();
        foreach ($customdata->extensions as $extension) {
            mtrace("Updating mime type for files with extension *.{$extension} to {$customdata->mimetype}");

            $condition = $DB->sql_like('filename', ":extension", false);
            $select = "{$condition} AND mimetype <> :mimetype";
            $params = [
                'extension' => '%' . $DB->sql_like_escape(".{$extension}"),
                'mimetype' => $customdata->mimetype,
            ];

            $count = $DB->count_records_select('files', $select, $params);
            $DB->set_field_select('files', 'mimetype', $customdata->mimetype, $select, $params);

            mtrace("Updated {$count} files with extension *.{$extension} to {$customdata->mimetype}");
        }
    }
}
