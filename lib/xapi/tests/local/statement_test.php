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
 * This file contains unit test related to xAPI library.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local;

use core_xapi\local\statement\item;
use core_xapi\local\statement\item_actor;
use core_xapi\local\statement\item_object;
use core_xapi\local\statement\item_activity;
use core_xapi\local\statement\item_verb;
use core_xapi\local\statement\item_agent;
use core_xapi\local\statement\item_group;
use core_xapi\xapi_exception;
use advanced_testcase;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Contains test cases for testing statement class.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class statement_testcase extends advanced_testcase {

    /**
     * Test statement creation.
     *
     * @dataProvider create_provider
     * @param bool $useagent if use agent as actor (or group if false)
     * @param array $extras
     */
    public function test_create(bool $useagent, array $extras) {

        $this->resetAfterTest();

        // Create one course with a group.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $user->id));

        // Our statement.
        $statement = new statement();

        // Populate statement.
        if ($useagent) {
            $statement->set_actor(item_agent::create_from_user($user));
        } else {
            $statement->set_actor(item_group::create_from_group($group));
        }
        $statement->set_verb(item_verb::create_from_id('cook'));
        $statement->set_object(item_activity::create_from_id('paella'));
        // For now, the rest of the optional properties have no validation
        // so we create a standard stdClass for all of them.
        $data = (object)[
            'some' => 'data',
        ];
        foreach ($extras as $extra) {
            $method = 'set_'.$extra;
            $statement->$method(item::create_from_data($data));
        }

        // Check resulting statement.
        if ($useagent) {
            $stuser = $statement->get_user();
            $this->assertEquals($user->id, $stuser->id);
            $stusers = $statement->get_all_users();
            $this->assertCount(1, $stusers);
        } else {
            $stgroup = $statement->get_group();
            $this->assertEquals($group->id, $stgroup->id);
            $stusers = $statement->get_all_users();
            $this->assertCount(1, $stusers);
            $stuser = array_shift($stusers);
            $this->assertEquals($user->id, $stuser->id);
        }
        $this->assertEquals('cook', $statement->get_verb_id());
        $this->assertEquals('paella', $statement->get_activity_id());

        // Check resulting json (only first node structure, internal structure
        // depends on every item json_encode test).
        $data = json_decode(json_encode($statement));
        $this->assertNotEmpty($data->actor);
        $this->assertNotEmpty($data->verb);
        $this->assertNotEmpty($data->object);
        $allextras = ['context', 'result', 'timestamp', 'stored', 'authority', 'version', 'attachments'];
        foreach ($allextras as $extra) {
            if (in_array($extra, $extras)) {
                $this->assertObjectHasAttribute($extra, $data);
                $this->assertNotEmpty($data->object);
            } else {
                $this->assertObjectNotHasAttribute($extra, $data);
            }
        }
    }

    /**
     * Data provider for the test_create and test_create_from_data tests.
     *
     * @return  array
     */
    public function create_provider() : array {
        return [
            'Agent statement with no extras' => [
                true, []
            ],
            'Agent statement with context' => [
                true, ['context']
            ],
            'Agent statement with result' => [
                true, ['result']
            ],
            'Agent statement with timestamp' => [
                true, ['timestamp']
            ],
            'Agent statement with stored' => [
                true, ['stored']
            ],
            'Agent statement with authority' => [
                true, ['authority']
            ],
            'Agent statement with version' => [
                true, ['version']
            ],
            'Agent statement with attachments' => [
                true, ['attachments']
            ],
            'Group statement with no extras' => [
                false, []
            ],
            'Group statement with context' => [
                false, ['context']
            ],
            'Group statement with result' => [
                false, ['result']
            ],
            'Group statement with timestamp' => [
                false, ['timestamp']
            ],
            'Group statement with stored' => [
                false, ['stored']
            ],
            'Group statement with authority' => [
                false, ['authority']
            ],
            'Group statement with version' => [
                false, ['version']
            ],
            'Group statement with attachments' => [
                false, ['attachments']
            ],
        ];
    }

    /**
     * Test statement creation from xAPI statement data.
     *
     * @dataProvider create_provider
     * @param bool $useagent if use agent as actor (or group if false)
     * @param array $extras
     */
    public function test_create_from_data(bool $useagent, array $extras) {
        $this->resetAfterTest();

        // Create one course with a group.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $user->id));

        // Populate data.
        if ($useagent) {
            $actor = item_agent::create_from_user($user);
        } else {
            $actor = item_group::create_from_group($group);
        }
        $verb = item_verb::create_from_id('cook');
        $object = item_activity::create_from_id('paella');

        $data = (object) [
            'actor' => $actor->get_data(),
            'verb' => $verb->get_data(),
            'object' => $object->get_data(),
        ];
        // For now, the rest of the optional properties have no validation
        // so we create a standard stdClass for all of them.
        $info = (object)[
            'some' => 'data',
        ];
        foreach ($extras as $extra) {
            $data->$extra = $info;
        }

        $statement = statement::create_from_data($data);

        // Check resulting statement.
        if ($useagent) {
            $stuser = $statement->get_user();
            $this->assertEquals($user->id, $stuser->id);
            $stusers = $statement->get_all_users();
            $this->assertCount(1, $stusers);
        } else {
            $stgroup = $statement->get_group();
            $this->assertEquals($group->id, $stgroup->id);
            $stusers = $statement->get_all_users();
            $this->assertCount(1, $stusers);
            $stuser = array_shift($stusers);
            $this->assertEquals($user->id, $stuser->id);
        }
        $this->assertEquals('cook', $statement->get_verb_id());
        $this->assertEquals('paella', $statement->get_activity_id());

        // Check resulting json (only first node structure, internal structure
        // depends on every item json_encode test).
        $data = json_decode(json_encode($statement));
        $this->assertNotEmpty($data->actor);
        $this->assertNotEmpty($data->verb);
        $this->assertNotEmpty($data->object);
        $allextras = ['context', 'result', 'timestamp', 'stored', 'authority', 'version', 'attachments'];
        foreach ($allextras as $extra) {
            if (in_array($extra, $extras)) {
                $this->assertObjectHasAttribute($extra, $data);
                $this->assertNotEmpty($data->object);
            } else {
                $this->assertObjectNotHasAttribute($extra, $data);
            }
        }
    }

    /**
     * Test all getters into a not set statement.
     *
     * @dataProvider invalid_gets_provider
     * @param string $method the method to test
     * @param bool $exception if an exception is expected
     */
    public function test_invalid_gets(string $method, bool $exception) {
        $statement = new statement();
        if ($exception) {
            $this->expectException(xapi_exception::class);
        }
        $result = $statement->$method();
        $this->assertNull($result);
    }

    /**
     * Data provider for the text_invalid_gets.
     *
     * @return  array
     */
    public function invalid_gets_provider() : array {
        return [
            'Method get_user on empty statement' => ['get_user', true],
            'Method get_all_users on empty statement' => ['get_all_users', true],
            'Method get_group on empty statement' => ['get_group', true],
            'Method get_verb_id on empty statement' => ['get_verb_id', true],
            'Method get_activity_id on empty statement' => ['get_activity_id', true],
            'Method get_actor on empty statement' => ['get_actor', false],
            'Method get_verb on empty statement' => ['get_verb', false],
            'Method get_object on empty statement' => ['get_object', false],
            'Method get_context on empty statement' => ['get_context', false],
            'Method get_result on empty statement' => ['get_result', false],
            'Method get_timestamp on empty statement' => ['get_timestamp', false],
            'Method get_stored on empty statement' => ['get_stored', false],
            'Method get_authority on empty statement' => ['get_authority', false],
            'Method get_version on empty statement' => ['get_version', false],
            'Method get_attachments on empty statement' => ['get_attachments', false],
        ];
    }

    /**
     * Try to get a user from a group statement.
     */
    public function test_invalid_get_user() {

        $this->resetAfterTest();

        // Create one course with a group.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $user->id));

        // Our statement.
        $statement = new statement();

        // Populate statement.
        $statement->set_actor(item_group::create_from_group($group));
        $statement->set_verb(item_verb::create_from_id('cook'));
        $statement->set_object(item_activity::create_from_id('paella'));

        $this->expectException(xapi_exception::class);
        $statement->get_user();
    }

    /**
     * Try to get a group from an agent statement.
     */
    public function test_invalid_get_group() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        // Our statement.
        $statement = new statement();

        // Populate statement.
        $statement->set_actor(item_agent::create_from_user($user));
        $statement->set_verb(item_verb::create_from_id('cook'));
        $statement->set_object(item_activity::create_from_id('paella'));

        $this->expectException(xapi_exception::class);
        $statement->get_group();
    }

    /**
     * Try to get activity Id from a statement with agent object.
     */
    public function test_invalid_get_activity_id() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        // Our statement.
        $statement = new statement();

        // Populate statement with and agent object.
        $statement->set_actor(item_agent::create_from_user($user));
        $statement->set_verb(item_verb::create_from_id('cook'));
        $statement->set_object(item_agent::create_from_user($user));

        $this->expectException(xapi_exception::class);
        $statement->get_activity_id();
    }

    /**
     * Test for invalid structures.
     *
     * @dataProvider invalid_data_provider
     * @param bool $useuser if use user into statement
     * @param bool $userverb if use verb into statement
     * @param bool $useobject if use object into statement
     */
    public function test_invalid_data(bool $useuser, bool $userverb, bool $useobject): void {

        $data = new stdClass();

        if ($useuser) {
            $this->resetAfterTest();
            $user = $this->getDataGenerator()->create_user();
            $data->actor = item_agent::create_from_user($user);
        }
        if ($userverb) {
            $data->verb = item_verb::create_from_id('cook');
        }
        if ($useobject) {
            $data->object = item_activity::create_from_id('paella');
        }

        $this->expectException(xapi_exception::class);
        $statement = statement::create_from_data($data);
    }

    /**
     * Data provider for the test_invalid_data tests.
     *
     * @return  array
     */
    public function invalid_data_provider() : array {
        return [
            'No actor, no verb, no object'  => [false, false, false],
            'No actor, verb, no object'     => [false, true, false],
            'No actor, no verb, object'     => [false, false, true],
            'No actor, verb, object'        => [false, true, true],
            'Actor, no verb, no object'     => [true, false, false],
            'Actor, verb, no object'        => [true, true, false],
            'Actor, no verb, object'        => [true, false, true],
        ];
    }

    /**
     * Test minify statement.
     */
    public function test_minify() {

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        // Our statement.
        $statement = new statement();

        // Populate statement.
        $statement->set_actor(item_agent::create_from_user($user));
        $statement->set_verb(item_verb::create_from_id('cook'));
        $statement->set_object(item_activity::create_from_id('paella'));
        // For now, the rest of the optional properties have no validation
        // so we create a standard stdClass for all of them.
        $data = (object)[
            'some' => 'data',
        ];
        $extras = ['context', 'result', 'timestamp', 'stored', 'authority', 'version', 'attachments'];
        foreach ($extras as $extra) {
            $method = 'set_'.$extra;
            $statement->$method(item::create_from_data($data));
        }

        $min = $statement->minify();

        // Check calculated fields.
        $this->assertCount(6, $min);
        $this->assertArrayNotHasKey('actor', $min);
        $this->assertArrayHasKey('verb', $min);
        $this->assertArrayHasKey('object', $min);
        $this->assertArrayHasKey('context', $min);
        $this->assertArrayHasKey('result', $min);
        $this->assertArrayNotHasKey('timestamp', $min);
        $this->assertArrayNotHasKey('stored', $min);
        $this->assertArrayHasKey('authority', $min);
        $this->assertArrayNotHasKey('version', $min);
        $this->assertArrayHasKey('attachments', $min);
    }
}
