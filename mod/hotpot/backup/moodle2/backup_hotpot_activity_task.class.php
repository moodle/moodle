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
 * Class {@link backup_hotpot_activity_task} definition
 *
 * @package    mod_hotpot
 * @subpackage backup-moodle2
 * @copyright  2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/hotpot/backup/moodle2/backup_hotpot_stepslib.php');

/**
 * Provides all the settings and steps to perform one complete backup of hotpot activity
 */
class backup_hotpot_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        $this->add_step(new backup_hotpot_activity_structure_step('hotpot_structure', 'hotpot.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of hotpots
        $search = "/(" . $base . "\/mod\/hotpot\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@HOTPOTINDEX*$2@$', $content);

        //Link to hotpot view by moduleid
        $search = "/(" . $base . "\/mod\/hotpot\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@HOTPOTVIEWBYID*$2@$', $content);

        return $content;
    }
}
