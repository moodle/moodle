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

namespace report_themeusage\reportbuilder\local\entities;

use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\report\column;
use lang_string;

/**
 * Theme entity.
 *
 * Defines all the columns and filters that can be added to reports that use this entity.
 *
 * @package    report_themeusage
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme extends base {

    /**
     * Database tables that this entity uses.
     *
     * @return array
     */
    protected function get_default_tables(): array {
        return [
            'config_plugins',
        ];
    }

    /**
     * The default title for this entity.
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('theme');
    }

    /**
     * Initialize the entity.
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        return $this;
    }

    /**
     * Returns list of all available columns.
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        global $DB;
        $themealias = $this->get_table_alias('config_plugins');
        $sqlsubstring = $DB->sql_substr("{$themealias}.plugin", 7);

        $courselabel = get_string('course');
        $cohortlabel = get_string('cohort', 'cohort');
        $userlabel = get_string('user');
        $categorylabel = get_string('category');

        // Force theme column.
        $columns[] = (new column(
            'forcetheme',
            new lang_string('forcetheme'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$themealias}.plugin")
            ->add_callback(static function(?string $theme): string {
                $theme = get_string('pluginname', $theme);
                return format_text($theme, FORMAT_PLAIN);
            });

        // Usage type column.
        $columns[] = (new column(
            'usagetype',
            new lang_string('usagetype', 'report_themeusage'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join("LEFT JOIN (
                           SELECT '{$courselabel}' AS usagetype, theme, COUNT(theme) AS themecount
                             FROM {course}
                            WHERE " . $DB->sql_isnotempty('course', 'theme', false, false) . "
                         GROUP BY theme
                            UNION
                           SELECT '{$userlabel}' AS usagetype, theme, COUNT(theme) AS themecount
                             FROM {user}
                            WHERE " . $DB->sql_isnotempty('user', 'theme', false, false) . "
                         GROUP BY theme
                            UNION
                           SELECT '{$cohortlabel}' AS usagetype, theme, COUNT(theme) AS themecount
                             FROM {cohort}
                            WHERE " . $DB->sql_isnotempty('cohort', 'theme', false, false) . "
                         GROUP BY theme
                            UNION
                           SELECT '{$categorylabel}' AS usagetype, theme, COUNT(theme) AS themecount
                             FROM {course_categories}
                            WHERE " . $DB->sql_isnotempty('course_categories', 'theme', false, false) . "
                         GROUP BY theme
                        ) tuse ON tuse.theme={$sqlsubstring}")
            ->set_type(column::TYPE_TEXT)
            ->add_fields("tuse.usagetype, tuse.themecount")
            ->add_callback(static function(?string $usagetype, \stdClass $row): string {
                $count = $row->themecount ?? 0;
                return format_text($usagetype . ' ('. $count . ')', FORMAT_PLAIN);
            });

        return $columns;
    }
}
