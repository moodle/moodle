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
 * Specialised restore for format_tiles (based on the equivalent for format_topics
 *
 * @package   format_tiles
 * @category  backup
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Specialised backup for format_tiles
 *
 * Ensure that photo background images are included in course backups.
 *
 * @package   format_tiles
 * @category  backup
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_format_tiles_plugin extends backup_format_plugin {

    /**
     * Carries out some checks at start of course backup.
     *
     * @throws moodle_exception
     */
    public function define_course_plugin_structure() {
        $this->fail_if_course_includes_excess_sections();
    }

    /**
     * Returns the format information to attach to section element.
     */
    protected function define_section_plugin_structure() {
        $fileapiparams = \format_tiles\tile_photo::file_api_params();

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, $this->get_format_condition(), 'tiles');

        // Define each element separated.
        $tile = new backup_nested_element('tile', array('id'), array('tilephoto'));

        // Define sources.
        $tile->set_source_table('course_sections', array('id' => backup::VAR_SECTIONID));

        // Define file annotations.
        $tile->annotate_files($fileapiparams['component'], $fileapiparams['filearea'], null);

        $plugin->add_child($tile);
        return $plugin;
    }

    /**
     * Issue 45.
     * If incompatible Moodle 3.7 version of Tiles plugin was used in Moodle 3.9, incorrectly numbered sections may exist.
     * To avoid creating a empty sections on import or restore, check for incorrect sections and throw error if found.
     * @throws moodle_exception
     * @throws dml_exception
     */
    private function fail_if_course_includes_excess_sections() {
        global $DB;
        $courseid = $this->step->get_task()->get_courseid();
        $format = $DB->get_field('course', 'format', ['id' => $courseid]);
        if ($format !== 'tiles') {
            return;
        }
        $maxsectionsconfig = \format_tiles\course_section_manager::get_max_sections();
        $maxallowed = $maxsectionsconfig + 1;// We +1 as sec zero not counted.

        // If user is admin, when we throw error, we offer them a button to delete excess sections.
        $isadmin = has_capability('moodle/site:config', \context_system::instance());
        if ($isadmin) {
            $admintoolsurl = \format_tiles\course_section_manager::get_list_problem_courses_url();
            $admintoolsbutton = \html_writer::link(
                $admintoolsurl,
                get_string('checkforproblemcourses', 'format_tiles'),
                array('class' => 'btn btn-secondary ml-2')
            );
        } else {
            $admintoolsurl = '';
            $admintoolsbutton = '';
        }

        // Get the course sections from the database for the course we are backing up and check them.
        $countsections = $DB->get_field('course_sections', 'COUNT(id)',  array('course' => $courseid));
        if ($countsections && $countsections > $maxallowed * 5) {
            // Course has a very high number of sections, so fail early as probably en error and we avoid further work.
            $a = new stdClass();
            $a->numsections = $countsections;
            $a->maxallowed = $maxsectionsconfig;
            \core\notification::error(get_string('restoretoomanysections', 'format_tiles', $a) . $admintoolsbutton);
            throw new moodle_exception('backupfailed', 'format_tiles', $admintoolsurl);
        }

        $sections = $DB->get_records('course_sections', array('course' => $courseid), 'id ASC, section ASC',
            'id, section, name', 0, $maxallowed * 5);

        $totalincluded = 0;
        foreach ($sections as $section) {
            // Is the section to be included in the backup or has the user excluded it (unchecked box)?  Ignore if excluded.
            $settingname = 'section_' . $section->id . '_included';
            $included = $this->get_setting_value($settingname);
            if ($included) {
                if ($section->section > $maxallowed) {
                    // Allowing this section would mean we had some secs with sec numbers too high - disallow.
                    $a = new stdClass();
                    $a->sectionnum = $section->section;
                    $a->maxallowed = $maxsectionsconfig;
                    \core\notification::error(get_string('restoreincorrectsections', 'format_tiles', $a) . $admintoolsbutton);
                    throw new moodle_exception('backupfailed', 'format_tiles', $admintoolsurl);
                } else {
                    $totalincluded++;
                    if ($totalincluded > $maxallowed) {
                        // Allowing this section to go in the backup would mean we have too many secs - disallow.
                        $a = new stdClass();
                        $a->numsections = $totalincluded;
                        $a->maxallowed = $maxsectionsconfig;
                        \core\notification::error(get_string('restoretoomanysections', 'format_tiles', $a) . $admintoolsbutton);
                        throw new moodle_exception('backupfailed', 'format_tiles', $admintoolsurl);
                    }
                }
            }
        }
    }
}
