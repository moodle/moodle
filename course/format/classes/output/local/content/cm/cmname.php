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
 * Contains the default activity name inplace editable.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\cm;

use cm_info;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use renderable;
use section_info;
use stdClass;

/**
 * Base class to render a course module inplace editable header.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cmname implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    private $section;

    /** @var cm_info the course module instance */
    protected $mod;

    /** @var array optional display options */
    protected $displayoptions;

    /** @var string the activity title output class name */
    protected $titleclass;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the course module ionfo
     * @param bool|null $editable if it is editable (not used)
     * @param array $displayoptions optional extra display options
     */
    public function __construct(
        course_format $format,
        section_info $section,
        cm_info $mod,
        ?bool $editable = null,
        array $displayoptions = []
    ) {
        $this->format = $format;
        $this->section = $section;
        $this->mod = $mod;
        $this->displayoptions = $displayoptions;

        // Get the necessary classes.
        $this->titleclass = $format->get_output_classname('content\\cm\\title');
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): array {
        $mod = $this->mod;
        $displayoptions = $this->displayoptions;

        if (!$this->has_name()) {
            // Nothing to be displayed to the user.
            return [];
        }

        $iconurl = $mod->get_icon_url();
        $iconclass = $iconurl->get_param('filtericon') ? '' : 'nofilter';
        $data = [
            'url' => $mod->url,
            'icon' => $iconurl,
            'iconclass' => $iconclass,
            'modname' => $mod->modname,
            'textclasses' => $displayoptions['textclasses'] ?? '',
            'purpose' => plugin_supports('mod', $mod->modname, FEATURE_MOD_PURPOSE, MOD_PURPOSE_OTHER),
            'activityname' => $this->get_title_data($output),
        ];

        if ($this->format->show_editor()) {
            $data['pluginname'] = get_string('pluginname', 'mod_' . $mod->modname);
        }

        return $data;
    }

    /**
     * Get the title data.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    protected function get_title_data(\renderer_base $output): array {
        $title = new $this->titleclass(
            $this->format,
            $this->section,
            $this->mod,
            $this->displayoptions
        );
        return (array) $title->export_for_template($output);
    }

    /**
     * Return if the activity has a visible name.
     *
     * @return bool if the title is visible.
     */
    public function has_name(): bool {
        return $this->mod->is_visible_on_course_page() && $this->mod->url;
    }
}
