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

namespace core_courseformat\output;

use cm_info;
use core_courseformat\output\local\courseformat_named_templatable;
use core\output\named_templatable;
use renderer_base;
use stdClass;

/**
 * Base class to render an activity badge.
 *
 * Plugins can extend this class and override some methods to customize the content to be displayed in the activity badge.
 *
 * @package    core_courseformat
 * @copyright  2023 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class activitybadge implements named_templatable, \renderable {
    use courseformat_named_templatable;

    /** @var array Badge defined styles. */
    public const STYLES = [
        'none' => 'badge-none',
        'dark' => 'badge-dark',
        'danger' => 'badge-danger',
        'warning' => 'badge-warning',
        'info' => 'badge-info',
    ];

    /** @var cm_info The course module information. */
    protected $cminfo = null;

    /** @var string The content to be displayed in the activity badge.  */
    protected $content = null;

    /** @var string The style for the activity badge.  */
    protected $style = self::STYLES['none'];

    /** @var \moodle_url An optional URL to redirect the user when the activity badge is clicked.  */
    protected $url = null;

    /** @var string An optional element id in case the module wants to add some code for the activity badge (events, CSS...). */
    protected $elementid = null;

    /**
     * @var array An optional array of extra HTML attributes to add to the badge element (for example, data attributes).
     * The format for this array is [['name' => 'attr1', 'value' => 'attrval1'], ['name' => 'attr2', 'value' => 'attrval2']].
     */
    protected $extraattributes = [];

    /**
     * Constructor.
     *
     * @param cm_info $cminfo The course module information.
     */
    public function __construct(cm_info $cminfo) {
        $this->cminfo = $cminfo;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    final public function export_for_template(renderer_base $output): stdClass {
        $this->update_content();
        if (empty($this->content)) {
            return new stdClass();
        }

        $data = (object)[
            'badgecontent' => $this->content,
            'badgestyle' => $this->style,
        ];

        if (!empty($this->url)) {
            $data->badgeurl = $this->url->out();
        }

        if (!empty($this->elementid)) {
            $data->badgeelementid = $this->elementid;
        }

        if (!empty($this->extraattributes)) {
            $data->badgeextraattributes = $this->extraattributes;
        }

        return $data;
    }

    /**
     * Creates an instance of activityclass for the given course module, in case it implements it.
     *
     * @param cm_info $cminfo
     * @return self|null An instance of activityclass for the given module or null if the module doesn't implement it.
     */
    final public static function create_instance(cm_info $cminfo): ?self {
        $classname = '\mod_' . $cminfo->modname . '\output\courseformat\activitybadge';
        if (!class_exists($classname)) {
            return null;
        }
        return new $classname($cminfo);
    }

    /**
     * This method will be called before exporting the template.
     *
     * It should be implemented by any module extending this class and will be in charge of updating any of the class attributes
     * with the proper information that will be displayed in the activity badge (like the content or the badge style).
     */
    abstract protected function update_content(): void;
}
