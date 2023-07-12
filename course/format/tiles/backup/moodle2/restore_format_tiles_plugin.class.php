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
 * @copyright 2017 David Watson {@link http://evolutioncode.uk}, Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('FILTER_NONE', 0);
define('FILTER_NUMBERS_ONLY', 1);
define('FILTER_OUTCOMES_ONLY', 2);
define('FILTER_OUTCOMES_AND_NUMBERS', 3);

/**
 * Specialised restore for format_tiles
 *
 * Processes 'numsections' from the old backup files and hides sections that used to be "orphaned".
 * Also handles restoring tile background image files from the backup archive to the tiles.
 *
 * @package   format_tiles
 * @category  backup
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}, Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_format_tiles_plugin extends restore_format_plugin {

    /** @var int */
    protected $originalnumsections = 0;

    /**
     * Checks if backup file was made on Moodle before 3.3 and we should respect the 'numsections'
     * and potential "orphaned" sections in the end of the course.
     *
     * @return bool
     */
    protected function need_restore_numsections() {
        $backupinfo = $this->step->get_task()->get_info();
        $backuprelease = $backupinfo->backup_release;
        return version_compare($backuprelease, '3.3', 'lt');
    }

    /**
     * Carries out some checks at start of course restore.
     *
     * @return restore_path_element[]
     * @return restore_path_element[]
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function define_course_plugin_structure() {
        global $DB;
        // Since this method is executed before the restore we can do some pre-checks here.
        $this->fail_if_course_includes_excess_sections();

        // In case of merging backup into existing course find the current number of sections.
        $target = $this->step->get_task()->get_target();
        if (($target == backup::TARGET_CURRENT_ADDING || $target == backup::TARGET_EXISTING_ADDING) &&
            $this->need_restore_numsections()) {
            $maxsection = $DB->get_field_sql(
                'SELECT max(section) FROM {course_sections} WHERE course = ?',
                [$this->step->get_task()->get_courseid()]);
            $this->originalnumsections = (int)$maxsection;
        }

        // Dummy path element is needed in order for after_restore_course() to be called.
        return [new restore_path_element('dummy_course', $this->get_pathfor('/dummycourse'))];
    }

    /**
     * Issue 45.
     * If incompatible Moodle 3.7 version of Tiles plugin was used in Moodle 3.9, incorrectly numbered sections may exist.
     * To avoid creating a empty sections on import or restore, check for incorrect sections and throw error if found.
     * @throws moodle_exception
     */
    private function fail_if_course_includes_excess_sections() {
        $backupinfo = $this->step->get_task()->get_info();
        if (!isset($backupinfo->original_course_format) || $backupinfo->original_course_format !== 'tiles') {
            return;
        }
        $maxallowed = \format_tiles\course_section_manager::get_max_sections();

        // Get the sections from the backup and check them one by one.
        $totalincluded = 0;
        foreach ($backupinfo->sections as $section) {
            // Is the section included or has the user excluded it (unchecked box)?  Ignore if excluded.
            $sectionid = $section->sectionid;
            $included = $this->get_setting_value('section_' . $sectionid . '_included');
            if ($included) {
                $sectionnum = is_numeric($section->title) ? (int)$section->title : false;
                if (($sectionnum && $sectionnum > $maxallowed + 1) || $totalincluded > $maxallowed) {
                    // Allowing this section would mean we had some secs with sec numbers too high - disallow.
                    $a = new stdClass();
                    $a->sectionnum = $sectionnum;
                    $a->maxallowed = $maxallowed;
                    \core\notification::error(get_string('restoreincorrectsections', 'format_tiles', $a));
                    throw new moodle_exception('restoreincorrectsections', 'format_tiles', '', $a);
                } else {
                    $totalincluded++;
                    if ($totalincluded > $maxallowed + 1) {
                        // Allowing this section would mean we have too many secs - disallow.
                        $a = new stdClass();
                        $a->numsections = $totalincluded;
                        $a->maxallowed = $maxallowed;
                        \core\notification::error(get_string('restoretoomanysections', 'format_tiles', $a));
                        throw new moodle_exception('restoretoomanysections', 'format_tiles', '', $a);
                    }
                }
            }
        }
    }

    /**
     * Check the destination course does not have a section number more than the max.
     * If it does, we cannot allow the restore.
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    private function check_destination_course_section_count() {
        global $DB, $SESSION;
        $maxallowed = \format_tiles\course_section_manager::get_max_sections();
        $courseid = $this->step->get_task()->get_courseid();
        $sessionvar = 'restore_dest_check_' . $courseid;
        if (isset($SESSION->$sessionvar) && $SESSION->$sessionvar > strtotime('2 minutes ago')) {
            // We've already done this very recently (probably in the same restore process) so don't need to do it now.
            return true;
        }
        $SESSION->$sessionvar = time();

        $maxsection = $DB->get_field('course_sections', 'MAX(section)',  array('course' => $courseid));

        if ($maxsection && $maxsection > $maxallowed + 1) {

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
            $a = new stdClass();
            $a->sectionnum = $maxsection;
            $a->maxallowed = $maxallowed;
            \core\notification::error(get_string('restoreincorrectsections', 'format_tiles', $a) . $admintoolsbutton);
            throw new moodle_exception('restorefailed', 'format_tiles', $admintoolsurl, $a);
        } else {
            return true;
        }
    }

    /**
     * Ensure that we include photo background images in our restore structure.
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function define_section_plugin_structure() {

        // We have to put this here as otherwise we don't seem to have a way of making sure it is done on import as well as restore.
        $this->check_destination_course_section_count();

        $this->add_related_files('format_tiles', 'tilephoto', null);
        // Dummy path element is needed in order for after_restore_section() to be called.
        return [new restore_path_element('dummy_section', $this->get_pathfor('/dummysection'))];
    }

    /**
     * Dummy process method
     */
    public function process_dummy_course() {

    }

    /**
     * Dummy process method
     */
    public function process_dummy_section() {

    }

    /**
     * Executed after course restore is complete
     *
     * This method is only executed if course configuration was overridden
     * @return bool|stored_file
     * @throws dml_exception
     * @throws file_exception
     * @throws stored_file_creation_exception
     */
    public function after_restore_section() {

        global $DB;
        $data = $this->connectionpoint->get_data();
        if (isset($data['path']) && $data['path'] = '/section' && isset($data['tags']['id'])) {
            $oldsectionid = $data['tags']['id'];
            $oldsectionnum = $data['tags']['number'];

            $newcourseid = $this->step->get_task()->get_courseid();
            $newsectionid = $DB->get_field('course_sections', 'id', array(
                    'course' => $newcourseid,
                    'section' => $oldsectionnum
                )
            );
            if ($newsectionid) {
                return self::update_file_record(
                    $newcourseid, context_course::instance($newcourseid)->id, $oldsectionid, $newsectionid
                );
            }
        }
        return false;
    }

    /**
     * Executed after course restore is complete
     *
     * This method is only executed if course configuration was overridden
     * @throws dml_exception
     */
    public function after_restore_course() {
        global $DB;
        // This function will be executed on every restore, whether or not the restored course uses this format.
        // So before doing anything else, check if the restored course is using format_tiles or not.
        $backupinfo = $this->step->get_task()->get_info();
        if ($backupinfo->original_course_format !== 'tiles') {
            // Backup is from another course format, so we bail out (the other format will take care of everything).
            // Moving this here fixes issue #4.
            return;
        }
        $currentfilterbarsetting = $DB->get_record(
            'course_format_options',
            array('name' => 'displayfilterbar', 'format' => 'tiles', 'courseid' => $this->step->get_task()->get_courseid())
        );
        if ($currentfilterbarsetting && $currentfilterbarsetting->value == FILTER_OUTCOMES_ONLY
            || $currentfilterbarsetting->value == FILTER_OUTCOMES_AND_NUMBERS) {
            // If the new course has the filter bar set to use outcomes then switch it.
            // Tile outcomes will not work correctly in the new course as they include ids from the old course.
            // This is a temporary solution until the tile outcomes code can be refactored not to use outcome ids.
            $newrecord = new stdClass();
            $newrecord->id = $currentfilterbarsetting->id;
            if ($currentfilterbarsetting->value == FILTER_OUTCOMES_ONLY) {
                $newrecord->value = FILTER_NONE;
                $DB->update_record('course_format_options', $newrecord);
            } else if ($currentfilterbarsetting->value == FILTER_OUTCOMES_AND_NUMBERS) {
                $newrecord->value = FILTER_NUMBERS_ONLY;
                $DB->update_record('course_format_options', $newrecord);
            }

            // Delete references to tile outcomes under section format options (now incorrect in restored course).
            // Users will have to set out up outcomes in new course for now if they want to.
            $DB->delete_records(
                'course_format_options',
                array('name' => 'tileoutcomeid', 'format' => 'tiles', 'courseid' => $this->step->get_task()->get_courseid())
            );
        }

        // The name of course format option "defaulttileicon" for a course used to be "defaulttiletopleftdisplay".
        // Before this was changed for clarity in summer 2018 release, so change it if present in the backup if present.
        // Same for the topic level option "tiletopleftthistile" which becomes "tileicon".
        $courseid = $this->step->get_task()->get_courseid();
        $DB->set_field('course_format_options', 'name', 'defaulttileicon',
            array('format' => 'tiles', 'name' => 'defaulttiletopleftdisplay', 'courseid' => $courseid));
        $DB->set_field('course_format_options', 'name', 'tileicon',
            array('format' => 'tiles', 'name' => 'tiletopleftthistile', 'courseid' => $courseid));

        // Old versions of this plugin used to refer to "course default" for each icon if the user had not selected one.
        // This no longer applies so delete them if present.
        $DB->delete_records_select(
            'course_format_options',
            "format  = 'tiles' AND name = 'tileicon' AND value = 'course default' AND courseid = :courseid",
            array("courseid" => $courseid)
        );

        $data = $this->connectionpoint->get_data();
        if (!isset($data['tags']['numsections']) || !$this->need_restore_numsections()) {
            // Backup file does not even have 'numsections' or was made in Moodle 3.3+, we don't need to process 'numsections'.
            return;
        }

        $numsections = (int)$data['tags']['numsections'];
        // Check each section from the backup file.
        // If it was "orphaned" in the original course, mark it as hidden.
        // This will leave all activities in it visible and available just as it was in the original course.
        // Exception is when we restore with merging and the course already had a section with this section number.
        // In this case we don't modify the visibility.
        foreach ($backupinfo->sections as $key => $section) {
            if ($this->step->get_task()->get_setting_value($key . '_included')) {
                $sectionnum = (int)$section->title;
                if ($sectionnum > $numsections && $sectionnum > $this->originalnumsections) {
                    $DB->execute("UPDATE {course_sections} SET visible = 0 WHERE course = ? AND section = ?",
                        [$this->step->get_task()->get_courseid(), $sectionnum]);
                }
            }
        }

        // While we are here, delete any temp tile photo files (we don't expect any but just in case).
        $fs = get_file_storage();
        $fs->delete_area_files(context_course::instance($courseid)->id, 'format_tiles', 'temptilephoto');
    }

    /**
     * Tile image file record needs updating to have section ids from new section not old.
     * Restore process will have created the file in files table but given it old section id.
     * This handles it and section ids from the new sections end up in {files} table.
     * @param int $newcourseid
     * @param int $contextid
     * @param int $oldsectionid
     * @param int $newsectionid
     * @return bool|stored_file
     * @throws dml_exception
     * @throws file_exception
     * @throws stored_file_creation_exception
     */
    private static function update_file_record($newcourseid, $contextid, $oldsectionid, $newsectionid) {
        global $DB;
        $record = $DB->get_record_select(
            'files',
            "contextid = :coursecontextid AND component = 'format_tiles'
            AND filearea = 'tilephoto' AND filepath = '/tilephoto/'
            AND itemid = :oldsectionid AND filesize > 0",
            array ('coursecontextid' => $contextid, 'oldsectionid' => $oldsectionid)
        );
        if ($record) {
            $fs = get_file_storage();
            $record->itemid = $newsectionid;
            $oldfile = $fs->get_file_by_id($record->id);
            $newfile = false;
            if ($oldfile) {
                // We have a file in the table with the old section id.
                // However if we are merging a backup into an existing course, the new section may already have a photo too.
                // We have to delete it if it does, as well as delete the old sec id version.
                \format_tiles\tile_photo::delete_file_from_ids($newcourseid, $newsectionid);
                $newfile = $fs->create_file_from_storedfile($record, $oldfile);
                $oldfile->delete();
            }
            return $newfile;
        }
        return false;
    }
}
