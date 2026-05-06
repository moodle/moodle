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

namespace core_admin\setting\setting;

/**
 * Blog level setting: if set to 0, disables the blog menu block.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bloglevel extends \core_admin\setting\setting\configselect {
    #[\Override]
    public function write_setting($data) {
        global $DB, $CFG;
        if ($data == 0) {
            $blogblocks = $DB->get_records_select('block', "name LIKE 'blog_%' AND visible = 1");
            foreach ($blogblocks as $block) {
                $DB->set_field('block', 'visible', 0, ['id' => $block->id]);
            }
        } else {
            // Reenable all blocks only when switching from disabled blogs.
            if (isset($CFG->bloglevel) && $CFG->bloglevel == 0) {
                $blogblocks = $DB->get_records_select('block', "name LIKE 'blog_%' AND visible = 0");
                foreach ($blogblocks as $block) {
                    $DB->set_field('block', 'visible', 1, ['id' => $block->id]);
                }
            }
        }
        return parent::write_setting($data);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(bloglevel::class, \admin_setting_bloglevel::class);
