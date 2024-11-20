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

namespace core_reportbuilder\external;

use core_user;
use renderer_base;
use core\external\persistent_exporter;
use core_reportbuilder\datasource;
use core_reportbuilder\manager;
use core_reportbuilder\local\models\report;
use core_tag\external\{tag_item_exporter, util};
use core_user\external\user_summary_exporter;

/**
 * Custom report details exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_details_exporter extends persistent_exporter {

    /** @var report The persistent object we will export. */
    protected $persistent = null;

    /**
     * Return the name of the class we are exporting
     *
     * @return string
     */
    protected static function define_class(): string {
        return report::class;
    }

    /**
     * Return a list of additional properties used only for display
     *
     * @return array
     */
    protected static function define_other_properties(): array {
        return [
            'sourcename' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
            ],
            'tags' => [
                'type' => tag_item_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'modifiedby' => ['type' => user_summary_exporter::read_properties_definition()],
        ];
    }

    /**
     * Get additional values to inject while exporting
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output): array {
        $source = $this->persistent->get('source');
        $usermodified = core_user::get_user($this->persistent->get('usermodified'));

        return [
            'sourcename' => manager::report_source_exists($source, datasource::class) ? $source::get_name() : null,
            'tags' => util::get_item_tags('core_reportbuilder', 'reportbuilder_report', $this->persistent->get('id')),
            'modifiedby' => (new user_summary_exporter($usermodified))->export($output),
        ];
    }
}
