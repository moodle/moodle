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

namespace core_reportbuilder;

use core_component;
use core_plugin_manager;
use stdClass;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\base;

/**
 * Report management class
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * Return an instance of a report class from the given report persistent
     *
     * @param report $report
     * @param array $parameters
     * @return base
     * @throws source_invalid_exception
     * @throws source_unavailable_exception
     */
    public static function get_report_from_persistent(report $report, array $parameters = []): base {
        $source = $report->get('source');

        // Throw exception for invalid or unavailable report source.
        if (!self::report_source_exists($source)) {
            throw new source_invalid_exception($source);
        } else if (!self::report_source_available($source)) {
            throw new source_unavailable_exception($source);
        }

        return new $source($report, $parameters);
    }

    /**
     * Return an instance of a report class from the given report ID
     *
     * @param int $reportid
     * @param array $parameters
     * @return base
     */
    public static function get_report_from_id(int $reportid, array $parameters = []): base {
        $report = new report($reportid);

        return self::get_report_from_persistent($report, $parameters);
    }

    /**
     * Verify that report source exists and extends appropriate base classes
     *
     * @param string $source Full namespaced path to report definition
     * @param string $additionalbaseclass Specify addition base class that given classname should extend
     * @return bool
     */
    public static function report_source_exists(string $source, string $additionalbaseclass = ''): bool {
        return (class_exists($source) && is_subclass_of($source, base::class) &&
            (empty($additionalbaseclass) || is_subclass_of($source, $additionalbaseclass)));
    }

    /**
     * Verify given report source is available. Note that it is assumed caller has already checked that it exists
     *
     * @param string $source
     * @return bool
     */
    public static function report_source_available(string $source): bool {
        return call_user_func([$source, 'is_available']);
    }

    /**
     * Create new report persistent
     *
     * @param stdClass $reportdata
     * @return report
     */
    public static function create_report_persistent(stdClass $reportdata): report {
        return (new report(0, $reportdata))->create();
    }

    /**
     * Return an array of all valid report sources across the site
     *
     * @return array[][] Indexed by [component => [class => name]]
     */
    public static function get_report_datasources(): array {
        $sources = array();

        $datasources = core_component::get_component_classes_in_namespace(null, 'reportbuilder\\datasource');
        foreach ($datasources as $class => $path) {
            if (self::report_source_exists($class, datasource::class) && self::report_source_available($class)) {
                [$component] = explode('\\', $class);

                if ($plugininfo = core_plugin_manager::instance()->get_plugin_info($component)) {
                    $componentname = $plugininfo->displayname;
                } else {
                    $componentname = get_string('site');
                }

                $sources[$componentname][$class] = call_user_func([$class, 'get_name']);
            }
        }

        return $sources;
    }
}
