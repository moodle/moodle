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

namespace mod_board\output\ajax_form\modal;

use lang_string;
use moodle_url;
use renderer_base;

/**
 * Base class for elements that open forms in modal dialogs.
 *
 * @package     mod_board
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class action implements \core\output\named_templatable, \core\output\renderable {
    /** @var string redirect after form submission */
    public const SUBMITTED_ACTION_REDIRECT = 'redirect';
    /** @var string reload page after form submission */
    public const SUBMITTED_ACTION_RELOAD = 'reload';
    /** @var string do nothing after form submission */
    public const SUBMITTED_ACTION__NOTHING = 'nothing';

    /** @var string what happens after form is submitted */
    protected $formsubmittedaction = self::SUBMITTED_ACTION_RELOAD;
    /** @var moodle_url ajax form URL */
    protected $formurl;
    /** @var string|lang_string element label*/
    protected $label;
    /** @var array extra CSS classes */
    protected $classes = [];
    /** @var \core\output\pix_icon|null */
    protected $icon = null;
    /** @var string standard dialog size names 'sm', 'lg' and 'xl' */
    protected $formsize = 'sm';
    /** @var string|null initial modal title, falls back to action label */
    protected $modaltitle = null;

    /**
     * Create button that open a form in modal dialog.
     *
     * @param moodle_url $formurl
     * @param string|lang_string $label element label
     */
    public function __construct(moodle_url $formurl, string|lang_string $label) {
        $this->formurl = $formurl;
        $this->label = $label;
    }

    /**
     * Reset CSS classes.
     *
     * @param array $classes
     * @return static
     */
    public function set_classes(array $classes): static {
        $this->classes = $classes;
        return $this;
    }

    /**
     * Add CSS class.
     *
     * @param string $class
     * @return static
     */
    public function add_class(string $class): static {
        $this->classes[] = $class;
        $this->classes = array_unique($this->classes);
        return $this;
    }

    /**
     * Set optional icon.
     *
     * @param \pix_icon $pixicon
     * @return static
     */
    public function set_icon(\pix_icon $pixicon): static {
        $this->icon = $pixicon;
        return $this;
    }

    /**
     * Set dialog size.
     *
     * @param string $size values 'sm', 'lg', 'xl'
     * @return static
     */
    public function set_form_size(string $size): static {
        if (!in_array($size, ['sm', 'lg', 'xl'])) {
            throw new \core\exception\invalid_parameter_exception('Invalid dialog size, use: sm, lg or xl');
        }
        $this->formsize = $size;
        return $this;
    }

    /**
     * Set initial modal dialog size.
     *
     * @param string $title
     * @return static
     */
    public function set_modal_title(string $title): static {
        $this->modaltitle = $title;
        return $this;
    }

    /**
     * Specify what happens after form is submitted.
     *
     * @param string $action
     * @return static
     */
    public function set_form_submitted_action(string $action): static {
        if (
            $action !== self::SUBMITTED_ACTION__NOTHING
            && $action !== self::SUBMITTED_ACTION_REDIRECT
            && $action !== self::SUBMITTED_ACTION_RELOAD
        ) {
            throw new \core\exception\coding_exception('Invalid submit action: ' . $action);
        }
        $this->formsubmittedaction = $action;
        return $this;
    }

    /**
     * Export data for template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $data = [
            'label' => (string)$this->label,
            'classes' => implode(' ', $this->classes),
            'formurl' => $this->formurl->out(false),
            'formsubmittedaction' => $this->formsubmittedaction,
            'formsize' => $this->formsize,
            'modaltitle' => $this->modaltitle ?? (string)$this->label,
        ];

        if ($this->icon) {
            $data['icon'] = \core\output\icon_system::instance()->render_pix_icon($output, $this->icon);
        }

        return $data;
    }

    /**
     * Returns template name.
     *
     * @param renderer_base $renderer
     * @return string
     */
    public function get_template_name(renderer_base $renderer): string {
        $parts = explode('\\', get_class($this));
        return 'mod_board/ajax_form/modal/' . array_pop($parts);
    }
}
