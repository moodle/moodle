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
 * Select for blog's bloglevel setting: if set to 0, will set blog_menu
 * block to hidden.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class bloglevel extends \admin_setting_configselect {
    /**
     * Updates the database and save the setting
     *
     * @param string data
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $DB, $CFG;
        if ($data == 0) {
            $blogblocks = $DB->get_records_select('block', "name LIKE 'blog_%' AND visible = 1");
            foreach ($blogblocks as $block) {
                $DB->set_field('block', 'visible', 0, array('id' => $block->id));
            }
        } else {
            // reenable all blocks only when switching from disabled blogs
            if (isset($CFG->bloglevel) and $CFG->bloglevel == 0) {
                $blogblocks = $DB->get_records_select('block', "name LIKE 'blog_%' AND visible = 0");
                foreach ($blogblocks as $block) {
                    $DB->set_field('block', 'visible', 1, array('id' => $block->id));
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
