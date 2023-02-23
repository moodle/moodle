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
 * Class for backup BigBlueButtonBN.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once($CFG->dirroot.'/mod/bigbluebuttonbn/backup/moodle2/backup_bigbluebuttonbn_stepslib.php');

/**
 * Backup task that provides all the settings and steps to perform one complete backup of the activity.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_bigbluebuttonbn_activity_task extends backup_activity_task {
    /**
     * Define (add) particular settings this activity can have.
     *
     * @return void
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have.
     *
     * @return void
     */
    protected function define_my_steps() {
        // Choice only has one structure step.
        $this->add_step(new backup_bigbluebuttonbn_activity_structure_step('bigbluebuttonbn_structure', 'bigbluebuttonbn.xml'));
    }

    /**
     * Code the transformations to perform in the activity in order to get transportable (encoded) links.
     *
     * @param string $content
     *
     * @return string
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot.'/mod/bigbluebuttonbn', '#');

        // Link to the list of bigbluebuttonbns.
        $pattern = '#('.$base."\/index.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@BIGBLUEBUTTONBNINDEX*$2@$', $content);

        // Link to bigbluebuttonbn view by moduleid.
        $pattern = '#('.$base."\/view.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@BIGBLUEBUTTONBNVIEWBYID*$2@$', $content);

        return $content;
    }
}
