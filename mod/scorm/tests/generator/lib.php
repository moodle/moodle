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
 * mod_scorm data generator.
 *
 * @package    mod_scorm
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_scorm data generator class.
 *
 * @package    mod_scorm
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_scorm_generator extends testing_module_generator {

    public function create_instance($record = null, array $options = null) {
        global $CFG, $USER;
        require_once($CFG->dirroot.'/mod/scorm/lib.php');
        require_once($CFG->dirroot.'/mod/scorm/locallib.php');
        $cfgscorm = get_config('scorm');

        // Add default values for scorm.
        $record = (array)$record + array(
            'scormtype' => SCORM_TYPE_LOCAL,
            'packagefile' => '',
            'packageurl' => '',
            'updatefreq' => SCORM_UPDATE_NEVER,
            'popup' => 0,
            'width' => $cfgscorm->framewidth,
            'height' => $cfgscorm->frameheight,
            'skipview' => $cfgscorm->skipview,
            'hidebrowse' => $cfgscorm->hidebrowse,
            'displaycoursestructure' => $cfgscorm->displaycoursestructure,
            'hidetoc' => $cfgscorm->hidetoc,
            'nav' => $cfgscorm->nav,
            'navpositionleft' => $cfgscorm->navpositionleft,
            'navpositiontop' => $cfgscorm->navpositiontop,
            'displayattemptstatus' => $cfgscorm->displayattemptstatus,
            'timeopen' => 0,
            'timeclose' => 0,
            'grademethod' => GRADESCOES,
            'maxgrade' => $cfgscorm->maxgrade,
            'maxattempt' => $cfgscorm->maxattempt,
            'whatgrade' => $cfgscorm->whatgrade,
            'forcenewattempt' => $cfgscorm->forcenewattempt,
            'lastattemptlock' => $cfgscorm->lastattemptlock,
            'forcecompleted' => $cfgscorm->forcecompleted,
            'masteryoverride' => $cfgscorm->masteryoverride,
            'auto' => $cfgscorm->auto,
            'displayactivityname' => $cfgscorm->displayactivityname
        );
        if (empty($record['packagefilepath'])) {
            $record['packagefilepath'] = $CFG->dirroot.'/mod/scorm/tests/packages/singlescobasic.zip';
        }

        // The 'packagefile' value corresponds to the draft file area ID. If not specified, create from packagefilepath.
        if (empty($record['packagefile']) && $record['scormtype'] === SCORM_TYPE_LOCAL) {
            if (!isloggedin() || isguestuser()) {
                throw new coding_exception('Scorm generator requires a current user');
            }
            if (!file_exists($record['packagefilepath'])) {
                throw new coding_exception("File {$record['packagefilepath']} does not exist");
            }
            $usercontext = context_user::instance($USER->id);

            // Pick a random context id for specified user.
            $record['packagefile'] = file_get_unused_draft_itemid();

            // Add actual file there.
            $filerecord = array('component' => 'user', 'filearea' => 'draft',
                    'contextid' => $usercontext->id, 'itemid' => $record['packagefile'],
                    'filename' => basename($record['packagefilepath']), 'filepath' => '/');
            $fs = get_file_storage();
            $fs->create_file_from_pathname($filerecord, $record['packagefilepath']);
        }

        return parent::create_instance($record, (array)$options);
    }
}
