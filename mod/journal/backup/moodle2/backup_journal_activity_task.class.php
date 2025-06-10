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
 * mod_journal backup moodle 2 structure
 *
 * @package    mod_journal
 * @copyright  2014 David Monllao <david.monllao@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/journal/backup/moodle2/backup_journal_stepslib.php');

/**
 * The backup_journal_activity_task class.
 *
 * @package    mod_journal
 * @copyright  2022 Elearning Software SRL http://elearningsoftware.ro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_journal_activity_task extends backup_activity_task {

    /**
     * Define the settings for the backup process
     *
     * @return void
     */
    protected function define_my_settings() {
    }

    /**
     * Define the steps for the backup process
     *
     * @return void
     */
    protected function define_my_steps() {
        $this->add_step(new backup_journal_activity_structure_step('journal_structure', 'journal.xml'));
    }

    /**
     * Encode content links for the backup process
     *
     * @param string $content Content
     * @return string $content with links encoded
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot.'/mod/journal', '#');

        $pattern = "#(".$base."\/index.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@JOURNALINDEX*$2@$', $content);

        $pattern = "#(".$base."\/view.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@JOURNALVIEWBYID*$2@$', $content);

        $pattern = "#(".$base."\/report.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@JOURNALREPORT*$2@$', $content);

        $pattern = "#(".$base."\/edit.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@JOURNALEDIT*$2@$', $content);

        return $content;
    }
}
