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
 * Block material_download
 *
 * @package    block_material_download
 * @copyright  2013 onwards Paola Frignani, TH Ingolstadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class block_material_download extends block_base {

    /**
     * Returns the last N words from the passed string
     * @param string $text  The original text to analyse
     * @param int $numwords  (OPTIONAL) The number of words to return, default 1
     * @return string
     */
    public function getlastwordsstr($text, $numwords=1) {
        $nonwordchars = ':;,.?![](){}*';
        $result = '';
        $words = explode(' ', $text);
        $wordcount = count($words);
        if ($numwords > $wordcount) {
            $numwords = $wordcount;
        }
        for ($w = $numwords; $w > 0; $w--) {
            if (!empty($result)) {
                $result .= ' ';
            }
            $result .= trim($words[$wordcount - $w], $nonwordchars);
        }
        return $result;
    }

    public function init() {
        $this->title = get_string('material_download', 'block_material_download');
    }

    public function get_content() {
        global $DB, $CFG, $OUTPUT, $COURSE, $PAGE;
        require_once("$CFG->libdir/resourcelib.php");

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $resources['resource'] = get_string('resource', 'block_material_download');
        $resources['folder'] = get_string('folder', 'block_material_download');

        $modinfo = get_fast_modinfo($COURSE);

        $meldung = '';

        foreach ($modinfo->instances as $modname => $instances) {
            if (array_key_exists($modname, $resources)) {
                if ($modname == 'resource') {
                    $ii = 0;
                    foreach ($instances as $instancesid => $instance) {
                        if (!$instance->uservisible) {
                            continue;
                        }
                        $cms[$instance->id] = $instance;
                        $materialien[$instance->modname][] = $instance->id;
                        $ii++;
                    }
                }

                if ($modname == 'folder') {
                    $fi = 0;
                    foreach ($instances as $instancesid => $instance) {
                        if (!$instance->uservisible) {
                            continue;
                        }
                        $cms[$instance->id] = $instance;
                        $materialien[$instance->modname][] = $instance->id;
                        $fi++;
                    }
                    $ii = $fi;
                }

                if ($ii > 0 || $fi > 0) {
                    if ($ii > 1) {
                        $resources['resource'] = get_string('resources', 'block_material_download');
                    }
                    if (isset($fi) && $fi > 1) {
                        $resources['folder'] = get_string('folders', 'block_material_download');
                    }
                    $meldung .= $ii . ' ' . $resources[$modname] . '<br />';
                }
            }
        }

        $downloadlink = array();

        $sqlchk = "SELECT cm.id FROM {course_modules} cm INNER JOIN {modules} m ON m.id = cm.module
                 WHERE cm.course = '" . $COURSE->id .
                "' AND ( m.name IN ('folder','resource') )";
        $modules = $DB->get_records_sql($sqlchk);
        foreach ($modules as $module) {
            $checkid = $module->id;
            $sqlsec = "SELECT * FROM {course_sections} cs WHERE cs.course = ? AND ".
                    "( cs.sequence LIKE ? OR cs.sequence LIKE ? OR cs.sequence LIKE ? OR cs.sequence = ? ) LIMIT 1";
            $rowsec = $DB->get_records_sql($sqlsec, array($COURSE->id, $checkid . ",%", '%,' . $checkid . ',%', '%,' .
                $checkid, $checkid));

            foreach ($rowsec as $row) {
                if (!empty($row->section) OR ($row->section == 0)) {
                    $sectid = $row->section;
                    $downloadlink[$sectid] = $row->name;
                }
            }
        }

        ksort($downloadlink);
        $showlink = '';
        $this->content->footer = '';

        foreach ($downloadlink as $value => $text) {
            $prefix = get_string('resource2', 'block_material_download') . ' ' .
                get_string('from', 'block_material_download') . ' ';

            // Add section name modifier (i.e. "week" or "topic") if the course
            // format is known. Section 0 name if section 0 is the case.
            if ($value == 0) {
                if ($COURSE->format == "weeks") {
                    $optionprefix = $prefix . get_string('section0name', 'format_weeks');
                } else if ($COURSE->format == "topics") {
                    $optionprefix = $prefix . get_string('section0name', 'format_topics');
                } else {
                    $optionprefix = $prefix . get_string('section', 'block_material_download') .' 0';
                }
            } else {
                if ($COURSE->format == "weeks") {
                    $optionprefix = $prefix . get_string('week', 'block_material_download') .' ';
                } else if ($COURSE->format == "topics") {
                    $optionprefix = $prefix . get_string('topic', 'block_material_download') .' ';
                } else {
                    $optionprefix = $prefix . get_string('section', 'block_material_download') .' ';
                }
            }
            // Add title to option if there is long form of the section title.
            if ($text) {
                $title = ' title="' . $text .'" ';
                if (strlen($text) <= 35) {
                    $text = $text;
                } else if (preg_match('/\s/', $text)) {
                    $lastword = $this->getlastwordsstr($text, 1);
                    $text = substr($text, 0, strrpos(substr($text, 0, 20), ' ')) . '&hellip;' .
                            (strlen($lastword) <= 15 ? $lastword : substr($lastword, -15));
                } else {
                    $text = substr($text, 0, 25);
                }
                $showlink .= '<option ' . $title . ' value="' . $CFG->wwwroot .
                    '/blocks/material_download/download_materialien.php?courseid=' . ($COURSE->id) . '&ccsectid=' .
                    $value . '">' . $prefix . $text . '</option>';
            } else {
                 $showlink .= '<option value="' . $CFG->wwwroot .
                     '/blocks/material_download/download_materialien.php?courseid=' . ($COURSE->id) . '&ccsectid=' .
                    $value . '">' . $optionprefix;
                if ($value != 0) {
                    $showlink .= $value;
                }
                $showlink .= '</option>';
            }
        }
        if ($meldung != '') {
            $this->content->text = $meldung;
            $this->content->footer .= '
                    <form class="form-inline" onsubmit="this.action = document.getElementById(\'filename\').value">
                        <select id="filename" class="custom-select singleselect">
                            <option value="#">' . get_string('choose', 'block_material_download') . '</option>
                            ' . $showlink . '
                            <option value="' . $CFG->wwwroot .'/blocks/material_download/download_materialien.php?courseid=' .
                                ($COURSE->id) . '&ccsectid=0">' . get_string('download_files', 'block_material_download') .
                                '</option>
                        </select>
                       <input type = "button" value = "' . get_string('download', 'moodle') .
                           '" class="btn btn-secondary mt-1" onclick="window.location.href=document.getElementById(\'filename\').value" />
                   </form>';
        } else {
            $this->content->text = $PAGE->user_is_editing() ? get_string('no_file_exist', 'block_material_download') : '';
        }
        return $this->content;
    }

    public function applicable_formats() {
        return array('my' => false, 'course-view' => true, 'course-view-social' => true);
    }

}


