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
 * Setup trait.
 *
 * PHP Unit setUp method compatibility between multiple PHP versions.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\tests;

defined('MOODLE_INTERNAL') || die();

/**
 * Setup trait.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait setup_trait_310_onwards {

    /**
     * PHP Unit setup method.
     */
    public function setUp(): void {
        $this->setup_test();
    }

}

/**
 * Setup trait.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait setup_trait_pre_310 {

    /**
     * PHP Unit setup method.
     */
    public function setup() {
        $this->setup_test();
    }

}

if ($CFG->branch < 310) {
    /**
     * Setup trait.
     *
     * @package    block_xp
     * @copyright  2023 Frédéric Massart
     * @author     Frédéric Massart <fred@branchup.tech>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    trait setup_trait {
        use setup_trait_pre_310;

        /**
         * Alias for the standard method.
         */
        protected function setup_test() {
        }
    }
} else {
    /**
     * Setup trait.
     *
     * @package    block_xp
     * @copyright  2023 Frédéric Massart
     * @author     Frédéric Massart <fred@branchup.tech>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    trait setup_trait { // @codingStandardsIgnoreLine
        use setup_trait_310_onwards;

        /**
         * Alias for the standard method.
         */
        protected function setup_test() {
        }
    }
}
