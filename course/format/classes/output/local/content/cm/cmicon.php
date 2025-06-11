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
 * Contains the default activity icon.
 *
 * @package   core_courseformat
 * @copyright 2023 Mikel Martin <mikel@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\cm;

use cm_info;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use renderable;
use stdClass;

/**
 * Base class to render a course module icon.
 *
 * @package   core_courseformat
 * @copyright 2023 Mikel Martin <mikel@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cmicon implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var course_format the course format */
    protected $format;

    /** @var cm_info the course module instance */
    protected $mod;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param cm_info $mod the course module ionfo
     */
    public function __construct(
        course_format $format,
        cm_info $mod,
    ) {
        $this->format = $format;
        $this->mod = $mod;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): array {
        $mod = $this->mod;

        if (!$this->is_icon_visible()) {
            // Nothing to be displayed to the user.
            return [];
        }

        $iconurl = $mod->get_icon_url();
        $iconclass = $iconurl->get_param('filtericon') ? '' : 'nofilter';
        $isbranded = component_callback('mod_' . $mod->modname, 'is_branded', [], false);

        $data = [
            'uservisible' => $mod->uservisible,
            'url' => $mod->url,
            'icon' => $iconurl,
            'iconclass' => $iconclass,
            'modname' => $mod->modname,
            'pluginname' => get_string('pluginname', 'mod_' . $mod->modname),
            'showtooltip' => $this->format->show_editor(),
            'purpose' => plugin_supports('mod', $mod->modname, FEATURE_MOD_PURPOSE, MOD_PURPOSE_OTHER),
            'branded' => $isbranded,
        ];

        return $data;
    }

    /**
     * Return if the activity has a visible icon.
     *
     * @return bool if the icon should be shown.
     */
    public function is_icon_visible(): bool {
        return $this->mod->is_visible_on_course_page() && $this->mod->url;
    }
}
