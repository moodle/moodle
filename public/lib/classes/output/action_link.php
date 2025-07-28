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

use core\external\action_link_exporter;
use core\output\actions\component_action;
use moodle_url;
use stdClass;

/**
 * Data structure describing html link with special action attached.
 *
 * @copyright 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class action_link implements externable, renderable {
    /**
     * @var moodle_url Href url
     */
    public $url;

    /**
     * @var string|renderable Link text HTML fragment
     */
    public $text;

    /**
     * @var array HTML attributes
     */
    public $attributes;

    /**
     * @var array List of actions attached to link
     */
    public $actions;

    /**
     * @var pix_icon Optional pix icon to render with the link
     */
    public $icon;

    /**
     * Constructor
     * @param moodle_url $url
     * @param string|renderable $text HTML fragment
     * @param null|component_action $action
     * @param null|array $attributes associative array of html link attributes + disabled
     * @param null|pix_icon $icon optional pix_icon to render with the link text
     */
    public function __construct(
        moodle_url $url,
        $text,
        ?component_action $action = null,
        ?array $attributes = null,
        ?pix_icon $icon = null
    ) {
        $this->url = clone($url);
        $this->text = $text;
        if (empty($attributes['id'])) {
            $attributes['id'] = html_writer::random_id('action_link');
        }
        $this->attributes = (array)$attributes;
        if ($action) {
            $this->add_action($action);
        }
        $this->icon = $icon;
    }

    /**
     * Add action to the link.
     *
     * @param component_action $action
     */
    public function add_action(component_action $action) {
        $this->actions[] = $action;
    }

    /**
     * Adds a CSS class to this action link object
     * @param string $class
     */
    public function add_class($class) {
        if (empty($this->attributes['class'])) {
            $this->attributes['class'] = $class;
        } else {
            $this->attributes['class'] .= ' ' . $class;
        }
    }

    /**
     * Returns true if the specified class has been added to this link.
     * @param string $class
     * @return bool
     */
    public function has_class($class) {
        return strpos(' ' . $this->attributes['class'] . ' ', ' ' . $class . ' ') !== false;
    }

    /**
     * Return the rendered HTML for the icon. Useful for rendering action links in a template.
     * @return string
     */
    public function get_icon_html() {
        global $OUTPUT;
        if (!$this->icon) {
            return '';
        }
        return $OUTPUT->render($this->icon);
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $attributes = $this->attributes;

        $data->id = $attributes['id'];
        unset($attributes['id']);

        $data->disabled = !empty($attributes['disabled']);
        unset($attributes['disabled']);

        $data->text = $this->text instanceof renderable ? $output->render($this->text) : (string) $this->text;
        $data->url = $this->url ? $this->url->out(false) : '';
        $data->icon = $this->icon ? $this->icon->export_for_pix() : null;
        $data->classes = isset($attributes['class']) ? $attributes['class'] : '';
        unset($attributes['class']);

        $data->attributes = array_map(function ($key, $value) {
            return [
                'name' => $key,
                'value' => $value,
            ];
        }, array_keys($attributes), $attributes);

        $data->actions = array_map(function ($action) use ($output) {
            return $action->export_for_template($output);
        }, !empty($this->actions) ? $this->actions : []);
        $data->hasactions = !empty($this->actions);

        return $data;
    }

    #[\Override]
    public function get_exporter(?\core\context $context = null): action_link_exporter {
        $context = $context ?? \core\context\system::instance();
        return new action_link_exporter($this, ['context' => $context]);
    }

    #[\Override]
    public static function get_read_structure(
        int $required = VALUE_REQUIRED,
        mixed $default = null
    ): \core_external\external_single_structure {
        return action_link_exporter::get_read_structure($required, $default);
    }

    #[\Override]
    public static function read_properties_definition(): array {
        return action_link_exporter::read_properties_definition();
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(action_link::class, \action_link::class);
