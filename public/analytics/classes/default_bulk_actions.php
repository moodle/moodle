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
 * Default list of bulk actions to reuse across different targets as presets.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Default list of bulk actions to reuse across different targets as presets.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_bulk_actions {

    /**
     * Accepted prediction.
     *
     * @return \core_analytics\bulk_action
     */
    public static function accept() {
        $attrs = [
            'data-bulk-actionname' => prediction::ACTION_FIXED
        ] + self::bulk_action_base_attrs();

        return new bulk_action(prediction::ACTION_FIXED,
            new \moodle_url(''), new \pix_icon('t/check', get_string('fixedack', 'analytics')),
            get_string('fixedack', 'analytics'), false, $attrs, action::TYPE_POSITIVE);
    }

    /**
     * The prediction is not applicable for this same (e.g. This student was unenrolled in the uni SIS).
     *
     * @return \core_analytics\bulk_action
     */
    public static function not_applicable() {
        $attrs = [
            'data-bulk-actionname' => prediction::ACTION_NOT_APPLICABLE
        ] + self::bulk_action_base_attrs();

        return new bulk_action(prediction::ACTION_NOT_APPLICABLE,
            new \moodle_url(''), new \pix_icon('fp/cross', get_string('notapplicable', 'analytics'), 'theme'),
            get_string('notapplicable', 'analytics'), false, $attrs, action::TYPE_NEUTRAL);
    }

    /**
     * Incorrectly flagged prediction, useful for models based on data.
     *
     * @return \core_analytics\bulk_action
     */
    public static function incorrectly_flagged() {
        $attrs = [
            'data-bulk-actionname' => prediction::ACTION_INCORRECTLY_FLAGGED
        ] + self::bulk_action_base_attrs();

        return new bulk_action(prediction::ACTION_INCORRECTLY_FLAGGED,
            new \moodle_url(''), new \pix_icon('i/incorrect', get_string('incorrectlyflagged', 'analytics')),
            get_string('incorrectlyflagged', 'analytics'), false, $attrs, action::TYPE_NEGATIVE);
    }

    /**
     * Useful prediction.
     *
     * @return \core_analytics\bulk_action
     */
    public static function useful() {
        $attrs = [
            'data-bulk-actionname' => prediction::ACTION_USEFUL
        ] + self::bulk_action_base_attrs();

        return new bulk_action(prediction::ACTION_USEFUL,
            new \moodle_url(''), new \pix_icon('t/check', get_string('useful', 'analytics')),
            get_string('useful', 'analytics'), false, $attrs, action::TYPE_POSITIVE);

    }

    /**
     * Not useful prediction.
     *
     * @return \core_analytics\bulk_action
     */
    public static function not_useful() {
        $attrs = [
            'data-bulk-actionname' => prediction::ACTION_NOT_USEFUL
        ] + self::bulk_action_base_attrs();

        return new bulk_action(prediction::ACTION_NOT_USEFUL,
            new \moodle_url(''), new \pix_icon('t/delete', get_string('notuseful', 'analytics')),
            get_string('notuseful', 'analytics'), false, $attrs, action::TYPE_NEGATIVE);
    }

    /**
     * Common attributes for all the action renderables.
     *
     * @return array
     */
    private static function bulk_action_base_attrs() {
        return [
            'disabled' => 'disabled',
            'data-toggle' => 'action',
            'data-action' => 'toggle',
        ];
    }
}