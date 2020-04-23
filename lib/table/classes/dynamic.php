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
 * This file contains the dynamic interface.
 *
 * @package core_table
 * @copyright 2020 Simey Lameze <simey@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core_table;

/**
 * Interface to identify this table as a table which can be dynamically updated via webservice calls.
 *
 * For a table to be defined as dynamic it must meet the following requirements:
 *
 * # it must be located with a namespaced class of \[component]\table\[tablename]
 * # it must define a \core_table\local\filter\filterset implementation in \[component]\table\[tablename]_filterset
 * # it must override the {{guess_base_url}} function and specify a base URL to be used when constructing URLs
 * # it must override the {{get_context}} function to specify the correct context
 *
 * @package core_table
 * @copyright 2020 Simey Lameze <simey@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface dynamic {
}
