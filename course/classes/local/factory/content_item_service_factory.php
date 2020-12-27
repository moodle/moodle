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
 * Contains the service_factory, a locator for services for course content items.
 *
 * Services encapsulate the business logic, and any data manipulation code, and are what clients should interact with.
 *
 * @package   core_course
 * @copyright 2020 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\local\factory;

defined('MOODLE_INTERNAL') || die();

use core_course\local\repository\caching_content_item_readonly_repository;
use core_course\local\repository\content_item_readonly_repository;
use core_course\local\service\content_item_service;

/**
 * Class service_factory, providing functions for location of service objects for course content items.
 *
 * This class is responsible for providing service objects to clients only.
 *
 * @copyright 2020 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item_service_factory {

    /**
     * Returns a basic service object providing operations for course content items.
     *
     * @return content_item_service
     */
    public static function get_content_item_service(): content_item_service {
        return new content_item_service(
            new caching_content_item_readonly_repository(
                \cache::make('core', 'user_course_content_items'),
                new content_item_readonly_repository()
            )
        );
    }
}
