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

namespace report_themeusage\reportbuilder\local\systemreports;

use context_system;
use core_reportbuilder\local\entities\{course, user};
use core_cohort\reportbuilder\local\entities\cohort;
use core_course\reportbuilder\local\entities\course_category;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\system_report;
use core\output\theme_usage;
use report_themeusage\reportbuilder\local\entities\theme;

/**
 * Config changes system report class implementation
 *
 * @package    report_themeusage
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_usage_report extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters.
     */
    protected function initialise(): void {
        // Show results depending on the theme and type chosen.
        $themechoice = $this->get_parameter('themechoice', '', PARAM_TEXT);
        $typechoice = $this->get_parameter('typechoice', '', PARAM_TEXT);

        $themename = get_string('pluginname', 'theme_'.$themechoice);

        $themeentity = new theme();
        $themealias = $themeentity->get_table_alias('config_plugins');
        $this->set_main_table('config_plugins', $themealias);
        $this->add_entity($themeentity);

        $param1 = database::generate_param_name();
        $param2 = database::generate_param_name();
        $params = [$param1 => 'theme_' . $themechoice, $param2 => 'version'];

        $this->add_base_condition_sql("{$themealias}.plugin = :{$param1} AND {$themealias}.name = :{$param2}", $params);

        switch ($typechoice) {

            case theme_usage::THEME_USAGE_TYPE_ALL:

                $this->add_columns_theme();
                $this->set_downloadable(true, get_string('themeusagereportall', 'report_themeusage', $themename));

                break;

            case theme_usage::THEME_USAGE_TYPE_USER:

                $userentity = new user();
                $useralias = $userentity->get_table_alias('user');

                $this->add_entity($userentity->add_join(
                    "JOIN {user} {$useralias}
                       ON $useralias.theme = '{$themechoice}'
                      AND {$themealias}.plugin = 'theme_{$themechoice}'"));

                $this->add_columns_user();
                $this->add_filters_user();
                $this->set_downloadable(true, get_string('themeusagereportuser', 'report_themeusage', $themename));
                break;

            case theme_usage::THEME_USAGE_TYPE_COURSE:

                $courseentity = new course();
                $coursealias = $courseentity->get_table_alias('course');

                $this->add_entity($courseentity->add_join(
                    "JOIN {course} {$coursealias}
                       ON $coursealias.theme = '{$themechoice}'
                      AND {$themealias}.plugin = 'theme_{$themechoice}'"));

                $this->add_columns_course();
                $this->add_filters_course();
                $this->set_downloadable(true, get_string('themeusagereportcourse', 'report_themeusage', $themename));
                break;

            case theme_usage::THEME_USAGE_TYPE_COHORT:

                $cohortentity = new cohort();
                $cohortalias = $cohortentity->get_table_alias('cohort');

                $this->add_entity($cohortentity->add_join(
                    "JOIN {cohort} {$cohortalias}
                       ON $cohortalias.theme = '{$themechoice}'
                      AND {$themealias}.plugin = 'theme_{$themechoice}'"));

                $this->add_columns_cohort();
                $this->add_filters_cohort();
                $this->set_downloadable(true, get_string('themeusagereportcohort', 'report_themeusage', $themename));
                break;

            case theme_usage::THEME_USAGE_TYPE_CATEGORY:

                $categoryentity = new course_category();
                $categoryalias = $categoryentity->get_table_alias('course_categories');

                $this->add_entity($categoryentity->add_join(
                    "JOIN {course_categories} {$categoryalias}
                       ON $categoryalias.theme = '{$themechoice}'
                      AND {$themealias}.plugin = 'theme_{$themechoice}'"));

                $this->add_columns_category();
                $this->add_filters_category();
                $this->set_downloadable(true, get_string('themeusagereportcategory', 'report_themeusage', $themename));
                break;

            default:
                break;
        }
    }

    /**
     * Validates access to view this report.
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('moodle/site:config', context_system::instance());
    }

    /**
     * Adds the columns we want to display in the report for 'theme'.
     */
    protected function add_columns_theme(): void {
        $columns = [
            'theme:usagetype',
            'theme:forcetheme',
        ];

        $this->add_columns_from_entities($columns);
        $this->set_initial_sort_column('theme:forcetheme', SORT_ASC);
    }

    /**
     * Adds the columns we want to display in the report for 'user'.
     */
    protected function add_columns_user(): void {
        $columns = [
            'user:firstname',
            'user:lastname',
            'theme:forcetheme',
        ];

        $this->add_columns_from_entities($columns);
        $this->set_initial_sort_column('user:firstname', SORT_ASC);
    }

    /**
     * Adds the filters we want to display in the report for 'user'.
     */
    protected function add_filters_user(): void {
        $filters = [
            'user:firstname',
            'user:lastname',
        ];

        $this->add_filters_from_entities($filters);
    }

    /**
     * Adds the columns we want to display in the report for 'course'.
     */
    protected function add_columns_course(): void {
        $columns = [
            'course:fullname',
            'course:shortname',
            'theme:forcetheme',
        ];

        $this->add_columns_from_entities($columns);
        $this->set_initial_sort_column('course:fullname', SORT_ASC);
    }

    /**
     * Adds the filters we want to display in the report for 'course'.
     */
    protected function add_filters_course(): void {
        $filters = [
            'course:fullname',
            'course:shortname',
        ];

        $this->add_filters_from_entities($filters);
    }

    /**
     * Adds the columns we want to display in the report for 'cohort'.
     */
    protected function add_columns_cohort(): void {
        $columns = [
            'cohort:name',
            'cohort:context',
            'theme:forcetheme',
        ];

        $this->add_columns_from_entities($columns);
        $this->set_initial_sort_column('cohort:name', SORT_ASC);
    }

    /**
     * Adds the filters we want to display in the report for 'cohort'.
     */
    protected function add_filters_cohort(): void {
        $filters = [
            'cohort:name',
            'cohort:context',
        ];

        $this->add_filters_from_entities($filters);
    }

    /**
     * Adds the columns we want to display in the report for 'category'.
     */
    protected function add_columns_category(): void {
        $columns = [
            'course_category:name',
            'course_category:coursecount',
            'theme:forcetheme',
        ];

        $this->add_columns_from_entities($columns);
        $this->set_initial_sort_column('course_category:name', SORT_ASC);
    }

    /**
     * Adds the filters we want to display in the report for 'category'.
     */
    protected function add_filters_category(): void {
        $filters = [
            'course_category:name',
        ];

        $this->add_filters_from_entities($filters);
    }
}
