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

namespace core_course\output;

use core\component;
use core\exception\coding_exception;
use core\output\local\properties\iconsize;
use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use core\url;
use cm_info;

/**
 * Class activity_icon
 *
 * @package    core_course
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_icon implements renderable, templatable {

    /** @var string optional text title */
    protected string $title = '';

    /** @var string Extra container classes. */
    protected string $extraclasses = '';

    /** @var bool Determine if the icon must be colored or not. */
    protected bool $colourize = true;

    /** @var iconsize set the icon size */
    protected iconsize $iconsize = iconsize::UNDEFINED;

    /** @var url The icon URL. */
    protected url $iconurl;

    /** @var string The module purpose */
    protected string $purpose;

    /** @var bool is branded */
    protected bool $isbranded;

    /**
     * Constructor.
     *
     * @param string $modname the module name
     */
    protected function __construct(
        /** @var string the module name */
        protected string $modname,
    ) {
        try {
            $this->isbranded = component_callback('mod_' . $this->modname, 'is_branded', [], false);
        } catch (coding_exception $e) {
            debugging($e->getMessage(), DEBUG_DEVELOPER);
            $this->isbranded = false;
        }
        $this->purpose = plugin_supports('mod', $this->modname, FEATURE_MOD_PURPOSE, MOD_PURPOSE_OTHER);
    }

    /**
     * Create an activity icon from a cm_info object.
     *
     * @param cm_info $cm
     * @return self
     */
    public static function from_cm_info(cm_info $cm): self {
        $result = new self($cm->modname);
        $result->iconurl = $cm->get_icon_url();
        return $result;
    }

    /**
     * Create an activity icon from a module name.
     *
     * @param string $modname
     * @return self
     */
    public static function from_modname(string $modname): self {
        return new self($modname);
    }

    /**
     * Set the title.
     *
     * @param string $title
     * @return self
     */
    public function set_title(string $title): self {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the colourize icon value.
     *
     * @param bool $colourize
     * @return self
     */
    public function set_colourize(bool $colourize): self {
        $this->colourize = $colourize;
        return $this;
    }

    /**
     * Set the extra classes.
     *
     * @param string $extraclasses
     * @return self
     */
    public function set_extra_classes(string $extraclasses): self {
        $this->extraclasses = $extraclasses;
        return $this;
    }

    /**
     * Set the icon size.
     *
     * @param iconsize $iconsize
     * @return self
     */
    public function set_icon_size(iconsize $iconsize): self {
        $this->iconsize = $iconsize;
        return $this;
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        $data = [
            'icon' => $this->get_icon_url($output),
            'iconclass' => $this->get_icon_classes($output),
            'modname' => $this->modname,
            'pluginname' => get_string('pluginname', 'mod_' . $this->modname),
            'purpose' => $this->purpose,
            'branded' => $this->isbranded,
            'extraclasses' => $this->extraclasses . $this->iconsize->classes(),
        ];

        if (!empty($this->title)) {
            $data['title'] = $this->title;
        }

        return $data;
    }

    /**
     * Get the icon URL.
     *
     * @param renderer_base $output
     * @return url
     */
    public function get_icon_url(renderer_base $output): url {
        if (isset($this->iconurl)) {
            return $this->iconurl;
        }
        $icon = $output->image_url('monologo', $this->modname);
        // Legacy activity modules may only have an `icon` icon instead of a `monologo` icon.
        $ismonologo = component::has_monologo_icon('mod', $this->modname);

        if ($ismonologo) {
            // The filtericon param is used to determine if the icon should be colored or not.
            // The name of the param is not colorize to preserve backward compatibility.
            $icon->param('filtericon', 1);
        }
        $this->iconurl = $icon;
        return $icon;
    }

    /**
     * Check if the module is branded.
     *
     * @return bool
     */
    public function is_branded(): bool {
        return $this->isbranded;
    }

    /**
     * Get the colourize icon value.
     *
     * @return bool
     */
    public function get_colourize(): bool {
        return $this->colourize;
    }

    /**
     * Get the title text.
     *
     * @return string
     */
    public function get_title(): string {
        return $this->title;
    }

    /**
     * Get the extra classes.
     *
     * @return string
     */
    public function get_extra_classes(): string {
        return $this->extraclasses;
    }

    /**
     * Get the icon classes.
     *
     * @param renderer_base $output
     * @return string
     */
    public function get_icon_classes(renderer_base $output): string {
        $result = 'activityicon icon';
        $iconurl = $this->get_icon_url($output);
        $needfiltering = $this->colourize && $iconurl->get_param('filtericon');
        $result .= ($needfiltering) ? '' : ' nofilter';
        return $result;
    }

    /**
     * Get the icon size.
     *
     * @return iconsize
     */
    public function get_icon_size(): iconsize {
        return $this->iconsize;
    }

    /**
     * Get the activity purpose.
     *
     * @return string
     */
    public function get_purpose(): string {
        return $this->purpose;
    }
}
