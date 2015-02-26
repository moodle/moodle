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
 * Defines fixutres for unit testing of lib/classes/myprofile/.
 *
 * @package   core_user
 * @category  test
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class phpunit_fixture_myprofile_category
 *
 * @package   core_user
 * @category  test
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpunit_fixture_myprofile_category extends \core_user\output\myprofile\category {
    /**
     * Make protected method public for testing.
     */
    public function find_nodes_after($node) {
        return parent::find_nodes_after($node);
    }
}

/**
 * Class phpunit_fixture_myprofile_tree
 */
class phpunit_fixture_myprofile_tree extends \core_user\output\myprofile\tree {
    /**
     * Make protected method public for testing.
     */
    public function find_categories_after($cat) {
        return parent::find_categories_after($cat);
    }
}