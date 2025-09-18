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

namespace core_courseformat\output\local;

use core\output\named_templatable;
use core_courseformat\base as course_format;
use course_modinfo;
use section_info;
use renderable;

/**
 * Base class to render a course format.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content implements named_templatable, renderable {
    use courseformat_named_templatable;

    /** @var \core_courseformat\base the course format class */
    protected $format;

    /** @var string the section format class */
    protected $sectionclass;

    /** @var string the add section output class name */
    protected $addsectionclass;

    /** @var string section navigation class name */
    protected $sectionnavigationclass;

    /** @var string section selector class name */
    protected $sectionselectorclass;

    /** @var string the section control menu class */
    protected $sectioncontrolmenuclass;

    /** @var string bulk editor bar toolbox */
    protected $bulkedittoolsclass;

    /** @var bool if uses add section */
    protected $hasaddsection = true;

    /**
     * Constructor.
     *
     * @param course_format $format the coruse format
     */
    public function __construct(course_format $format) {
        $this->format = $format;

        // Load output classes names from format.
        $this->sectionclass = $format->get_output_classname('content\\section');
        $this->addsectionclass = $format->get_output_classname('content\\addsection');
        $this->sectionnavigationclass = $format->get_output_classname('content\\sectionnavigation');
        $this->sectionselectorclass = $format->get_output_classname('content\\sectionselector');
        $this->bulkedittoolsclass = $format->get_output_classname('content\\bulkedittools');
        $this->sectioncontrolmenuclass = $format->get_output_classname('content\\section\\controlmenu');
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return \stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;
        $format = $this->format;

        $sections = $this->export_sections($output);
        $initialsection = '';

        $data = (object)[
            'title' => $format->page_title(), // This method should be in the course_format class.
            'initialsection' => $initialsection,
            'sections' => $sections,
            'format' => $format->get_format(),
            'sectionreturn' => 'null', // Mustache templates don't display NULL, so pass a string value.
            'pagesectionid' => $this->format->get_sectionid() ?? 'null', // Pass a string value if NULL.
        ];

        // The single section format has extra navigation.
        if ($this->format->get_sectionid()) {
            $singlesectionnum = $this->format->get_sectionnum();
            if (!$PAGE->theme->usescourseindex) {
                $sectionnavigation = new $this->sectionnavigationclass($format, $singlesectionnum);
                $data->sectionnavigation = $sectionnavigation->export_for_template($output);

                $sectionselector = new $this->sectionselectorclass($format, $sectionnavigation);
                $data->sectionselector = $sectionselector->export_for_template($output);
            }
            $data->hasnavigation = true;
            $data->singlesection = array_shift($data->sections);
            $data->sectionreturn = $singlesectionnum;
        }

        if ($this->hasaddsection) {
            $addsection = new $this->addsectionclass($format);
            $data->numsections = $addsection->export_for_template($output);
        }

        if ($format->show_editor()) {
            $bulkedittools = new $this->bulkedittoolsclass($format);
            $data->bulkedittools = $bulkedittools->export_for_template($output);
        }

        return $data;
    }

    /**
     * Retrieves the action menu for the page header of the local content section.
     *
     * @param \renderer_base $output The renderer object used for rendering the action menu.
     * @return string|null The rendered action menu HTML, null if page no action menu is available.
     */
    public function get_page_header_action(\renderer_base $output): ?string {
        $sectionid = $this->format->get_sectionid();
        if ($sectionid !== null) {
            $modinfo = $this->format->get_modinfo();
            $sectioninfo = $modinfo->get_section_info_by_id($sectionid);
            /** @var \core_courseformat\output\local\content\section\controlmenu */
            $controlmenu = new $this->sectioncontrolmenuclass($this->format, $sectioninfo);
            return $output->render($controlmenu->get_action_menu($output));
        }
        return null;
    }

    /**
     * Export sections array data.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    protected function export_sections(\renderer_base $output): array {

        $format = $this->format;
        $course = $format->get_course();
        $modinfo = $this->format->get_modinfo();

        // Generate section list.
        $sections = [];
        $stealthsections = [];
        foreach ($this->get_sections_to_display($modinfo) as $sectionnum => $thissection) {
            // The course/view.php check the section existence but the output can be called
            // from other parts so we need to check it.
            if (!$thissection) {
                throw new \moodle_exception('unknowncoursesection', 'error', course_get_url($course),
                    format_string($course->fullname));
            }

            if (!$format->is_section_visible($thissection)) {
                continue;
            }

            /** @var \core_courseformat\output\local\content\section $section */
            $section = new $this->sectionclass($format, $thissection);

            if ($section->is_stealth()) {
                // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                if (!empty($modinfo->sections[$sectionnum])) {
                    $stealthsections[] = $section->export_for_template($output);
                }
                continue;
            }

            $sections[] = $section->export_for_template($output);
        }
        if (!empty($stealthsections)) {
            $sections = array_merge($sections, $stealthsections);
        }
        return $sections;
    }

    /**
     * Return an array of sections to display.
     *
     * This method is used to differentiate between display a specific section
     * or a list of them.
     *
     * @param course_modinfo $modinfo the current course modinfo object
     * @return section_info[] an array of section_info to display
     */
    protected function get_sections_to_display(course_modinfo $modinfo): array {
        $singlesectionid = $this->format->get_sectionid();
        if ($singlesectionid) {
            return [
                $modinfo->get_section_info_by_id($singlesectionid),
            ];
        }
        return $modinfo->get_listed_section_info_all();
    }
}
