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

namespace core_badges\output;

use renderable;
use templatable;
use moodle_page;

/**
 * Abstract class for the badges tertiary navigation. The class initialises the page and type class variables.
 *
 * @package   core_badges
 * @copyright 2021 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_action_bar implements renderable, templatable {
    /** @var moodle_page $page The context we are operating within. */
    protected $page;
    /** @var int $type The badge type. */
    protected $type;

    /**
     * standard_action_bar constructor.
     *
     * @param moodle_page $page
     * @param int $type
     */
    public function __construct(moodle_page $page, int $type) {
        $this->type = $type;
        $this->page = $page;
    }

    /**
     * The template that this tertiary nav should use.
     *
     * @return string
     */
    abstract public function get_template(): string;
}
