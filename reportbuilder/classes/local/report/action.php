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

declare(strict_types=1);

namespace core_reportbuilder\local\report;

use moodle_url;
use pix_icon;
use popup_action;
use stdClass;

/**
 * Class to represent a report action
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class action {

    /** @var moodle_url $url */
    protected $url;

    /** @var pix_icon $icon */
    protected $icon;

    /** @var array $attributes */
    protected $attributes;

    /** @var bool $popup */
    protected $popup;

    /** @var callable[] $callbacks */
    protected $callbacks = [];

    /**
     * Create an instance of an action to be added to a report. Both the parameters of the URL, and the attributes parameter
     * support placeholders which will be replaced with appropriate row values, e.g.:
     *
     * new action(new moodle_url('/', ['id' => ':id']), new pix_icon(...), ['data-id' => ':id'])
     *
     * Note that all expected placeholders should be added as base fields to the report
     *
     * @param moodle_url $url
     * @param pix_icon $icon
     * @param array $attributes
     * @param bool $popup
     */
    public function __construct(
        moodle_url $url,
        pix_icon $icon,
        array $attributes = [],
        bool $popup = false
    ) {
        $this->url = $url;
        $this->icon = $icon;
        $this->attributes = $attributes;
        $this->popup = $popup;
    }

    /**
     * Adds callback to the action. Used to verify action is available to current user, or preprocess values used in placeholders
     *
     * Multiple callbacks can be added. If at least one returns false then the action will not be displayed
     *
     * @param callable $callback
     * @return self
     */
    public function add_callback(callable $callback): self {
        $this->callbacks[] = $callback;
        return $this;
    }

    /**
     * Return renderer action icon suitable for output
     *
     * @uses core_renderer::action_icon()
     *
     * @param stdClass $row
     * @return string|null
     */
    public function get_action_link(stdClass $row): ?string {
        global $OUTPUT;

        foreach ($this->callbacks as $callback) {
            $row = clone $row; // Clone so we don't modify the shared row inside a callback.
            if (!$callback($row)) {
                return null;
            }
        }

        // Create a new moodle_url instance with our filled in placeholders for this row.
        $url = new moodle_url(
            $this->url->out_omit_querystring(true),
            self::replace_placeholders($this->url->params(), $row)
        );

        if ($this->popup) {
            $this->attributes['data-action'] = 'report-action-popup';
            $this->attributes['data-popup-action'] = json_encode(new popup_action('click', $url));
        }

        return $OUTPUT->action_icon($url, $this->icon, null, self::replace_placeholders($this->attributes, $row));
    }

    /**
     * Given an array of values, replace all placeholders with corresponding property of the given row
     *
     * @param array $values
     * @param stdClass $row
     * @return array
     */
    private static function replace_placeholders(array $values, stdClass $row): array {
        return array_map(static function($value) use ($row) {
            return preg_replace_callback('/^:(?<property>.*)$/', static function(array $matches) use ($row): string {
                return (string) ($row->{$matches['property']} ?? '');
            }, $value);
        }, $values);
    }
}
