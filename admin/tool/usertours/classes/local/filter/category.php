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

namespace tool_usertours\local\filter;

use tool_usertours\tour;
use context;

/**
 * Category filter.
 *
 * @package    tool_usertours
 * @copyright  2017 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category extends base {
    /**
     * The exclude key constant.
     */
    public const EXCLUDE_KEY = 'exclude_category';

    #[\Override]
    public static function get_filter_name() {
        return 'category';
    }

    #[\Override]
    public static function get_filter_options() {
        $options = \core_course_category::make_categories_list();
        return $options;
    }

    #[\Override]
    public static function add_filter_to_form(\MoodleQuickForm &$mform) {
        parent::add_filter_to_form($mform);
        $excludekey = 'filter_' . self::EXCLUDE_KEY;
        $mform->addElement(
            'select',
            $excludekey,
            get_string($excludekey, 'tool_usertours'),
            static::get_filter_options(),
            ['multiple' => true]
        );
        $mform->addHelpButton($excludekey, $excludekey, 'tool_usertours');
    }

    #[\Override]
    public static function filter_matches(tour $tour, context $context) {
        $includevalues = $tour->get_filter_values(static::get_filter_name());
        $excludevalues = $tour->get_filter_values(self::EXCLUDE_KEY);

        if (empty($includevalues) || empty($includevalues[0])) {
            return !static::check_contexts($context, $excludevalues);
        }

        if ($context->contextlevel < CONTEXT_COURSECAT) {
            return false;
        }
        return self::check_contexts($context, $includevalues) && !self::check_contexts($context, $excludevalues);
    }

    /**
     * Recursive function allows checking of parent categories.
     *
     * @param context $context
     * @param array $values
     * @return boolean
     */
    private static function check_contexts(context $context, array $values): bool {
        if (empty($values)) {
            return false;
        }

        if ($context->contextlevel > CONTEXT_COURSECAT) {
            return self::check_contexts($context->get_parent_context(), $values);
        } else if ($context->contextlevel == CONTEXT_COURSECAT) {
            if (in_array($context->instanceid, $values)) {
                return true;
            } else {
                return self::check_contexts($context->get_parent_context(), $values);
            }
        } else {
            return false;
        }
    }

    #[\Override]
    public static function prepare_filter_values_for_form(tour $tour, \stdClass $data) {
        parent::prepare_filter_values_for_form($tour, $data);
        $excludekey = 'filter_' . self::EXCLUDE_KEY;
        $data->$excludekey = $tour->get_filter_values(self::EXCLUDE_KEY);

        return $data;
    }

    #[\Override]
    public static function save_filter_values_from_form(tour $tour, \stdClass $data) {
        parent::save_filter_values_from_form($tour, $data);
        $excludekey = 'filter_' . self::EXCLUDE_KEY;
        $excludevalues = $data->$excludekey;
        $tour->set_filter_values(self::EXCLUDE_KEY, $excludevalues);
    }
}
