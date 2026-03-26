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

namespace core\hook\output;

use core\output\help_icon;
use core\output\renderer_base;
use stdClass;

/**
 * Hook dispatched during help icon rendering.
 *
 * This hook allows plugins to:
 * 1. Modify the template context (e.g. append to 'text' or 'completedoclink' to add
 *    content inside the popover).
 * 2. Add extra HTML next to the help icon (e.g. an additional button beside the ?).
 * 3. Completely replace the help icon output with custom HTML.
 *
 * The template context is a mutable stdClass. Callbacks can directly modify its
 * properties (text, completedoclink, alt, title, url, ltr, linktext, icon) to
 * change the popover content without requiring template changes.
 *
 * @package    core
 * @copyright  2026 ISB Bayern
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\tags('output')]
#[\core\attribute\label('Allows plugins to modify or extend help icon rendering')]
final class before_help_icon_rendered {
    /** @var string Extra HTML to render before the help icon. */
    private string $beforeicon = '';

    /** @var string Extra HTML to render next to (after) the help icon. */
    private string $aftericon = '';

    /** @var string|null If set, completely replaces the default help icon output. */
    private ?string $replacement = null;

    /**
     * Constructor.
     *
     * @param help_icon $helpicon The help icon renderable being rendered.
     * @param renderer_base $renderer The renderer instance.
     * @param stdClass $templatecontext The mutable template context data from export_for_template.
     */
    public function __construct(
        /** @var help_icon The help icon renderable being rendered */
        public readonly help_icon $helpicon,
        /** @var renderer_base The renderer instance */
        public readonly renderer_base $renderer,
        /** @var stdClass The mutable template context data */
        private stdClass $templatecontext,
    ) {
    }

    /**
     * Get the help icon renderable.
     *
     * Useful for inspecting identifier and component to decide whether to act.
     *
     * @return help_icon
     */
    public function get_helpicon(): help_icon {
        return $this->helpicon;
    }

    /**
     * Get the mutable template context data.
     *
     * Modify properties directly to change the popover content.
     * Key properties:
     * - text: The help text HTML shown in the popover.
     * - completedoclink: The "More help" doc link HTML in the popover.
     * - alt: Alt text for the help icon.
     * - title: Title text.
     * - url: The help page URL.
     *
     * @return stdClass
     */
    public function get_templatecontext(): stdClass {
        return $this->templatecontext;
    }

    /**
     * Add extra HTML to render before the help icon.
     *
     * @param string|null $html HTML string to render before the help icon.
     */
    public function add_before_icon(?string $html): void {
        if ($html) {
            $this->beforeicon .= $html;
        }
    }

    /**
     * Get the extra HTML to render before the icon.
     *
     * @return string
     */
    public function get_before_icon(): string {
        return $this->beforeicon;
    }

    /**
     * Add extra HTML to render next to (after) the help icon.
     *
     * @param string|null $html HTML string to render after the help icon.
     */
    public function add_after_icon(?string $html): void {
        if ($html) {
            $this->aftericon .= $html;
        }
    }

    /**
     * Get the extra HTML to render after the icon.
     *
     * @return string
     */
    public function get_after_icon(): string {
        return $this->aftericon;
    }

    /**
     * Set a complete replacement for the help icon output.
     *
     * When set, the default help icon template will not be rendered.
     * Instead, the replacement HTML will be returned directly.
     *
     * @param string $html Complete HTML replacement for the help icon.
     */
    public function set_replacement(string $html): void {
        $this->replacement = $html;
    }

    /**
     * Get the replacement HTML, or null if no replacement was set.
     *
     * @return string|null
     */
    public function get_replacement(): ?string {
        return $this->replacement;
    }
}
