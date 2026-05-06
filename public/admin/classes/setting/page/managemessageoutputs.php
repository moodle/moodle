<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\page;

use core_admin\admin_search;

/**
 * Message output configuration admin page.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class managemessageoutputs extends \core_admin\setting\tree\externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct(
            'managemessageoutputs',
            get_string('defaultmessageoutputs', 'message'),
            new \moodle_url('/admin/message.php')
        );
    }

    /**
     * Search for a specific message processor
     *
     * @param string $query The string to search for
     * @return array
     */
    public function search($query) {
        global $CFG, $DB;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        if ($processors = get_message_processors()) {
            foreach ($processors as $processor) {
                if (!$processor->available) {
                    continue;
                }
                if (strpos($processor->name, $query) !== false) {
                    $type = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                    $found = true;
                    break;
                }
                $strprocessorname = get_string('pluginname', 'message_' . $processor->name);
                if (strpos(\core_text::strtolower($strprocessorname), $query) !== false) {
                    $type = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                    $found = true;
                    break;
                }
            }
        }
        if ($found) {
            $result = new \stdClass();
            $result->page     = $this;
            $result->settings = [];
            $result->searchmatchtype = $type;
            return [$this->name => $result];
        } else {
            return [];
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(managemessageoutputs::class, \admin_page_managemessageoutputs::class);
