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
 * @package mod_dataform
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/mod/dataform/backup/moodle2/backup_dataform_stepslib.php');

/**
 * data backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_dataform_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have.
     */
    protected function define_my_settings() {
        global $SESSION;
        // No particular settings for this activity.

        // For preseting get root settings from SESSION and adjust root task.
        if (isset($SESSION->{"dataform_{$this->moduleid}_preset"})) {
            list($users, $anon) = explode(' ', $SESSION->{"dataform_{$this->moduleid}_preset"});
            list($roottask, , ) = $this->plan->get_tasks();
            // Set users setting.
            $userssetting = $roottask->get_setting('users');
            $userssetting->set_value($users);
            $this->plan->get_setting('users')->set_value($users);
            // Disable dependencies if needed.
            if (!$users) {
                $dependencies = $userssetting->get_dependencies();
                foreach ($dependencies as &$dependent) {
                    $dependentsetting = $dependent->get_dependent_setting();
                    $dependentsetting->set_value(0);
                }
            }
            // Set anonymize.
            $anonsetting = $roottask->get_setting('anonymize');
            $anonsetting->set_value($anon);
            $this->plan->get_setting('anonymize')->set_value($anon);

        }
    }

    /**
     * Define (add) particular steps this activity can have.
     */
    protected function define_my_steps() {
        // Dataform only has one structure step.
        $this->add_step(new backup_dataform_activity_structure_step('dataform_structure', 'dataform.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links.
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Index: id.
        $search = "/(".$base."\/mod\/dataform\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@DFINDEX*$2@$', $content);

        // View/embed: d, view, filter.
        $search = array(
            "/(".$base."\/mod\/dataform\/view.php\?d\=)([0-9]+)\&(amp;)view\=([0-9]+)\&(amp;)filter\=([0-9]+)/",
            "/(".$base."\/mod\/dataform\/embed.php\?d\=)([0-9]+)\&(amp;)view\=([0-9]+)\&(amp;)filter\=([0-9]+)/"
        );
        $replacement = array('$@DFVIEWVIEWFILTER*$2*$4*$6@$', '$@DFEMBEDVIEWFILTER*$2*$4*$6@$');
        $content = preg_replace($search, $replacement, $content);

        // View/embed: d, view.
        $search = array(
            "/(".$base."\/mod\/dataform\/view.php\?d\=)([0-9]+)\&(amp;)view\=([0-9]+)/",
            "/(".$base."\/mod\/dataform\/embed.php\?d\=)([0-9]+)\&(amp;)view\=([0-9]+)/"
        );
        $replacement = array('$@DFVIEWVIEW*$2*$4@$', '$@DFEMBEDVIEW*$2*$4@$');
        $content = preg_replace($search, $replacement, $content);

        // View/embed: d, eid.
        $search = array(
            "/(".$base."\/mod\/dataform\/view.php\?d\=)([0-9]+)\&(amp;)eid\=([0-9]+)/",
            "/(".$base."\/mod\/dataform\/embed.php\?d\=)([0-9]+)\&(amp;)eid\=([0-9]+)/"
        );
        $replacement = array('$@DFVIEWENTRY*$2*$4@$', '$@DFEMBEDENTRY*$2*$4@$');
        $content = preg_replace($search, $replacement, $content);

        // View/embed: id.
        $search = array(
            "/(".$base."\/mod\/dataform\/view.php\?id\=)([0-9]+)/",
            "/(".$base."\/mod\/dataform\/embed.php\?id\=)([0-9]+)/"
        );
        $replacement = array('$@DFVIEWBYID*$2@$', '$@DFEMBEDBYID*$2@$');
        $content = preg_replace($search, $replacement, $content);

        // View/embed: d.
        $search = array(
            "/(".$base."\/mod\/dataform\/view.php\?d\=)([0-9]+)/",
            "/(".$base."\/mod\/dataform\/embed.php\?d\=)([0-9]+)/"
        );
        $replacement = array('$@DFVIEWBYD*$2@$', '$@DFEMBEDBYD*$2@$');
        $content = preg_replace($search, $replacement, $content);

        return $content;
    }
}
