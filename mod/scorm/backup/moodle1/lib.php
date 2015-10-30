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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod_scorm
 * @copyright  2011 Aparup Banerjee <nebgor@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Scorm conversion handler
 */
class moodle1_mod_scorm_handler extends moodle1_mod_handler {

    /** @var moodle1_file_manager */
    protected $fileman = null;

    /** @var int cmid */
    protected $moduleid = null;

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/SCORM does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path('scorm', '/MOODLE_BACKUP/COURSE/MODULES/MOD/SCORM',
                array(
                    'newfields' => array(
                        'whatgrade' => 0,
                        'scormtype' => 'local',
                        'sha1hash' => null,
                        'revision' => '0',
                        'forcecompleted' => 1,
                        'forcenewattempt' => 0,
                        'lastattemptlock' => 0,
                        'displayattemptstatus' => 1,
                        'displaycoursestructure' => 1,
                        'timeopen' => '0',
                        'timeclose' => '0',
                        'introformat' => '0',
                    ),
                    'renamefields' => array(
                        'summary' => 'intro'
                    )
                )
            ),
            new convert_path('scorm_sco', '/MOODLE_BACKUP/COURSE/MODULES/MOD/SCORM/SCOES/SCO')
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/SCORM
     * data available
     */
    public function process_scorm($data) {
        global $CFG;

        // get the course module id and context id
        $instanceid     = $data['id'];
        $currentcminfo  = $this->get_cminfo($instanceid);
        $this->moduleid = $currentcminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $data['intro']       = text_to_html($data['intro'], false, false, true);
            $data['introformat'] = FORMAT_HTML;
        }

        // get a fresh new file manager for this instance
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_scorm');

        // convert course files embedded into the intro
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['intro'], $this->fileman);

        // check 1.9 version where backup was created
        $backupinfo = $this->converter->get_stash('backup_info');
        if ($backupinfo['moodle_version'] < 2007110503) {
            // as we have no module version data, assume $currmodule->version <= $module->version
            // - fix data as the source 1.9 build hadn't yet at time of backing up.
            $data['grademethod'] = $data['grademethod']%10;
        }

        // update scormtype (logic is consistent as done in scorm/db/upgrade.php)
        $ismanifest = preg_match('/imsmanifest\.xml$/', $data['reference']);
        $iszippif = preg_match('/.(zip|pif)$/', $data['reference']);
        $isurl = preg_match('/^((http|https):\/\/|www\.)/', $data['reference']);
        if ($isurl) {
            if ($ismanifest) {
                $data['scormtype'] = 'external';
            } else if ($iszippif) {
                $data['scormtype'] = 'localtype';
            }
        }

        // migrate scorm package file
        $this->fileman->filearea = 'package';
        $this->fileman->itemid   = 0;
        $this->fileman->migrate_file('course_files/'.$data['reference']);

        // start writing scorm.xml
        $this->open_xml_writer("activities/scorm_{$this->moduleid}/scorm.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $this->moduleid,
            'modulename' => 'scorm', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('scorm', array('id' => $instanceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        $this->xmlwriter->begin_tag('scoes');

        return $data;
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/SCORM/SCOES/SCO
     * data available
     */
    public function process_scorm_sco($data) {
        $this->write_xml('sco', $data, array('/sco/id'));
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'scorm' path
     */
    public function on_scorm_end() {
        // close scorm.xml
        $this->xmlwriter->end_tag('scoes');
        $this->xmlwriter->end_tag('scorm');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/scorm_{$this->moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}
