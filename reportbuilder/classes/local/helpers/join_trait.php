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

namespace core_reportbuilder\local\helpers;

/**
 * Trait for classes that expect to store SQL table joins
 *
 * @package     core_reportbuilder
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait join_trait {

    /** @var string[] SQL table joins */
    private array $joins = [];

    /**
     * Add single SQL table join
     *
     * @param string $join
     * @return static
     */
    final public function add_join(string $join): static {
        $this->joins[trim($join)] = trim($join);
        return $this;
    }

    /**
     * Add multiple SQL table joins
     *
     * @param string[] $joins
     * @return static
     */
    final public function add_joins(array $joins): static {
        foreach ($joins as $join) {
            $this->add_join($join);
        }
        return $this;
    }

    /**
     * Return SQL table joins
     *
     * @return string[]
     */
    final public function get_joins(): array {
        return array_values($this->joins);
    }
}
