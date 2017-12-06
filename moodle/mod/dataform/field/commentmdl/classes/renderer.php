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
 * @package dataformfield
 * @subpackage commentmdl
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class dataformfield_commentmdl_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        global $CFG;

        $field = $this->_field;
        $fieldname = $field->name;

        $replacements = array_fill_keys(array_keys($patterns), '');

        // No edit mode for this field so just return html.
        if ($entry->id > 0 and !empty($CFG->usecomments)) {
            foreach ($patterns as $pattern) {
                switch($pattern) {
                    case "[[$fieldname:count]]":
                        $options = array('count' => true);
                        $str = $this->display_browse($entry, $options);
                        break;
                    case "[[$fieldname:inline]]":
                        $options = array('notoggle' => true, 'autostart' => true);
                        $str = $this->display_browse($entry, $options);
                        break;
                    case "[[$fieldname]]":
                    case "[[$fieldname:add]]":
                        $str = $this->display_browse($entry);
                        break;
                    default:
                        $str = '';
                }
                $replacements[$pattern] = $str;
            }
        }

        return $replacements;
    }

    /**
     *
     */
    public function display_browse($entry, $options = array()) {
        global $DB, $CFG;

        $field = $this->_field;
        $fieldid = $field->id;
        $df = $field->get_df();

        // Only for existing entries.
        if (!($entry->id > 0)) {
            return '';
        }

        $str = '';

        require_once("$CFG->dirroot/comment/lib.php");
        $cmt = new stdClass;
        $cmt->context = $df->context;
        $cmt->courseid  = $df->course->id;
        $cmt->cm      = $df->cm;
        $cmt->itemid  = $entry->id;
        $cmt->component = 'mod_dataform';
        $cmt->area = $field->name;
        $cmt->showcount = isset($options['showcount']) ? $options['showcount'] : true;

        if (!empty($options['count'])) {
            $comment = new comment($cmt);
            $str = $comment->count();
        } else {
            foreach ($options as $key => $val) {
                $cmt->$key = $val;
            }
            $comment = new comment($cmt);
            $str = $comment->output(true);
        }

        return $str;
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[$fieldname]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:count]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:inline]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:add]]"] = array(false, $fieldname);

        return $patterns;
    }
}
