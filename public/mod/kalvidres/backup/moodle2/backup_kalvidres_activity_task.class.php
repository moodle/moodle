<?php
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
 * Kaltura video resource backup activity tasks script.
 *
 * @package    mod_kalvidres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once($CFG->dirroot.'/mod/kalvidres/backup/moodle2/backup_kalvidres_stepslib.php');
require_once($CFG->dirroot.'/mod/kalvidres/backup/moodle2/backup_kalvidres_settingslib.php');

/**
 * kalvidres backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_kalvidres_activity_task extends backup_activity_task {
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
        // Choice only has one structure step
        $this->add_step(new backup_kalvidres_activity_structure_step('kalvidres_structure', 'kalvidres.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of kalvidress
        $search="/(".$base."\/mod\/kalvidres\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@KALVIDRESINDEX*$2@$', $content);

        // Link to kalvidres view by moduleid
        $search="/(".$base."\/mod\/kalvidres\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@KALVIDRESVIEWBYID*$2@$', $content);

        return $content;
    }
}
