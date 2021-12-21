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

namespace tool_brickfield\local\tool;

use tool_brickfield\manager;

/**
 * Brickfield accessibility tool base class.
 *
 * All common properties and methods for all tool types.
 *
 * @package     tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class tool {

    /** @var string[] All of the tools provided. */
    const TOOLNAMES = ['errors', 'activityresults', 'checktyperesults', 'printable', 'advanced'];

    /** @var string base64 bitmap image type */
    const BASE64_BMP = 'data:image/bmp;base64';

    /** @var string base64 gif image type */
    const BASE64_GIF = 'data:image/gif;base64';

    /** @var string base64 jpg image type */
    const BASE64_JPG = 'data:image/jpeg;base64';

    /** @var string base64 png image type */
    const BASE64_PNG = 'data:image/png;base64';

    /** @var string base64 svg image type */
    const BASE64_SVG = 'data:image/svg+xml;base64';

    /** @var string base64 webp image type */
    const BASE64_WEBP = 'data:image/webp;base64';

    /** @var string base64 ico image type */
    const BASE64_ICO = 'data:image/x-icon;base64';

    /** @var null Generic object for data used in tool renderers/templates. */
    private $data = null;

    /** @var  filter Any filter being used for tool display. */
    private $filter;

    /** @var string Error message if there is one. */
    private $errormessage = '';

    /**
     * Get a mapping of tool shortname => class name.
     *
     * @return  string[]
     */
    protected static function get_tool_classnames(): array {
        $tools = [];

        foreach (self::TOOLNAMES as $toolname) {
            $tools[$toolname] = "tool_brickfield\\local\\tool\\{$toolname}";
        }

        return $tools;
    }

    /**
     * Return an array with one of each tool instance.
     *
     * @return tool[]
     */
    public static function build_all_accessibilitytools(): array {
        return array_map(function($classname) {
            return new $classname();
        }, self::get_tool_classnames());
    }

    /**
     * Get a list of formal tool names for each tool.
     *
     * @return  string[]
     */
    public static function get_tool_names(): array {
        return array_map(function($classname) {
            return $classname::toolname();
        }, self::get_tool_classnames());
    }

    /**
     * Provide a lowercase name identifying this plugin. Should really be the same as the directory name.
     * @return string
     */
    abstract public function pluginname();

    /**
     * Provide a name for this tool, suitable for display on pages.
     * @return mixed
     */
    abstract public static function toolname();

    /**
     * Provide a short name for this tool, suitable for menus and selectors.
     * @return mixed
     */
    abstract public static function toolshortname();

    /**
     * Fetch the data for renderer / template display. Classes must implement this.
     * @return \stdClass
     */
    abstract protected function fetch_data(): \stdClass;

    /**
     * Return the data needed for the renderer.
     * @return \stdClass
     * @throws \coding_exception
     */
    public function get_data(): \stdClass {
        if (!$this->filter) {
            throw new \coding_exception('Filter has not been set.');
        }

        if (empty($this->data)) {
            $this->data = $this->fetch_data();
        }

        return $this->data;
    }

    /**
     * Implementing class should set the 'valid' property when get_data is called.
     * @return bool
     */
    public function data_is_valid(): bool {
        $data = $this->get_data();
        return (!empty($data->valid));
    }

    /**
     * Implementing class should set an error string if data is invalidated in 'get_data';
     * @return string
     */
    public function data_error(): string {
        if (!$this->data_is_valid()) {
            return $this->data->error;
        } else {
            return '';
        }
    }

    /**
     * Setter for filter property.
     * @param filter $filter
     * @throws \coding_exception
     */
    public function set_filter(filter $filter): void {
        if ($this->filter) {
            throw new \coding_exception('Filter can only be set once.');
        }

        $this->filter = $filter;
    }

    /**
     * Getter for filter property.
     * @return filter|null
     */
    public function get_filter(): ?filter {
        return $this->filter;
    }

    /**
     * Returns the output target for this tool's filter.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_output_target() {
        $filter = $this->get_filter();
        if (!$filter) {
            throw new \coding_exception('Filter has not been set.');
        }
        return $filter->target;
    }

    /**
     * Get the HTML output for display.
     *
     * @return mixed
     */
    public function get_output() {
        global $PAGE;

        $data = $this->get_data();
        $filter = $this->get_filter();
        $renderer = $PAGE->get_renderer('tool_brickfield', $this->pluginname());
        return $renderer->display($data, $filter);
    }

    /**
     * Return the defined toolname.
     *
     * @return mixed
     */
    public function get_toolname(): string {
        return static::toolname();
    }

    /**
     * Return the defined toolshortname.
     *
     * @return mixed
     */
    public function get_toolshortname(): string {
        return static::toolshortname();
    }

    /**
     * Verify that accessibility tools can be accessed in the provided context.
     * @param filter $filter
     * @param \context $context
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function can_access(filter $filter, \context $context = null): bool {
        return $filter->can_access($context);
    }

    /**
     * Return any defined error message.
     *
     * @return string
     */
    public function get_error_message(): string {
        return $this->errormessage;
    }

    /**
     * Get module label for display
     * @param string $modulename
     * @return string
     * @throws \coding_exception
     */
    public static function get_module_label(string $modulename): string {
        if (get_string_manager()->string_exists('pluginname', $modulename)) {
            $modulename = get_string('pluginname', $modulename);
        } else {
            $modulename = get_string($modulename, manager::PLUGINNAME);
        }
        return($modulename);
    }

    /**
     * Get instance name for display
     * @param string $component
     * @param string $table
     * @param int $cmid
     * @param int $courseid
     * @param int $categoryid
     * @return string
     */
    public static function get_instance_name(string $component, string $table, ?int $cmid, ?int $courseid,
        ?int $categoryid): string {
        global $DB;

        $instancename = '';
        if (empty($component)) {
            return $instancename;
        }
        if ($component == 'core_course') {
            if (($table == 'course_categories') && ($categoryid != 0) && ($categoryid != null)) {
                $instancename = $DB->get_field($table, 'name', ['id' => $categoryid]);
                return get_string('category') . ' - ' . $instancename;
            }
            if (($courseid == 0) || ($courseid == null)) {
                return $instancename;
            }
            $thiscourse = get_fast_modinfo($courseid)->get_course();
            $instancename = $thiscourse->shortname;
        } else if ($component == 'core_question') {
            $instancename = get_string('questions', 'question');
        } else {
            if (($cmid == 0) || ($cmid == null)) {
                return $instancename;
            }
            $cm = get_fast_modinfo($courseid)->cms[$cmid];
            $instancename = $cm->name;
        }
        $instancename = static::get_module_label($component).' - '.$instancename;
        return($instancename);
    }

    /**
     * Provide arguments required for the toplevel page, using any provided filter.
     * @param filter|null $filter
     * @return array
     */
    public static function toplevel_arguments(filter $filter = null): array {
        if ($filter !== null) {
            return ['courseid' => $filter->courseid, 'categoryid' => $filter->categoryid];
        } else {
            return [];
        }
    }

    /**
     * Override this to return any tool specific perpage limits.
     * @param int $perpage
     * @return int
     */
    public function perpage_limits(int $perpage): int {
        return $perpage;
    }

    /**
     * Return array of base64 image formats.
     * @return array
     */
    public static function base64_img_array(): array {
        $base64 = [];
        $base64[] = self::BASE64_BMP;
        $base64[] = self::BASE64_GIF;
        $base64[] = self::BASE64_JPG;
        $base64[] = self::BASE64_PNG;
        $base64[] = self::BASE64_SVG;
        $base64[] = self::BASE64_WEBP;
        $base64[] = self::BASE64_ICO;
        return $base64;
    }

    /**
     * Detects if htmlcode contains base64 img data, for HTML display, such as errors page.
     * @param string $htmlcode
     * @return boolean
     */
    public static function base64_img_detected(string $htmlcode): bool {
        $detected = false;

        // Grab defined base64 img array.
        $base64 = self::base64_img_array();
        foreach ($base64 as $type) {
            // Need to detect this within an img tag.
            $pos = stripos($htmlcode, '<img src="'.$type);
            if ($pos !== false) {
                $detected = true;
                return $detected;
            }
        }
        return $detected;
    }

    /**
     * Truncate base64-containing htmlcode for HTML display, such as errors page.
     * @param string $htmlcode
     * @return string
     */
    public static function truncate_base64(string $htmlcode): string {
        $newhtmlcode = '';
        // Parse HTML by " characters.
        $sections = explode('"', $htmlcode);
        $base64 = self::base64_img_array();
        foreach ($sections as $section) {
            foreach ($base64 as $type) {
                $pos = stripos($section, $type);
                if ($pos !== false) {
                    $section = substr($section, 0, $pos + strlen($type)).'...';
                }
            }
            $newhtmlcode .= $section.'"';
        }
        return $newhtmlcode;
    }

    /**
     * Return the correct language string for the provided check.
     *
     * @param string $check
     * @return string
     */
    public static function get_check_description(string $check): string {
        return get_string('checkdesc:' . str_replace('_', '', $check), manager::PLUGINNAME);
    }
}
