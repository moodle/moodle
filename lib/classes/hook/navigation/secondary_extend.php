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

namespace core\hook\navigation;
use core\hook\stoppable_trait;
use core\navigation\views\secondary;

/**
 * Allows plugins to insert nodes into site secondary navigation
 *
 * @package    core
 * @author     Sumaiya Javed
 * @copyright  2024 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins to insert nodes into site secondary navigation')]
#[\core\attribute\tags('navigation')]
final class secondary_extend {
    use stoppable_trait;

    /**
     * Creates new hook.
     *
     * @param secondary $secondaryview secondary navigation view
     */
    public function __construct(
        /**
         * @var secondary $secondaryview secondary navigation view
         */
        public readonly secondary $secondaryview,
    ) {
    }

    /**
     * secondary navigation view
     *
     * @return secondary
     */
    public function get_secondaryview(): secondary {
        return $this->secondaryview;
    }
}
