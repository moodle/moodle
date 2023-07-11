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

namespace core\output;

use moodle_page;
use renderer_base;
use url_select;

/**
 * Data structure representing standard components displayed on the activity header.
 *
 * Consists of title, header, description. In addition, additional_items can be provided which is a url_select
 *
 * @copyright 2021 Peter
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.0
 * @package core
 * @category output
 */
class activity_header implements \renderable, \templatable {
    /** @var moodle_page $page The current page we are looking at */
    protected $page;
    /** @var string $title The title to be displayed in the header. Defaults to activityrecord name. */
    protected $title;
    /** @var string $description The description to be displayed. Defaults to activityrecord intro. */
    protected $description;
    /** @var \stdClass $user The user we are dealing with */
    protected $user;
    /** @var url_select $additionalnavitems Any additional custom navigation elements to be injected into template. */
    protected $additionalnavitems;
    /** @var bool $hidecompletion Whether to show completion criteria, if available, or not */
    protected $hidecompletion;
    /** @var bool $hideoverflow Whether to show the overflow data or not */
    protected $hideoverflow;
    /** @var bool $hideheader Whether or not to show the header */
    protected $hideheader;

    /**
     * Constructor for activity_header
     *
     * @param moodle_page $page
     * @param \stdClass $user
     */
    public function __construct(moodle_page $page, \stdClass $user) {
        $this->page = $page;
        $this->user = $user;
        $pageoptions = $this->page->theme->activityheaderconfig ?? [];
        $layoutoptions = $this->page->layout_options['activityheader'] ?? [];
        // Do a basic setup for the header based on theme/page options.
        if ($page->activityrecord) {
            if (empty($pageoptions['notitle']) && empty($layoutoptions['notitle'])) {
                $this->title = format_string($page->activityrecord->name);
            }

            if (empty($layoutoptions['nodescription']) && !empty($page->activityrecord->intro) &&
                    trim($page->activityrecord->intro)) {
                $this->description = format_module_intro($this->page->activityname, $page->activityrecord, $page->cm->id);
            }
        }
        $this->hidecompletion = !empty($layoutoptions['nocompletion']);
        $this->hideoverflow = false;
        $this->hideheader = false;
    }

    /**
     * Checks if the theme has specified titles to be displayed.
     *
     * @return bool
     */
    public function is_title_allowed(): bool {
        return empty($this->page->theme->activityheaderconfig['notitle']);
    }

    /**
     * Bulk set class member variables. Only updates variables which have corresponding setters
     *
     * @param mixed[] $config Array of variables to set, with keys being their name. Valid names/types as follows:
     *      'hidecompletion' => bool
     *      'additionalnavitems' => url_select
     *      'hideoverflow' => bool
     *      'title' => string
     *      'description' => string
     */
    public function set_attrs(array $config): void {
        foreach ($config as $key => $value) {
            if (method_exists($this, "set_$key")) {
                $this->{"set_$key"}($value);
            } else {
                debugging("Invalid class member variable: {$key}", DEBUG_DEVELOPER);
            }
        }
    }

    /**
     * Sets the hidecompletion class member variable
     *
     * @param bool $value
     */
    public function set_hidecompletion(bool $value): void {
        $this->hidecompletion = $value;
    }

    /**
     * Sets the additionalnavitems class member variable
     *
     * @param url_select $value
     */
    public function set_additionalnavitems(url_select $value): void {
        $this->additionalnavitems = $value;
    }

    /**
     * Sets the hideoverflow class member variable
     *
     * @param bool $value
     */
    public function set_hideoverflow(bool $value): void {
        $this->hideoverflow = $value;
    }

    /**
     * Sets the title class member variable.
     *
     * @param string $value
     */
    public function set_title(string $value): void {
        $this->title = preg_replace('/<h2[^>]*>([.\s\S]*)<\/h2>/', '$1', $value);
    }

    /**
     * Sets the description class member variable
     *
     * @param string $value
     */
    public function set_description(string $value): void {
        $this->description = $value;
    }

    /**
     * Disable the activity header completely. Use this if the page has some custom content, headings to be displayed.
     */
    public function disable(): void {
        $this->hideheader = true;
    }

    /**
     * Export items to be rendered with a template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        // Don't need to show anything if not displaying within an activity context.
        if (!$this->page->activityrecord) {
            return [];
        }

        // If within an activity context but requesting to hide the header,
        // then just trigger the render for maincontent div.
        if ($this->hideheader) {
            return ['title' => ''];
        }

        $completion = null;
        if (!$this->hidecompletion) {
            $completiondetails = \core_completion\cm_completion_details::get_instance($this->page->cm, $this->user->id);
            $activitydates = \core\activity_dates::get_dates_for_module($this->page->cm, $this->user->id);
            $completion = $output->activity_information($this->page->cm, $completiondetails, $activitydates);
        }

        $format = course_get_format($this->page->course);
        if ($format->supports_components()) {
            $this->page->requires->js_call_amd(
                'core_courseformat/local/content/activity_header',
                'init'
            );
        }

        return [
            'title' => $this->title,
            'description' => $this->description,
            'completion' => $completion,
            'additional_items' => $this->hideoverflow ? '' : $this->additionalnavitems,
        ];
    }

    /**
     * Get the heading level for a given heading depending on whether the theme's activity header displays a heading
     * (usually the activity name).
     *
     * @param int $defaultlevel The default heading level when the activity header does not display a heading.
     * @return int
     */
    public function get_heading_level(int $defaultlevel = 2): int {
        // The heading level depends on whether the theme's activity header displays a heading (usually the activity name).
        $headinglevel = $defaultlevel;
        if ($this->is_title_allowed() && !empty(trim($this->title))) {
            // A heading for the activity name is displayed on this page with a heading level 2.
            // Increment the default level for this heading by 1.
            $headinglevel++;
        }
        return $headinglevel;
    }
}
