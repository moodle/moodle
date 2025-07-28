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

use core\external\pix_icon_exporter;

/**
 * Data structure representing an icon.
 *
 * @copyright 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class pix_icon implements externable, renderable, templatable {
    /**
     * @var string The icon name
     */
    public $pix;

    /**
     * @var string The component the icon belongs to.
     */
    public $component;

    /**
     * @var array An array of attributes to use on the icon
     */
    public $attributes = [];

    /**
     * Constructor
     *
     * @param string $pix short icon name
     * @param string $alt The alt text to use for the icon
     * @param string $component component name
     * @param null|array $attributes html attributes
     */
    public function __construct(
        $pix,
        $alt,
        $component = 'moodle',
        ?array $attributes = null,
    ) {
        global $PAGE;

        $this->pix = $pix;
        $this->component  = $component;
        $this->attributes = (array)$attributes;

        if (empty($this->attributes['class'])) {
            $this->attributes['class'] = '';
        }

        // Set an additional class for big icons so that they can be styled properly.
        if (substr($pix, 0, 2) === 'b/') {
            $this->attributes['class'] .= ' iconsize-big';
        }

        // If the alt is empty, don't place it in the attributes, otherwise it will override parent alt text.
        if (!is_null($alt)) {
            $this->attributes['alt'] = $alt;

            // If there is no title, set it to the attribute.
            if (!isset($this->attributes['title'])) {
                $this->attributes['title'] = $this->attributes['alt'];
            }
        } else {
            unset($this->attributes['alt']);
        }

        if (empty($this->attributes['title'])) {
            // Remove the title attribute if empty, we probably want to use the parent node's title
            // and some browsers might overwrite it with an empty title.
            unset($this->attributes['title']);
        }

        // Hide icons from screen readers that have no alt.
        if (empty($this->attributes['alt'])) {
            $this->attributes['aria-hidden'] = 'true';
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        $attributes = $this->attributes;
        $extraclasses = '';

        foreach ($attributes as $key => $item) {
            if ($key == 'class') {
                $extraclasses = $item;
                unset($attributes[$key]);
                break;
            }
        }

        $attributes['src'] = $output->image_url($this->pix, $this->component)->out(false);
        $templatecontext = [];
        foreach ($attributes as $name => $value) {
            $templatecontext[] = ['name' => $name, 'value' => $value];
        }
        $title = isset($attributes['title']) ? $attributes['title'] : '';
        if (empty($title)) {
            $title = isset($attributes['alt']) ? $attributes['alt'] : '';
        }
        $data = [
            'attributes' => $templatecontext,
            'extraclasses' => $extraclasses,
        ];

        return $data;
    }

    #[\Override]
    public function get_exporter(?\core\context $context = null): pix_icon_exporter {
        $context = $context ?? \core\context\system::instance();
        return new pix_icon_exporter($this, ['context' => $context]);
    }

    #[\Override]
    public static function get_read_structure(
        int $required = VALUE_REQUIRED,
        mixed $default = null
    ): \core_external\external_single_structure {
        return pix_icon_exporter::get_read_structure($required, $default);
    }

    #[\Override]
    public static function read_properties_definition(): array {
        return pix_icon_exporter::read_properties_definition();
    }

    /**
     * Much simpler version of export that will produce the data required to render this pix with the
     * pix helper in a mustache tag.
     *
     * @return array
     */
    public function export_for_pix() {
        $title = isset($this->attributes['title']) ? $this->attributes['title'] : '';
        if (empty($title)) {
            $title = isset($this->attributes['alt']) ? $this->attributes['alt'] : '';
        }
        return [
            'key' => $this->pix,
            'component' => $this->component,
            'title' => (string) $title,
        ];
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(pix_icon::class, \pix_icon::class);
