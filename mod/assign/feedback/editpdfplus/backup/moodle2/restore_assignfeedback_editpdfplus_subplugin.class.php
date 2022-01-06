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
 * This file contains the restore code for the feedback_editpdfplus plugin.
 *
 * Restore subplugin class.
 *
 * Provides the necessary information needed
 * to restore one assign_feedback subplugin.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/backup/moodle2/restore_assignfeedback_editpdfplus_subplugin.class.php by Damyon Wiese.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class restore_assignfeedback_editpdfplus_subplugin extends restore_subplugin {

    const GRADE = 'grade';

    /**
     * Returns the paths to be handled by the subplugin at assignment level
     * @return array
     */
    protected function define_grade_subplugin_structure() {

        $paths = array();

        // We used get_recommended_name() so this works.
        // The files node is a placeholder just containing gradeid so we can restore files once per grade.
        $elename = $this->get_namefor('files');
        $elepath = $this->get_pathfor('/feedback_editpdfplus_files');
        $paths[] = new restore_path_element($elename, $elepath);

        // Now we have the list of comments and annotations per grade.
        $elename = $this->get_namefor('annotation');
        $elepath = $this->get_pathfor('/feedback_editpdfplus_annotations/feedback_editpdfplus_annotation');
        $paths[] = new restore_path_element($elename, $elepath);
        
        // Rotation details.
        $elename = $this->get_namefor('pagerotation');
        $elepath = $this->get_pathfor('/feedback_editpdfplus_rotation/pagerotation');
        $paths[] = new restore_path_element($elename, $elepath);


        return $paths;
    }

    /**
     * Processes one feedback_editpdfplus_files element
     * @param mixed $data
     */
    public function process_assignfeedback_editpdfplus_files($data) {
        $data = (object) $data;

        // In this case the id is the old gradeid which will be mapped.
        $this->add_related_files('assignfeedback_editpdfplus', \assignfeedback_editpdfplus\document_services::FINAL_PDF_FILEAREA, self::GRADE, null, $data->gradeid);
        $this->add_related_files('assignfeedback_editpdfplus', \assignfeedback_editpdfplus\document_services::PAGE_IMAGE_READONLY_FILEAREA, self::GRADE, null, $data->gradeid);
    }

    /**
     * Processes one feedback_editpdfplus_annotations/annotation element
     * @param mixed $data
     */
    public function process_assignfeedback_editpdfplus_annotation($data) {
        global $DB;

        $data = (object) $data;
        $oldgradeid = $data->gradeid;
        // The mapping is set in the restore for the core assign activity
        // when a grade node is processed.
        $data->gradeid = $this->get_mappingid(self::GRADE, $data->gradeid);

        $DB->insert_record('assignfeedback_editpp_annot', $data);
    }

    /**
     * Processes one /feedback_editpdfplus_rotation/pagerotation element
     * @param mixed $data
     */
    public function process_assignfeedback_editpdfplus_pagerotation($data) {
        global $DB;
        $data = (object)$data;
        $oldgradeid = $data->gradeid;
        $data->gradeid = $this->get_mappingid('grade', $oldgradeid);
        $DB->insert_record('assignfeedback_editpp_rot', $data);
    }
}
