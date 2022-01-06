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
 * This file contains the editor class for the assignfeedback_editpdfplus plugin
 *
 * This class performs crud operations on comments and annotations from a page of a response.
 *
 * No capability checks are done - they should be done by the calling class.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/classes/page_editor.php by Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdfplus;

use assignfeedback_editpdfplus\bdd\type_tool;
use assignfeedback_editpdfplus\bdd\tool;
use assignfeedback_editpdfplus\bdd\tool_generic;
use assignfeedback_editpdfplus\bdd\axis;
use assignfeedback_editpdfplus\bdd\annotation;

class page_editor {

    const BDDTABLEOOL = "assignfeedback_editpp_tool";
    const BDDTABLETOOLTYPE = "assignfeedback_editpp_typet";
    const BDDTABLEANNOTATION = "assignfeedback_editpp_annot";
    const BDDTABLEAXIS = "assignfeedback_editpp_axis";
    const CONTEXTID = "contextid";
    const GRADEID = "gradeid";
    const DRAFLIB = "draft";
    const AXISGENERIC = 0;

    /**
     * Get all tools for a page.
     * @param int $contextid
     * @param int $axis
     * @return tool[]
     */
    public static function get_tools($contextidlist) {
        global $DB;

        $typeToolsRaw = self::get_typetools(null);
        $typeTools = array();
        foreach ($typeToolsRaw as $typeTool) {
            $typeTools[$typeTool->id] = $typeTool;
        }

        $tools = array();
        if ($contextidlist) {
            $records = $DB->get_records_list(self::BDDTABLEOOL, self::CONTEXTID, $contextidlist);
        } else {
            $records = $DB->get_records(self::BDDTABLEOOL);
        }
        foreach ($records as $record) {
            $tooltmp = null;
            if ($record->axis == self::AXISGENERIC) {
                $tooltmp = new tool_generic($record);
            } else {
                $tooltmp = new tool($record);
            }
            $tooltmp->typeObject = $typeTools[$tooltmp->type];
            array_push($tools, $tooltmp);
        }
        usort($tools, function($a, $b) {
            $al = $a->order_tool;
            $bl = $b->order_tool;
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;
        });
        return $tools;
    }

    /**
     * Get all tools for a given axis.
     * @param int $axisid
     * @return tool[]
     */
    public static function get_tools_by_axis($axisid) {
        global $DB;

        $typeToolsRaw = self::get_typetools(null);
        $typeTools = array();
        foreach ($typeToolsRaw as $typeTool) {
            $typeTools[$typeTool->id] = $typeTool;
        }

        $tools = array();
        $records = $DB->get_records(self::BDDTABLEOOL, array('axis' => $axisid));
        foreach ($records as $record) {
            $tooltmp = null;
            if ($record->axis == self::AXISGENERIC) {
                $tooltmp = new tool_generic($record);
            } else {
                $tooltmp = new tool($record);
            }
            $tooltmp->typeObject = $typeTools[$tooltmp->type];
            array_push($tools, $tooltmp);
        }
        usort($tools, function($a, $b) {
            $al = $a->order_tool;
            $bl = $b->order_tool;
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;
        });
        return $tools;
    }

    /**
     * Get a single tool by id.
     * @param int $toolid
     * @return tool or false
     */
    public static function get_tool($toolid) {
        global $DB;
        $record = $DB->get_record(self::BDDTABLEOOL, array('id' => $toolid), '*', IGNORE_MISSING);
        if ($record) {
            $tool = new tool($record);
            $typetool = self::get_type_tool($tool->type);
            $tool->typeObject = $typetool;
            return $tool;
        }
        return false;
    }

    /**
     * Get the tool type with id tooltypeid.
     * @param int $tooltypeid
     * @return type_tool
     */
    public static function get_type_tool($tooltypeid) {
        global $DB;
        $record = $DB->get_record(self::BDDTABLETOOLTYPE, array('id' => $tooltypeid));
        if ($record) {
            $newTypeTool = new type_tool($record);
            return page_editor::custom_type_tool($newTypeTool);
        }
        return false;
    }

    /**
     * Get all the type tools.
     * @param array $contextidlist
     * @return type_tool
     */
    public static function get_typetools($contextidlist) {
        global $DB;
        $typetools = array();
        if ($contextidlist) {
            $records = $DB->get_records_list(self::BDDTABLETOOLTYPE, self::CONTEXTID, $contextidlist);
        } else {
            $records = $DB->get_records(self::BDDTABLETOOLTYPE);
        }
        foreach ($records as $record) {
            $newToolType = new type_tool($record);
            array_push($typetools, page_editor::custom_type_tool($newToolType));
        }
        return $typetools;
    }

    /**
     * Get all axis for contextid.
     * @param array $contextidlist optional
     * @return axis[]
     */
    public static function get_axis($contextidlist) {
        global $DB;
        $axis = array();
        if ($contextidlist) {
            $records = $DB->get_records_list('assignfeedback_editpp_axis', self::CONTEXTID, $contextidlist);
        } else {
            $records = $DB->get_records('assignfeedback_editpp_axis');
        }
        foreach ($records as $record) {
            array_push($axis, new axis($record));
        }
        usort($axis, function($a, $b) {
            $al = $a->order_axis;
            $bl = $b->order_axis;
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;
        });
        return $axis;
    }

    /**
     * Get an axis given by its id.
     * @param int $axisid
     * @return axis
     */
    public static function get_axis_by_id($axisid) {
        global $DB;
        $record = $DB->get_record(self::BDDTABLEAXIS, array('id' => $axisid), '*', IGNORE_MISSING);
        if ($record) {
            return new axis($record);
        }
        return null;
    }

    /**
     * Get all annotations for a page.
     * @param int $gradeid
     * @param int $pageno
     * @param bool $draft
     * @return bdd\annotation[]
     */
    public static function get_annotations($gradeid, $pageno, $draft) {
        global $DB;

        $params = array(self::GRADEID => $gradeid, 'pageno' => $pageno, self::DRAFLIB => 1);
        if (!$draft) {
            $params[self::DRAFLIB] = 0;
        }
        $annotations = array();
        $records = $DB->get_records(self::BDDTABLEANNOTATION, $params);
        foreach ($records as $record) {
            array_push($annotations, new annotation($record));
        }

        return $annotations;
    }

    /**
     * Set all annotations for a page.
     * @param int $gradeid
     * @param int $pageno
     * @param annotation[] $annotations
     * @return int - the number of annotations.
     */
    public static function set_annotations($gradeid, $pageno, $annotations) {
        global $CFG, $DB;

        $annotationsRelease = $DB->get_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, 'pageno' => $pageno, self::DRAFLIB => 1));

        $added = 0;
        if (!$annotationsRelease || sizeof($annotationsRelease) == 0 || $CFG->preserve_student_on_update == 0) {
            $DB->delete_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, 'pageno' => $pageno, self::DRAFLIB => 1));
            $annotationdiv = array();
            foreach ($annotations as $record) {
                $currentdiv = $record->divcartridge;
                $newid = self::create_annotation_for_draft($record, $annotationdiv, $gradeid, $pageno);
                if ($newid) {
                    if ($currentdiv != '') {
                        $annotationdiv[$currentdiv] = $newid;
                    }
                    $added++;
                }
            }
        } else {
            $draftid = [];
            foreach ($annotations as $record) {
                if (!$record->id) {
                    $currentdiv = $record->divcartridge;
                    $newid = self::create_annotation_for_draft($record, $annotationdiv, $gradeid, $pageno);
                    if ($newid) {
                        if ($currentdiv != '') {
                            $annotationdiv[$currentdiv] = $newid;
                        }
                        $added++;
                    }
                    continue;
                }
                $annotationDraft = self::get_annotation($record->id);

                //maj annotation
                $annotationDraft->clone_teacher_annotation($record);
                $DB->update_record(self::BDDTABLEANNOTATION, $annotationDraft);
                $added++;
                $draftid[] = $annotationDraft->id;
            }
            foreach ($annotationsRelease as $annotation) {
                if (in_array($annotation->id, $draftid)) {
                    continue;
                }
                //need to be deleted
                self::remove_annotation($annotation->id);
            }
        }

        return $added;
    }

    /**
     * create an annotation for a draft
     * @param annotation|record $annotationRecord annotation to create
     * @param array $annotationdiv array with parent's annotation's id
     * @param type $gradeid grade id
     * @param type $pageno page no
     * @return int new annotation id
     */
    private static function create_annotation_for_draft($annotationRecord, $annotationdiv, $gradeid, $pageno) {
        if ($annotationRecord->parent_annot_div != '') {
            //on est dans le cas d'une annotation liee
            $idparent = $annotationdiv[$annotationRecord->parent_annot_div];
            $annotationRecord->parent_annot = intval($idparent);
        }
        // Force these.
        if (!($annotationRecord instanceof annotation)) {
            $annotation = new annotation($annotationRecord);
        } else {
            $annotation = $annotationRecord;
        }
        $annotation->gradeid = $gradeid;
        $annotation->pageno = $pageno;
        $annotation->draft = 1;
        return self::add_annotation($annotation);
    }

    /**
     * Update a set of annotations to database
     * @global $DB
     * @param annotation[] $annotations
     * @return int number of rows updated
     */
    public static function update_annotations_status($annotations) {
        global $DB;
        $added = 0;
        foreach ($annotations as $recordtmp) {
            $record = page_editor::get_annotation($recordtmp->id);
            //$old = $record->studentstatus;
            $record->studentstatus = $recordtmp->studentstatus;
            $record->studentanswer = $recordtmp->studentanswer;
            //debugging($recordtmp->id . ' - ' . $record->id . ' - ' . $old . ' | ' . $recordtmp->studentstatus . ' - ' . $record->studentstatus . ' | ' . $recordtmp->studentanswer . ' - ' . $record->studentanswer);
            $DB->update_record(self::BDDTABLEANNOTATION, $record);
            $added++;
        }

        return $added;
    }

    /**
     * Get a single annotation by id.
     * @param int $annotationid
     * @return annotation or false
     */
    public static function get_annotation($annotationid) {
        global $DB;

        $record = $DB->get_record(self::BDDTABLEANNOTATION, array('id' => $annotationid), '*', IGNORE_MISSING);
        if ($record) {
            return new annotation($record);
        }
        return false;
    }

    /**
     * Unrelease drafts
     * @param int $gradeid
     * @return bool
     */
    public static function unrelease_drafts($gradeid) {
        global $DB;

        // Delete the non-draft annotations.
        return $DB->delete_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, self::DRAFLIB => 0));
    }

    /**
     * Release the draft comments and annotations to students.
     * @param int $gradeid
     * @return bool
     */
    public static function release_drafts($gradeid) {
        global $CFG, $DB;

        $annotationsRelease = $DB->get_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, self::DRAFLIB => 0));
        $parentlink = [];

        if (!$annotationsRelease || sizeof($annotationsRelease) == 0 || $CFG->preserve_student_on_update == 0) {
            // Delete the previous non-draft annotations.
            $DB->delete_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, self::DRAFLIB => 0));

            // Copy all the draft annotations to non-drafts.
            $records = $DB->get_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, self::DRAFLIB => 1));
            foreach ($records as $record) {
                $oldid = $record->id;
                $newid = self::create_annotation_for_release($record);
                $parentlink[$oldid] = $newid;
            }
            $newAnnotationsRelease = $DB->get_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, self::DRAFLIB => 0));
            foreach ($newAnnotationsRelease as $annotation) {
                if ($annotation->parent_annot > 0 && $parentlink[$annotation->parent_annot]) {
                    $annotation->parent_annot = $parentlink[$annotation->parent_annot];
                    $DB->update_record(self::BDDTABLEANNOTATION, $annotation);
                }
            }
        } else {
            $records = $DB->get_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, self::DRAFLIB => 1));
            $draftid = [];
            // update existing annotations
            foreach ($annotationsRelease as $annotation) {
                $annotationDraft = self::get_annotation($annotation->draft_id);

                //if no result, annotation has been deleted
                if (!$annotationDraft) {
                    self::remove_annotation($annotation->id);
                    continue;
                }

                //maj annotation
                $annotation = new annotation($annotation);
                $annotation->clone_teacher_annotation($annotationDraft);
                $DB->update_record(self::BDDTABLEANNOTATION, $annotation);

                $parentlink[$annotation->draft_id] = $annotation->id;
                $draftid[] = $annotation->draft_id;
            }
            //create only new annotations
            foreach ($records as $record) {
                if (in_array($record->id, $draftid)) {
                    continue;
                }
                //need to be created
                $oldid = $record->id;
                $newid = self::create_annotation_for_release($record, $parentlink);
                $parentlink[$oldid] = $newid;
            }
            //maj parent ling
            $newAnnotationsRelease = $DB->get_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, self::DRAFLIB => 0));
            foreach ($newAnnotationsRelease as $annotation) {
                if (!$annotation->parent_annot || $annotation->parent_annot < 0 || in_array($annotation->draft_id, $draftid) || !$parentlink[$annotation->parent_annot]) {
                    continue;
                }
                $annotation->parent_annot = $parentlink[$annotation->parent_annot];
                $DB->update_record(self::BDDTABLEANNOTATION, $annotation);
            }
        }

        return true;
    }

    /**
     * create an annotation for a release draft
     * @param annotation|record $annotationRecord annotation to create
     * @param array $parentlink array with parent's annotation's id
     * @return int new annotation id
     */
    private static function create_annotation_for_release($annotationRecord) {
        $oldid = $annotationRecord->id;
        unset($annotationRecord->id);
        $annotationRecord->draft = 0;
        $annotationRecord->draft_id = $oldid;
        /* if ($annotationRecord->parent_annot > 0) {
          $annotationRecord->parent_annot = $parentlink[$annotationRecord->parent_annot];
          } */
        // Force these.
        if (!($annotationRecord instanceof annotation)) {
            $annotation = new annotation($annotationRecord);
        } else {
            $annotation = $annotationRecord;
        }
        return self::add_annotation($annotation);
    }

    /**
     * Has annotations or comments.
     * @param int $gradeid
     * @return bool
     */
    public static function has_annotations_or_comments($gradeid, $includedraft) {
        global $DB;
        $params = array(self::GRADEID => $gradeid);
        if (!$includedraft) {
            $params[self::DRAFLIB] = 0;
        }
        if ($DB->count_records(self::BDDTABLEANNOTATION, $params)) {
            return true;
        }
        return false;
    }

    /**
     * Aborts all draft annotations and reverts to the last version released to students.
     * @param int $gradeid
     * @return bool
     */
    public static function revert_drafts($gradeid) {
        global $DB;

        // Delete the previous non-draft annotations and comments.
        $DB->delete_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, self::DRAFLIB => 1));

        // Copy all the draft annotations and comments to non-drafts.
        $records = $DB->get_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $gradeid, self::DRAFLIB => 0));
        foreach ($records as $record) {
            unset($record->id);
            $record->draft = 0;
            $DB->insert_record(self::BDDTABLEANNOTATION, $record);
        }

        return true;
    }

    /**
     * Add a annotation to a page.
     * @param annotation $annotation
     * @return bool
     */
    public static function add_annotation(annotation $annotation) {
        global $DB;

        $annotation->id = null;
        if ($annotation->parent_annot == 0) {
            $annotation->parent_annot = null;
        }
        return $DB->insert_record(self::BDDTABLEANNOTATION, $annotation);
    }

    /**
     * Remove a annotation from a page.
     * @param int $annotationid
     * @return bool
     */
    public static function remove_annotation($annotationid) {
        global $DB;

        return $DB->delete_records(self::BDDTABLEANNOTATION, array('id' => $annotationid));
    }

    /**
     * Copy annotations, comments, pages, and other required content from the source user to the current group member
     * being procssed when using applytoall.
     *
     * @param int|\assign $assignment
     * @param stdClass $grade
     * @param int $sourceuserid
     * @return bool
     */
    public static function copy_drafts_from_to($assignment, $grade, $sourceuserid) {
        global $DB;

        // Delete any existing annotations and comments from current user.
        $DB->delete_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $grade->id));
        // Get gradeid, annotations and comments from sourceuserid.
        $sourceusergrade = $assignment->get_user_grade($sourceuserid, true, $grade->attemptnumber);
        $annotations = $DB->get_records(self::BDDTABLEANNOTATION, array(self::GRADEID => $sourceusergrade->id, self::DRAFLIB => 1));
        $contextid = $assignment->get_context()->id;
        $sourceitemid = $sourceusergrade->id;

        // Add annotations and comments to current user to generate feedback file.
        foreach ($annotations as $annotation) {
            $annotation->gradeid = $grade->id;
            $DB->insert_record(self::BDDTABLEANNOTATION, $annotation);
        }

        $fs = get_file_storage();

        // Copy the stamp files.
        self::replace_files_from_to($fs, $contextid, $sourceitemid, $grade->id, document_services::STAMPS_FILEAREA, true);

        // Copy the PAGE_IMAGE_FILEAREA files.
        self::replace_files_from_to($fs, $contextid, $sourceitemid, $grade->id, document_services::PAGE_IMAGE_FILEAREA);

        return true;
    }

    /**
     * Replace the area files in the specified area with those in the source item id.
     *
     * @param \file_storage $fs The file storage class
     * @param int $contextid The ID of the context for the assignment.
     * @param int $sourceitemid The itemid to copy from - typically the source grade id.
     * @param int $itemid The itemid to copy to - typically the target grade id.
     * @param string $area The file storage area.
     * @param bool $includesubdirs Whether to copy the content of sub-directories too.
     */
    public static function replace_files_from_to($fs, $contextid, $sourceitemid, $itemid, $area, $includesubdirs = false) {
        $component = 'assignfeedback_editpdfplus';
        // Remove the existing files within this area.
        $fs->delete_area_files($contextid, $component, $area, $itemid);

        // Copy the files from the source area.
        if ($files = $fs->get_area_files($contextid, $component, $area, $sourceitemid, "filename", $includesubdirs)) {
            foreach ($files as $file) {
                $newrecord = new \stdClass();
                $newrecord->contextid = $contextid;
                $newrecord->itemid = $itemid;
                $fs->create_file_from_storedfile($newrecord, $file);
            }
        }
    }

    /**
     * Delete the draft annotations and comments.
     *
     * This is intended to be used when the version of the PDF has changed and the annotations
     * might not be relevant any more, therefore we should delete them.
     *
     * @param int $gradeid The grade ID.
     * @return bool
     */
    public static function delete_draft_content($gradeid) {
        global $DB;
        $conditions = array(self::GRADEID => $gradeid, self::DRAFLIB => 1);
        $result = $DB->delete_records(self::BDDTABLEANNOTATION, $conditions);
        return $result;
    }

    /**
     * Get the feedback comment from the database.
     *
     * @param int $gradeid
     * @return stdClass|false The feedback comments for the given grade if it exists.
     *                        False if it doesn't.
     */
    public function get_feedback_comments($gradeid) {
        global $DB;
        return $DB->get_record('assignfeedback_comments', array('grade' => $gradeid));
    }

    public static function custom_type_tool(type_tool $newToolType) {
        global $CFG;
        switch ($newToolType->label) {
            case 'highlightplus':
                $newToolType->set_color($CFG->highlightplus_color);
                $newToolType->set_cartridge_color($CFG->highlightplus_cartridge_color);
                $newToolType->set_cartridge_x($CFG->highlightplus_cartridge_x);
                $newToolType->set_cartridge_y($CFG->highlightplus_cartridge_y);
                break;

            case 'stampplus':
                $newToolType->set_color($CFG->stampplus_color);
                break;

            case 'frame':
                $newToolType->set_cartridge_x($CFG->frame_cartridge_x);
                $newToolType->set_cartridge_y($CFG->frame_cartridge_y);
                break;

            case 'verticalline':
                $newToolType->set_color($CFG->verticalline_color);
                $newToolType->set_cartridge_color($CFG->verticalline_cartridge_color);
                $newToolType->set_cartridge_x($CFG->verticalline_cartridge_x);
                $newToolType->set_cartridge_y($CFG->verticalline_cartridge_y);
                break;

            case 'stampcomment':
                $newToolType->set_cartridge_color($CFG->stampcomment_cartridge_color);
                $newToolType->set_cartridge_x($CFG->stampcomment_cartridge_x);
                $newToolType->set_cartridge_y($CFG->stampcomment_cartridge_y);
                break;

            case 'commentplus':
                $newToolType->set_cartridge_color($CFG->commentplus_cartridge_color);
                $newToolType->set_cartridge_x($CFG->commentplus_cartridge_x);
                $newToolType->set_cartridge_y($CFG->commentplus_cartridge_y);
                break;

            default:
                break;
        }
        return $newToolType;
    }

    /**
     * Set page rotation value.
     * @param int $gradeid grade id.
     * @param int $pageno page number.
     * @param bool $isrotated whether the page is rotated or not.
     * @param string $pathnamehash path name hash
     * @param int $degree rotation degree.
     * @throws \dml_exception
     */
    public static function set_page_rotation($gradeid, $pageno, $isrotated, $pathnamehash, $degree = 0) {
        global $DB;
        $oldrecord = self::get_page_rotation($gradeid, $pageno);
        if ($oldrecord == null) {
            $record = new \stdClass();
            $record->gradeid = $gradeid;
            $record->pageno = $pageno;
            $record->isrotated = $isrotated;
            $record->pathnamehash = $pathnamehash;
            $record->degree = $degree;
            $DB->insert_record('assignfeedback_editpp_rot', $record, false);
        } else {
            $oldrecord->isrotated = $isrotated;
            $oldrecord->pathnamehash = $pathnamehash;
            $oldrecord->degree = $degree;
            $DB->update_record('assignfeedback_editpp_rot', $oldrecord, false);
        }
    }

    /**
     * Get Page Rotation Value.
     * @param int $gradeid grade id.
     * @param int $pageno page number.
     * @return mixed
     * @throws \dml_exception
     */
    public static function get_page_rotation($gradeid, $pageno) {
        global $DB;
        $result = $DB->get_record('assignfeedback_editpp_rot', array('gradeid' => $gradeid, 'pageno' => $pageno));
        return $result;
    }

}
