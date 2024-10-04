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

namespace customfield_number\privacy;

use core_customfield\data_controller;
use core_customfield\privacy\customfield_provider;
use core_privacy\local\metadata\null_provider;
use core_privacy\local\request\writer;
use stdClass;

/**
 * Plugin privacy provider
 *
 * @package    customfield_number
 * @copyright  2024 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements null_provider, customfield_provider {

    /**
     * Plugin language string identifier to explain why this plugin stores no data
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }

    /**
     * Preprocesses data object that is going to be exported
     *
     * @param data_controller $data
     * @param stdClass $exportdata
     * @param array $subcontext
     */
    public static function export_customfield_data(data_controller $data, stdClass $exportdata, array $subcontext): void {
        writer::with_context($data->get_context())->export_data($subcontext, $exportdata);
    }

    /**
     * Callback to clean up any related files prior to data record deletion
     *
     * @param string $select
     * @param array $params
     * @param int[] $contextids
     */
    public static function before_delete_data(string $select, array $params, array $contextids): void {

    }

    /**
     * Callback to clean up any related field prior to field record deletion
     *
     * @param string $select
     * @param array $params
     * @param int[] $contextids
     */
    public static function before_delete_fields(string $select, array $params, array $contextids): void {

    }
}
