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
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core_table;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use context;
use core_table\local\filter\filterset;

/**
 * Interface dynamic.
 *
 * @package core_table
 */
interface dynamic {

    /**
     * Get the base url.
     *
     * @return moodle_url
     */
    public function get_base_url(): moodle_url;

    /**
     * Set the filterset filters build table object.
     *
     * @param filterset $filterset The filterset object to get the filters from.
     * @return void
     */
    public function set_filterset(filterset $filterset): void;

    /**
     * Get the currently defined filterset.
     *
     * @return filterset
     */
    public function get_filterset(): ?filterset;

    /**
     * Get the context of the current table.
     *
     * Note: This function should not be called until after the filterset has been provided.
     *
     * @return context
     */
    public function get_context(): ?context;
}
