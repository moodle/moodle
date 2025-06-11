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
 * Contains the default activity title.
 *
 * This class is usually rendered inside the cmname inplace editable.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\cm;

use cm_info;
use core\output\inplace_editable;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_text;
use lang_string;
use renderable;
use section_info;
use stdClass;
use core_external\external_api;
use context_module;

/**
 * Base class to render a course module title inside a course format.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class title extends inplace_editable implements named_templatable, renderable {

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    private $section;

    /** @var cm_info the course module instance */
    protected $mod;

    /** @var array optional display options */
    protected $displayoptions;

    /** @var editable if the title is editable */
    protected $editable;

    /** @var displaytemplate the default display template */
    protected $displaytemplate = 'core_courseformat/local/content/cm/title';

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the course module ionfo
     * @param array $displayoptions optional extra display options
     * @param bool|null $editable force editable value
     */
    public function __construct(
        course_format $format,
        section_info $section,
        cm_info $mod,
        array $displayoptions = [],
        ?bool $editable = null
    ) {
        $this->format = $format;
        $this->section = $section;
        $this->mod = $mod;

        // Usually displayoptions are loaded in the main cm output. However when the user uses the inplace editor
        // the cmname output does not calculate the css classes.
        $this->displayoptions = $this->load_display_options($displayoptions);

        if ($editable === null) {
            $editable = $format->show_editor();
        }
        $this->editable = $editable;

        // Setup inplace editable.
        parent::__construct(
            'core_course',
            'activityname',
            $mod->id,
            $this->editable,
            $mod->name,
            $mod->name,
            new lang_string('edittitle'),
            new lang_string('newactivityname', '', $mod->get_formatted_name())
        );
    }

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param \renderer_base $renderer The renderer requesting the template name
     * @return string
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'core/inplace_editable';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): array {

        // Inplace editable uses pre-rendered elements and does not allow line beaks in the UI value.
        $this->displayvalue = str_replace("\n", "", $this->get_title_displayvalue());

        if (trim($this->displayvalue) == '') {
            $this->editable = false;
        }
        return parent::export_for_template($output);
    }

    /**
     * Return the title template data to be used inside the inplace editable.
     *
     */
    protected function get_title_displayvalue(): string {
        global $PAGE;

        // Inplace editable uses core renderer by default. However, course elements require
        // the format specific renderer.
        $courseoutput = $this->format->get_renderer($PAGE);

        $mod = $this->mod;

        $data = (object)[
            'url' => $mod->url,
            'instancename' => $mod->get_formatted_name(),
            'uservisible' => $mod->uservisible,
            'linkclasses' => $this->displayoptions['linkclasses'],
        ];

        // File type after name, for alphabetic lists (screen reader).
        if (strpos(
            core_text::strtolower($data->instancename),
            core_text::strtolower($mod->modfullname)
        ) === false) {
            $data->altname = get_accesshide(' ' . $mod->modfullname);
        }

        // Get on-click attribute value if specified and decode the onclick - it
        // has already been encoded for display (puke).
        $data->onclick = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);

        return $courseoutput->render_from_template(
            $this->displaytemplate,
            $data
        );
    }

    /**
     * Load the required display options if not present already.
     *
     * In most cases, display options are provided as a param when creating the
     * object. However, inplace_editable and some blocks does not know all of them as it is
     * called in a webservice and we need to ensure it is calculated.
     *
     * @param array $displayoptions the provided dispaly options
     * @return array the full display options list
     */
    protected function load_display_options(array $displayoptions): array {
        $format = $this->format;
        $mod = $this->mod;

        if (
            isset($displayoptions['linkclasses']) &&
            isset($displayoptions['textclasses']) &&
            isset($displayoptions['onclick'])
        ) {
            return $displayoptions;
        }

        $cmclass = $format->get_output_classname('content\\cm');
        $cmoutput = new $cmclass(
            $format,
            $this->section,
            $mod,
            $displayoptions
        );
        $displayoptions['linkclasses'] = $cmoutput->get_link_classes();
        $displayoptions['textclasses'] = $cmoutput->get_text_classes();
        $displayoptions['onclick'] = $cmoutput->get_onclick_code();
        return $displayoptions;
    }

    /**
     * Updates course module name.
     *
     * This method is used mainly by inplace_editable webservice.
     *
     * @param int $itemid course module id
     * @param string $newvalue new name
     * @return static
     */
    public static function update($itemid, $newvalue) {
        $context = context_module::instance($itemid);
        // Check access.
        external_api::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);

        // Trim module name and Update value.
        set_coursemodule_name($itemid, trim($newvalue));
        $coursemodulerecord = get_coursemodule_from_id('', $itemid, 0, false, MUST_EXIST);
        // Return instance.
        $modinfo = get_fast_modinfo($coursemodulerecord->course);
        $cm = $modinfo->get_cm($itemid);
        $section = $modinfo->get_section_info($cm->sectionnum);

        $format = course_get_format($cm->course);
        return new static($format, $section, $cm, [], true);
    }
}
