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
 * Fixture for fake course format testing course format API.
 *
 * @package    core_course
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_theunittest extends core_courseformat\base {

    /**
     * Definitions of the additional options that format uses
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseformatoptions = array(
                'hideoddsections' => array(
                    'default' => 0,
                    'type' => PARAM_INT,
                ),
                'summary_editor' => array(
                    'default' => '',
                    'type' => PARAM_RAW,
                ),
            );
        }
        if ($foreditform && !isset($courseformatoptions['hideoddsections']['label'])) {
            $sectionmenu = array(
                0 => 'Never',
                1 => 'Hide without notice',
                2 => 'Hide with notice'
            );
            $courseformatoptionsedit = array(
                'hideoddsections' => array(
                    'label' => 'Hide odd sections',
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                ),
                'summary_editor' => array(
                    'label' => 'Summary Text',
                    'element_type' => 'editor',
                ),
            );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Allows to specify for modinfo that section is not available even when it is visible and conditionally available.
     *
     * @param section_info $section
     * @param bool $available
     * @param string $availableinfo
     */
    public function section_get_available_hook(section_info $section, &$available, &$availableinfo) {
        if (($section->section % 2) && ($hideoddsections = $this->get_course()->hideoddsections)) {
            $available = false;
            if ($hideoddsections == 2) {
                $availableinfo = 'Odd sections are oddly hidden';
            } else {
                $availableinfo = '';
            }
        }
    }
}
