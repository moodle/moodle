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
 * Test for file updates webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\webservice\file_updates;
use tool_ally\local;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for file updates webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_file_updates_test extends abstract_testcase {
    /**
     * Test the web service.
     */
    public function test_service() {
        global $DB;
        $this->resetAfterTest();
        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);

        $course      = $this->getDataGenerator()->create_course();
        $resource    = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $resource2   = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $filecreated = $this->get_resource_file($resource);
        $fileupdated = $this->get_resource_file($resource2);

        $since = new \DateTimeImmutable('October 21 2015', new \DateTimeZone('UTC'));
        $filedate = $since->add(new \DateInterval('P3D'));

        $tempobject = new \stdClass();
        $tempobject->id = $fileupdated->get_id();
        $tempobject->timemodified = $filedate->getTimestamp();
        $tempobject->timecreated = $filedate->getTimestamp() - DAYSECS;

        $DB->update_record('files', $tempobject);
        $fileupdated = $this->get_resource_file($resource2);

        $expectedfilecreated = [
            'entity_id'    => $filecreated->get_pathnamehash(),
            'context_id'   => $course->id,
            'event_name'   => 'file_created',
            'event_time'   => local::iso_8601($filecreated->get_timemodified()),
            'mime_type'    => $filecreated->get_mimetype(),
            'content_hash' => $filecreated->get_contenthash(),
        ];
        $expectedfileupdated = [
            'entity_id'    => $fileupdated->get_pathnamehash(),
            'context_id'   => $course->id,
            'event_name'   => 'file_updated',
            'event_time'   => local::iso_8601($fileupdated->get_timemodified()),
            'mime_type'    => $fileupdated->get_mimetype(),
            'content_hash' => $fileupdated->get_contenthash(),
        ];

        $files = file_updates::service($since->format(\DateTime::ISO8601));
        $files = \external_api::clean_returnvalue(file_updates::service_returns(), $files);

        $this->assertCount(2, $files);

        foreach ($files as $file) {
            if ($file['entity_id'] == $fileupdated->get_pathnamehash()) {
                $this->assertEquals($expectedfileupdated, $file);
            } else {
                $this->assertEquals($expectedfilecreated, $file);
            }
        }
    }
}
