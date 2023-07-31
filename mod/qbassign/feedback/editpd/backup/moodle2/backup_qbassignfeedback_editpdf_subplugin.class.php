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
 * This file contains the backup code for the feedback_editpd plugin.
 *
 * @package   qbassignfeedback_editpd
 * @copyright 2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Provides the information to backup feedback pdf annotations.
 *
 * This just adds its fileareas to the annotations and the comments and annotation data.
 *
 * @package   qbassignfeedback_editpd
 * @copyright 2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qbassignfeedback_editpd_subplugin extends backup_subplugin {

    /**
     * Returns the subplugin information to attach to feedback element
     * @return backup_subplugin_element
     */
    protected function define_grade_subplugin_structure() {

        // Create XML elements.
        $subplugin = $this->get_subplugin_element();
        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
        $subpluginelementfiles = new backup_nested_element('feedback_editpd_files', null, array('gradeid'));
        $subpluginelementannotations = new backup_nested_element('feedback_editpd_annotations');
        $subpluginelementannotation = new backup_nested_element('annotation', null, array('gradeid', 'pageno', 'type', 'x', 'y', 'endx', 'endy', 'colour', 'path', 'draft'));
        $subpluginelementcomments = new backup_nested_element('feedback_editpd_comments');
        $subpluginelementcomment = new backup_nested_element('comment', null, array('gradeid', 'pageno', 'x', 'y', 'width', 'rawtext', 'colour', 'draft'));
        $subpluginelementrotation = new backup_nested_element('feedback_editpd_rotation');
        $subpluginelementpagerotation = new backup_nested_element('pagerotation', null,
            array('gradeid', 'pageno', 'pathnamehash', 'isrotated', 'degree'));

        // Connect XML elements into the tree.
        $subplugin->add_child($subpluginwrapper);
        $subpluginelementannotations->add_child($subpluginelementannotation);
        $subpluginelementcomments->add_child($subpluginelementcomment);
        $subpluginelementrotation->add_child($subpluginelementpagerotation);
        $subpluginwrapper->add_child($subpluginelementfiles);
        $subpluginwrapper->add_child($subpluginelementannotations);
        $subpluginwrapper->add_child($subpluginelementcomments);
        $subpluginwrapper->add_child($subpluginelementrotation);

        // Set source to populate the data.
        $subpluginelementfiles->set_source_sql('SELECT id AS gradeid from {qbassign_grades} where id = :gradeid', array('gradeid' => backup::VAR_PARENTID));
        $subpluginelementannotation->set_source_table('qbassignfeedback_editpd_anno', array('gradeid' => backup::VAR_PARENTID));
        $subpluginelementcomment->set_source_table('qbassignfeedback_editpd_cmnt', array('gradeid' => backup::VAR_PARENTID));
        $subpluginelementpagerotation->set_source_table('qbassignfeedback_editpd_rot', array('gradeid' => backup::VAR_PARENTID));
        // We only need to backup the files in the final pdf area, and the readonly page images - the others can be regenerated.
        $subpluginelementfiles->annotate_files('qbassignfeedback_editpd',
            \qbassignfeedback_editpd\document_services::FINAL_PDF_FILEAREA, 'gradeid');
        $subpluginelementfiles->annotate_files('qbassignfeedback_editpd',
            \qbassignfeedback_editpd\document_services::PAGE_IMAGE_READONLY_FILEAREA, 'gradeid');
        $subpluginelementfiles->annotate_files('qbassignfeedback_editpd', 'stamps', 'gradeid');
        return $subplugin;
    }

}
