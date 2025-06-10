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
 * Testcase class for the tool_ally\componentsupport\page_component class,
 * for cases where the content can be null.
 *
 * @package   tool_ally
 * @author    Julian Tovar <julian.tovar@openlms.net>
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\local_content;
use tool_ally\componentsupport\page_component;

defined('MOODLE_INTERNAL') || die();

require_once('abstract_testcase.php');

class components_page_component_with_null_content_test extends abstract_testcase {

    /**
     * @var stdClass
     */
    private $course;

    /**
     * @var stdClass
     */
    private $page;

    public function setUp(): void {
        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $this->course = $gen->create_course();
        $this->page = $gen->create_module('page',
            [
                'course' => $this->course->id,
                'contentformat' => FORMAT_HTML,
                'content' => ''
            ]
        );
    }

    public function test_get_all_html_content_when_null_content() {
        $items = local_content::get_all_html_content($this->page->id, 'page', true);
        $this->assertEmpty($items[1]->content);
    }
}
