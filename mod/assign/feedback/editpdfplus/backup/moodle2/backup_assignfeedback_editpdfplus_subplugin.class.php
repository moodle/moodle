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
 * This file contains the backup code for the feedback_editpdfplus plugin.
 *
 * Provides the information to backup feedback pdf annotations.
 *
 * This just adds its fileareas to the annotations and the comments and annotation data.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/backup/moodle2/backup_assignfeedback_editpdf_subplugin.class.php by Damyon Wiese.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class backup_assignfeedback_editpdfplus_subplugin extends backup_subplugin {

    const GRADEID = 'gradeid';

    /**
     * Returns the subplugin information to attach to feedback element
     * @return backup_subplugin_element
     */
    protected function define_grade_subplugin_structure() {

        // Create XML elements.
        $subplugin = $this->get_subplugin_element();
        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
        $subpluginelementfiles = new backup_nested_element('feedback_editpdfplus_files', null, array(self::GRADEID));
        $subpluginelementannotations = new backup_nested_element('feedback_editpdfplus_annotations');
        $subpluginelementannotation = new backup_nested_element(
                'feedback_editpdfplus_annotation', null, array(
            self::GRADEID,
            'pageno',
            'x',
            'y',
            'endx',
            'endy',
            'cartridgex',
            'cartridgey',
            'path',
            'toolid',
            'textannot',
            'colour',
            'draft',
            'answerrequested',
            'studentanswer',
            'studentstatus',
            'displaylock',
            'displayrotation',
            'borderstyle',
            'parent_annot'
                )
        );
        $subpluginelementrotation = new backup_nested_element('feedback_editpdfplus_rotation');
        $subpluginelementpagerotation = new backup_nested_element('feedback_editpdfplus_pagerotation', null,
                array('gradeid', 'pageno', 'pathnamehash', 'isrotated', 'degree'));

        // Connect XML elements into the tree.
        $subplugin->add_child($subpluginwrapper);
        $subpluginelementannotations->add_child($subpluginelementannotation);
        $subpluginelementrotation->add_child($subpluginelementpagerotation);
        $subpluginwrapper->add_child($subpluginelementfiles);
        $subpluginwrapper->add_child($subpluginelementannotations);
        $subpluginwrapper->add_child($subpluginelementrotation);

        // Set source to populate the data.
        $subpluginelementfiles->set_source_sql('SELECT id AS gradeid from {assign_grades} where id = :' . self::GRADEID, array(self::GRADEID => backup::VAR_PARENTID));
        $subpluginelementannotation->set_source_table('assignfeedback_editpp_annot', array(self::GRADEID => backup::VAR_PARENTID));
        $subpluginelementpagerotation->set_source_table('assignfeedback_editpp_rot', array(self::GRADEID => backup::VAR_PARENTID));
        // We only need to backup the files in the final pdf area, and the readonly page images - the others can be regenerated.
        $subpluginelementfiles->annotate_files('assignfeedback_editpdfplus', \assignfeedback_editpdfplus\document_services::FINAL_PDF_FILEAREA, self::GRADEID);
        $subpluginelementfiles->annotate_files('assignfeedback_editpdfplus', \assignfeedback_editpdfplus\document_services::PAGE_IMAGE_READONLY_FILEAREA, self::GRADEID);
        return $subplugin;
    }

}
