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

namespace enrol_lti;

use enrol_lti\data_connector;
use IMSGlobal\LTI\ToolProvider\ConsumerNonce;
use IMSGlobal\LTI\ToolProvider\Context;
use IMSGlobal\LTI\ToolProvider\ResourceLink;
use IMSGlobal\LTI\ToolProvider\ResourceLinkShare;
use IMSGlobal\LTI\ToolProvider\ResourceLinkShareKey;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use IMSGlobal\LTI\ToolProvider\ToolProvider;
use IMSGlobal\LTI\ToolProvider\ToolProxy;
use IMSGlobal\LTI\ToolProvider\User;

/**
 * Test the data_connector class.
 *
 * @package enrol_lti
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class data_connector_test extends \advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        // Set this user as the admin.
        $this->setAdminUser();
    }

    /**
     * Test for data_connector::loadToolConsumer().
     */
    public function test_load_consumer(): void {
        $consumer = new ToolConsumer();
        $dc = new data_connector();

        // Consumer has not been saved to the database, so this should return false.
        $this->assertFalse($dc->loadToolConsumer($consumer));

        // Save a consumer into the DB.
        $time = time();
        $data = [
            'name' => 'TestName',
            'secret' => 'TestSecret',
            'ltiversion' => ToolProvider::LTI_VERSION1,
            'consumername' => 'TestConsumerName',
            'consumerversion' => 'TestConsumerVersion',
            'consumerguid' => 'TestConsumerGuid',
            'profile' => json_decode('{TestProfile}'),
            'toolproxy' => 'TestProxy',
            'settings' => ['setting1' => 'TestSetting 1', 'setting2' => 'TestSetting 2'],
            'protected' => 1,
            'enabled' => 0,
            'enablefrom' => $time,
            'enableuntil' => $time + 1,
            'lastaccess' => strtotime(date('Y-m-d')),
        ];
        $consumer->name = $data['name'];
        $consumer->setKey('TestKey');
        $consumer->secret = $data['secret'];
        $consumer->ltiVersion = $data['ltiversion'];
        $consumer->consumerName = $data['consumername'];
        $consumer->consumerVersion = $data['consumerversion'];
        $consumer->consumerGuid = $data['consumerguid'];
        $consumer->profile = $data['profile'];
        $consumer->toolProxy = $data['toolproxy'];
        $consumer->setSettings($data['settings']);
        $consumer->protected = true;
        $consumer->enabled = false;
        $consumer->enableFrom = $data['enablefrom'];
        $consumer->enableUntil = $data['enableuntil'];
        $consumer->lastAccess = $data['lastaccess'];

        $dc->saveToolConsumer($consumer);
        $this->assertTrue($dc->loadToolConsumer($consumer));
        $this->assertEquals($consumer->name, 'TestName');
        $this->assertEquals($consumer->getKey(), 'TestKey');
        $this->assertEquals($consumer->secret, 'TestSecret');
        $this->assertEquals($consumer->ltiVersion, $data['ltiversion']);
        $this->assertEquals($consumer->consumerName, $data['consumername']);
        $this->assertEquals($consumer->consumerVersion, $data['consumerversion']);
        $this->assertEquals($consumer->consumerGuid, $data['consumerguid']);
        $this->assertEquals($consumer->profile, $data['profile']);
        $this->assertEquals($consumer->toolProxy, $data['toolproxy']);
        $this->assertEquals($consumer->getSettings(), $data['settings']);
        $this->assertTrue($consumer->protected);
        $this->assertFalse($consumer->enabled);
        $this->assertEquals($consumer->enableFrom, $data['enablefrom']);
        $this->assertEquals($consumer->enableUntil, $data['enableuntil']);
        $this->assertEquals($consumer->lastAccess, $data['lastaccess']);
    }

    /**
     * Test for data_connector::saveToolConsumer().
     */
    public function test_save_consumer(): void {
        $dc = new data_connector();

        $time = time();
        $data = [
            'name' => 'TestName',
            'secret' => 'TestSecret',
            'ltiversion' => ToolProvider::LTI_VERSION1,
            'consumername' => 'TestConsumerName',
            'consumerversion' => 'TestConsumerVersion',
            'consumerguid' => 'TestConsumerGuid',
            'profile' => json_decode('{TestProfile}'),
            'toolproxy' => 'TestProxy',
            'settings' => ['setting1' => 'TestSetting 1', 'setting2' => 'TestSetting 2'],
            'protected' => 1,
            'enabled' => 0,
            'enablefrom' => $time,
            'enableuntil' => $time + 1,
            'lastaccess' => strtotime(date('Y-m-d')),
        ];
        $consumer = new ToolConsumer();
        $consumer->name = $data['name'];
        $consumer->setKey('TestKey');
        $consumer->secret = $data['secret'];
        $consumer->ltiVersion = $data['ltiversion'];
        $consumer->consumerName = $data['consumername'];
        $consumer->consumerVersion = $data['consumerversion'];
        $consumer->consumerGuid = $data['consumerguid'];
        $consumer->profile = $data['profile'];
        $consumer->toolProxy = $data['toolproxy'];
        $consumer->setSettings($data['settings']);
        $consumer->protected = true;
        $consumer->enabled = false;
        $consumer->enableFrom = $data['enablefrom'];
        $consumer->enableUntil = $data['enableuntil'];
        $consumer->lastAccess = $data['lastaccess'];

        // Save new consumer into the DB.
        $this->assertTrue($dc->saveToolConsumer($consumer));
        // Check saved values.
        $this->assertEquals($consumer->name, $data['name']);
        $this->assertEquals($consumer->getKey(), 'TestKey');
        $this->assertEquals($consumer->secret, $data['secret']);
        $this->assertEquals($consumer->ltiVersion, $data['ltiversion']);
        $this->assertEquals($consumer->consumerName, $data['consumername']);
        $this->assertEquals($consumer->consumerVersion, $data['consumerversion']);
        $this->assertEquals($consumer->consumerGuid, $data['consumerguid']);
        $this->assertEquals($consumer->profile, $data['profile']);
        $this->assertEquals($consumer->toolProxy, $data['toolproxy']);
        $this->assertEquals($consumer->getSettings(), $data['settings']);
        $this->assertTrue($consumer->protected);
        $this->assertFalse($consumer->enabled);
        $this->assertEquals($consumer->enableFrom, $data['enablefrom']);
        $this->assertEquals($consumer->enableUntil, $data['enableuntil']);
        $this->assertEquals($consumer->lastAccess, $data['lastaccess']);

        // Edit values.
        $edit = 'EDIT';
        $consumer->name = $data['name'] . $edit;
        $consumer->setKey('TestKey' . $edit);
        $consumer->secret = $data['secret'] . $edit;
        $consumer->ltiVersion = ToolProvider::LTI_VERSION2;
        $consumer->consumerName = $data['consumername'] . $edit;
        $consumer->consumerVersion = $data['consumerversion'] . $edit;
        $consumer->consumerGuid = $data['consumerguid'] . $edit;
        $editprofile = json_decode('{TestProfile}');
        $consumer->profile = $editprofile;
        $consumer->toolProxy = $data['toolproxy'] . $edit;
        $editsettings = ['setting1' => 'TestSetting 1'  . $edit, 'setting2' => 'TestSetting 2' . $edit];
        $consumer->setSettings($editsettings);
        $consumer->protected = null;
        $consumer->enabled = null;
        $consumer->enableFrom = $data['enablefrom'] + 100;
        $consumer->enableUntil = $data['enableuntil'] + 100;

        // Save edited values.
        $this->assertTrue($dc->saveToolConsumer($consumer));
        // Check edited values.
        $this->assertEquals($consumer->name, $data['name'] . $edit);
        $this->assertEquals($consumer->getKey(), 'TestKey' . $edit);
        $this->assertEquals($consumer->secret, $data['secret'] . $edit);
        $this->assertEquals($consumer->ltiVersion, ToolProvider::LTI_VERSION2);
        $this->assertEquals($consumer->consumerName, $data['consumername'] . $edit);
        $this->assertEquals($consumer->consumerVersion, $data['consumerversion'] . $edit);
        $this->assertEquals($consumer->consumerGuid, $data['consumerguid'] . $edit);
        $this->assertEquals($consumer->profile, $editprofile);
        $this->assertEquals($consumer->toolProxy, $data['toolproxy'] . $edit);
        $this->assertEquals($consumer->getSettings(), $editsettings);
        $this->assertNull($consumer->protected);
        $this->assertNull($consumer->enabled);
        $this->assertEquals($consumer->enableFrom, $data['enablefrom'] + 100);
        $this->assertEquals($consumer->enableUntil, $data['enableuntil'] + 100);
    }

    /**
     * Test for data_connector::deleteToolConsumer().
     */
    public function test_delete_tool_consumer(): void {
        $dc = new data_connector();
        $data = [
            'name' => 'TestName',
            'secret' => 'TestSecret',
            'ltiversion' => ToolProvider::LTI_VERSION1,
        ];
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = $data['name'];
        $consumer->setKey('TestKey');
        $consumer->secret = $data['secret'];
        $consumer->save();

        $nonce = new ConsumerNonce($consumer, 'testnonce');
        $nonce->save();

        $context = Context::fromConsumer($consumer, 'testlticontext');
        $context->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->setContextId($context->getRecordId());
        $resourcelink->save();
        $this->assertEquals($consumer->getRecordId(), $resourcelink->getConsumer()->getRecordId());

        $resourcelinkchild = ResourceLink::fromConsumer($consumer, 'testresourcelinkchildid');
        $resourcelinkchild->primaryResourceLinkId = $resourcelink->getRecordId();
        $resourcelinkchild->shareApproved = true;
        $resourcelinkchild->setContextId($context->getRecordId());
        $resourcelinkchild->save();
        $this->assertEquals($consumer->getRecordId(), $resourcelinkchild->getConsumer()->getRecordId());
        $this->assertEquals($resourcelink->getRecordId(), $resourcelinkchild->primaryResourceLinkId);
        $this->assertTrue($resourcelinkchild->shareApproved);

        $resourcelinkchild2 = clone $resourcelink;
        $resourcelinkchild2->setRecordId(null);
        $resourcelinkchild2->setConsumerId(null);
        $resourcelinkchild2->setContextId(0);
        $resourcelinkchild2->primaryResourceLinkId = $resourcelink->getRecordId();
        $resourcelinkchild2->shareApproved = true;
        $resourcelinkchild2->save();
        $this->assertNull($resourcelinkchild2->getConsumer()->getRecordId());
        $this->assertEquals(0, $resourcelinkchild2->getContextId());
        $this->assertNotEquals($resourcelink->getRecordId(), $resourcelinkchild2->getRecordId());

        $resourcelinksharekey = new ResourceLinkShareKey($resourcelink);
        $resourcelinksharekey->save();

        $user = User::fromResourceLink($resourcelink, '');
        $user->ltiResultSourcedId = 'testLtiResultSourcedId';
        $dc->saveUser($user);

        // Confirm that tool consumer deletion processing ends successfully.
        $this->assertTrue($dc->deleteToolConsumer($consumer));

        // Consumer object should have been initialised.
        foreach ($consumer as $key => $value) {
            $this->assertTrue(empty($value));
        }

        // Nonce record should have been deleted.
        $this->assertFalse($dc->loadConsumerNonce($nonce));
        // Share key record record should have been deleted.
        $this->assertFalse($dc->loadResourceLinkShareKey($resourcelinksharekey));
        // Resource record link should have been deleted.
        $this->assertFalse($dc->loadResourceLink($resourcelink));
        // Consumer record should have been deleted.
        $this->assertFalse($dc->loadToolConsumer($consumer));
        // Resource links for contexts in this consumer should have been deleted. Even child ones.
        $this->assertFalse($dc->loadResourceLink($resourcelinkchild));

        // Child resource link primaryResourceLinkId and shareApproved attributes should have been set to null.
        $this->assertTrue($dc->loadResourceLink($resourcelinkchild2));
        $this->assertNull($resourcelinkchild2->primaryResourceLinkId);
        $this->assertNull($resourcelinkchild2->shareApproved);
    }

    /**
     * Test for data_connector::getToolConsumers().
     */
    public function test_get_tool_consumers(): void {
        $dc = new data_connector();

        $consumers = $dc->getToolConsumers();
        // Does not return null.
        $this->assertNotNull($consumers);
        // But returns empty array when no consumers found.
        $this->assertEmpty($consumers);

        $data = [
            'name' => 'TestName',
            'secret' => 'TestSecret',
            'ltiversion' => ToolProvider::LTI_VERSION1,
        ];
        $count = 3;
        for ($i = 0; $i < $count; $i++) {
            $consumer = new ToolConsumer(null, $dc);
            $consumer->name = $data['name'] . $i;
            $consumer->setKey('TestKey' . $i);
            $consumer->secret = $data['secret'] . $i;
            $consumer->ltiVersion = $data['ltiversion'];
            $consumer->save();
        }

        $consumers = $dc->getToolConsumers();

        $this->assertNotEmpty($consumers);
        $this->assertCount($count, $consumers);

        // Check values.
        foreach ($consumers as $index => $record) {
            $this->assertEquals($data['name'] . $index, $record->name);
            $this->assertEquals('TestKey' . $index, $record->getKey());
            $this->assertEquals($data['secret'] . $index, $record->secret);
            $record->ltiVersion = $data['ltiversion'];
        }
    }

    /**
     * Test for data_connector::loadToolProxy().
     */
    public function test_get_tool_proxy(): void {
        $dc = new data_connector();
        $toolproxy = new ToolProxy($dc);
        $this->assertFalse($dc->loadToolProxy($toolproxy));
    }

    /**
     * Test for data_connector::saveToolProxy().
     */
    public function test_save_tool_proxy(): void {
        $dc = new data_connector();
        $toolproxy = new ToolProxy($dc);
        $this->assertFalse($dc->saveToolProxy($toolproxy));
    }

    /**
     * Test for data_connector::deleteToolProxy().
     */
    public function test_delete_tool_proxy(): void {
        $dc = new data_connector();
        $toolproxy = new ToolProxy($dc);
        $this->assertFalse($dc->deleteToolProxy($toolproxy));
    }

    /**
     * Test for data_connector::loadContext().
     */
    public function test_load_context(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'testconsumername';
        $consumer->setKey('TestKey');
        $consumer->secret = 'testsecret';
        $consumer->save();

        $title = 'testcontexttitle';
        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->title = $title;
        $context->settings = $settings;

        // Load an unsaved context.
        $this->assertFalse($dc->loadContext($context));

        // Save the context.
        $dc->saveContext($context);
        $created = $context->created;
        $updated = $context->updated;

        // Load saved context.
        $this->assertTrue($dc->loadContext($context));
        $this->assertEquals($consumer, $context->getConsumer());
        $this->assertEquals($title, $context->title);
        $this->assertEquals($settings, $context->getSettings());
        $this->assertEquals($lticontextid, $context->ltiContextId);
        $this->assertEquals($created, $context->created);
        $this->assertEquals($updated, $context->updated);
    }

    /**
     * Test for data_connector::saveContext().
     */
    public function test_save_context(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'testconsumername';
        $consumer->setKey('TestKey');
        $consumer->secret = 'testsecret';
        $consumer->save();

        $title = 'testcontexttitle';
        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->title = $title;
        $context->settings = $settings;

        // Save the context.
        $this->assertTrue($dc->saveContext($context));
        $id = $context->getRecordId();
        $created = $context->created;
        $updated = $context->updated;

        // Check saved values.
        $this->assertNotNull($id);
        $this->assertNotEmpty($created);
        $this->assertNotEmpty($updated);
        $this->assertEquals($consumer, $context->getConsumer());
        $this->assertEquals($title, $context->title);
        $this->assertEquals($settings, $context->getSettings());
        $this->assertEquals($lticontextid, $context->ltiContextId);

        // Edit the context details.
        $newsettings = array_merge($settings, ['d', 'e']);
        $context->title = $title . 'edited';
        $context->settings = $newsettings;
        $context->ltiContextId = $lticontextid . 'edited';

        // Confirm that edited context is saved successfully.
        $this->assertTrue($dc->saveContext($context));

        // Check edited values.
        $this->assertEquals($title . 'edited', $context->title);
        $this->assertEquals($newsettings, $context->getSettings());
        $this->assertEquals($lticontextid . 'edited', $context->ltiContextId);
        // Created time stamp should not change.
        $this->assertEquals($created, $context->created);
        // Updated time stamp should have been changed.
        $this->assertGreaterThanOrEqual($updated, $context->updated);
    }

    /**
     * Test for data_connector::deleteContext().
     */
    public function test_delete_context(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'testconsumername';
        $consumer->setKey('TestKey');
        $consumer->secret = 'testsecret';
        $consumer->save();

        $title = 'testcontexttitle';
        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->title = $title;
        $context->settings = $settings;

        // Save the context.
        $this->assertTrue($dc->saveContext($context));

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->setContextId($context->getRecordId());
        $resourcelink->save();
        $this->assertEquals($consumer->getRecordId(), $resourcelink->getConsumer()->getRecordId());

        $resourcelinkchild = ResourceLink::fromConsumer($consumer, 'testresourcelinkchildid');
        $resourcelinkchild->primaryResourceLinkId = $resourcelink->getRecordId();
        $resourcelinkchild->shareApproved = true;
        $resourcelinkchild->setContextId($context->getRecordId());
        $resourcelinkchild->save();
        $this->assertEquals($consumer->getRecordId(), $resourcelinkchild->getConsumer()->getRecordId());
        $this->assertEquals($resourcelink->getRecordId(), $resourcelinkchild->primaryResourceLinkId);
        $this->assertTrue($resourcelinkchild->shareApproved);

        $resourcelinkchild2 = clone $resourcelink;
        $resourcelinkchild2->setRecordId(null);
        $resourcelinkchild2->setConsumerId(null);
        $resourcelinkchild2->setContextId(0);
        $resourcelinkchild2->primaryResourceLinkId = $resourcelink->getRecordId();
        $resourcelinkchild2->shareApproved = true;
        $resourcelinkchild2->save();
        $this->assertNull($resourcelinkchild2->getConsumer()->getRecordId());
        $this->assertEquals(0, $resourcelinkchild2->getContextId());
        $this->assertNotEquals($resourcelink->getRecordId(), $resourcelinkchild2->getRecordId());

        $resourcelinksharekey = new ResourceLinkShareKey($resourcelink);
        $resourcelinksharekey->save();

        $user = User::fromResourceLink($resourcelink, '');
        $user->ltiResultSourcedId = 'testLtiResultSourcedId';
        $dc->saveUser($user);

        $this->assertTrue($dc->deleteContext($context));

        // Context properties should have been reset.
        $this->assertEmpty($context->title);
        $this->assertEmpty($context->settings);
        $this->assertNull($context->created);
        $this->assertNull($context->updated);

        // Context record should have already been deleted from the DB.
        $this->assertFalse($dc->loadContext($context));

        // Share key record record should have been deleted.
        $this->assertFalse($dc->loadResourceLinkShareKey($resourcelinksharekey));
        // Resource record link should have been deleted.
        $this->assertFalse($dc->loadResourceLink($resourcelink));
        // Resource links for contexts in this consumer should have been deleted. Even child ones.
        $this->assertFalse($dc->loadResourceLink($resourcelinkchild));

        // Child resource link primaryResourceLinkId and shareApproved attributes should have been set to null.
        $this->assertTrue($dc->loadResourceLink($resourcelinkchild2));
        $this->assertNull($resourcelinkchild2->primaryResourceLinkId);
        $this->assertNull($resourcelinkchild2->shareApproved);
    }

    /**
     * Test for data_connector::loadResourceLink().
     */
    public function test_load_resource_link(): void {
        $dc = new data_connector();

        // Consumer for the resource link.
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'testconsumername';
        $consumer->setKey('TestKey');
        $consumer->secret = 'testsecret';
        $consumer->save();

        // Context for the resource link.
        $title = 'testcontexttitle';
        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->title = $title;
        $context->settings = $settings;
        // Save the context.
        $context->save();

        $ltiresourcelinkid = 'testltiresourcelinkid';
        $resourcelink = ResourceLink::fromConsumer($consumer, $ltiresourcelinkid);
        $resourcelink->setContextId($context->getRecordId());
        $resourcelink->setSettings($settings);
        $resourcelink->shareApproved = true;
        $resourcelink->primaryResourceLinkId = 999;

        // Try to load an unsaved resource link.
        $this->assertFalse($dc->loadResourceLink($resourcelink));

        // Save the resource link.
        $resourcelink->save();

        // Load saved resource link.
        $this->assertTrue($dc->loadResourceLink($resourcelink));
        $this->assertNotEmpty($resourcelink->getRecordId());
        $this->assertEquals($settings, $resourcelink->getSettings());
        $this->assertTrue($resourcelink->shareApproved);
        $this->assertEquals(999, $resourcelink->primaryResourceLinkId);
        $this->assertNotEmpty($resourcelink->created);
        $this->assertNotEmpty($resourcelink->updated);

        // Create another resource link instance similar to the first one.
        $resourcelink2 = ResourceLink::fromConsumer($consumer, $ltiresourcelinkid);
        $resourcelink2->setContextId($context->getRecordId());

        // This should load the previous resource link.
        $this->assertTrue($dc->loadResourceLink($resourcelink2));
        $this->assertEquals($resourcelink, $resourcelink2);

        $resourcelink2->ltiResourceLinkId = $ltiresourcelinkid . '2';
        $resourcelink2->save();
        $dc->loadResourceLink($resourcelink2);
        $this->assertNotEquals($resourcelink, $resourcelink2);
    }

    /**
     * Test for data_connector::saveResourceLink().
     */
    public function test_save_resource_link(): void {
        $dc = new data_connector();

        // Consumer for the resource link.
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'testconsumername';
        $consumer->setKey('TestKey');
        $consumer->secret = 'testsecret';
        $consumer->save();

        // Context for the resource link.
        $title = 'testcontexttitle';
        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->title = $title;
        $context->settings = $settings;
        // Save the context.
        $context->save();

        $ltiresourcelinkid = 'testltiresourcelinkid';
        $resourcelink = ResourceLink::fromConsumer($consumer, $ltiresourcelinkid);
        $resourcelink->setContextId($context->getRecordId());
        $resourcelink->setSettings($settings);
        $resourcelink->shareApproved = true;
        $resourcelink->primaryResourceLinkId = 999;

        // Try to load an unsaved resource link.
        $this->assertFalse($dc->loadResourceLink($resourcelink));

        // Save the resource link.
        $this->assertTrue($resourcelink->save());

        // Check values.
        $resoucelinkid = $resourcelink->getRecordId();
        $created = $resourcelink->created;
        $updated = $resourcelink->updated;
        $this->assertNotEmpty($resoucelinkid);
        $this->assertEquals($settings, $resourcelink->getSettings());
        $this->assertTrue($resourcelink->shareApproved);
        $this->assertEquals(999, $resourcelink->primaryResourceLinkId);
        $this->assertNotEmpty($created);
        $this->assertNotEmpty($updated);

        // Update values.
        $newsettings = array_merge($settings, ['d', 'e']);
        $resourcelink->setSettings($newsettings);
        $resourcelink->shareApproved = false;
        $resourcelink->primaryResourceLinkId = 1000;
        $resourcelink->ltiResourceLinkId = $ltiresourcelinkid . 'edited';

        // Save modified resource link.
        $this->assertTrue($resourcelink->save());

        // Check edited values.
        $this->assertEquals($resoucelinkid, $resourcelink->getRecordId());
        $this->assertEquals($newsettings, $resourcelink->getSettings());
        $this->assertFalse($resourcelink->shareApproved);
        $this->assertEquals(1000, $resourcelink->primaryResourceLinkId);
        $this->assertEquals($created, $resourcelink->created);
        $this->assertGreaterThanOrEqual($updated, $resourcelink->updated);
    }

    /**
     * Test for data_connector::deleteResourceLink().
     */
    public function test_delete_resource_link(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'testconsumername';
        $consumer->setKey('TestKey');
        $consumer->secret = 'testsecret';
        $consumer->save();

        $title = 'testcontexttitle';
        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->title = $title;
        $context->settings = $settings;

        // Save the context.
        $this->assertTrue($dc->saveContext($context));

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->setContextId($context->getRecordId());
        $resourcelink->save();

        $resourcelinkchild = ResourceLink::fromConsumer($consumer, 'testresourcelinkchildid');
        $resourcelinkchild->primaryResourceLinkId = $resourcelink->getRecordId();
        $resourcelinkchild->shareApproved = true;
        $resourcelinkchild->setContextId($context->getRecordId());
        $resourcelinkchild->save();

        $resourcelinksharekey = new ResourceLinkShareKey($resourcelink);
        $resourcelinksharekey->save();

        $user = User::fromResourceLink($resourcelink, '');
        $user->ltiResultSourcedId = 'testLtiResultSourcedId';
        $dc->saveUser($user);

        $this->assertTrue($dc->deleteResourceLink($resourcelink));

        // Resource link properties should have been reset.
        $this->assertEmpty($resourcelink->title);
        $this->assertEmpty($resourcelink->getSettings());
        $this->assertNull($resourcelink->groupSets);
        $this->assertNull($resourcelink->groups);
        $this->assertNull($resourcelink->primaryResourceLinkId);
        $this->assertNull($resourcelink->shareApproved);
        $this->assertNull($resourcelink->created);
        $this->assertNull($resourcelink->updated);

        // Share key record record should have been deleted.
        $this->assertFalse($dc->loadResourceLinkShareKey($resourcelinksharekey));
        // Resource link record should have been deleted.
        $this->assertFalse($dc->loadResourceLink($resourcelink));
        // Child resource link should still exist and its primaryResourceLinkId attribute should have been set to null.
        $this->assertTrue($dc->loadResourceLink($resourcelinkchild));
        $this->assertNull($resourcelinkchild->primaryResourceLinkId);
    }

    /**
     * Test for data_connector::getUserResultSourcedIDsResourceLink().
     */
    public function test_get_user_result_sourced_ids_resource_link(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'testconsumername';
        $consumer->setKey('TestKey');
        $consumer->secret = 'testsecret';
        $consumer->idScope = ToolProvider::ID_SCOPE_GLOBAL;
        $consumer->save();

        $title = 'testcontexttitle';
        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->title = $title;
        $context->settings = $settings;
        $context->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->setContextId($context->getRecordId());
        $resourcelink->save();

        $resourcelinkchild = ResourceLink::fromConsumer($consumer, 'testresourcelinkchildid');
        $resourcelinkchild->primaryResourceLinkId = $resourcelink->getRecordId();
        $resourcelinkchild->shareApproved = true;
        $resourcelinkchild->setContextId($context->getRecordId());
        $resourcelinkchild->save();

        $user = User::fromResourceLink($resourcelink, '');
        $user->ltiResultSourcedId = 'testLtiResultSourcedId';
        $user->ltiUserId = 'user1';
        $dc->saveUser($user);

        $user2 = User::fromResourceLink($resourcelinkchild, '');
        $user2->ltiResultSourcedId = 'testLtiResultSourcedId2';
        $user->ltiUserId = 'user2';
        $dc->saveUser($user2);

        $users = $dc->getUserResultSourcedIDsResourceLink($resourcelink, false, null);
        $this->assertNotEmpty($users);
        $this->assertCount(2, $users);

        $users = $dc->getUserResultSourcedIDsResourceLink($resourcelink, true, null);
        $this->assertNotEmpty($users);
        $this->assertCount(1, $users);

        $users = $dc->getUserResultSourcedIDsResourceLink($resourcelink, false, ToolProvider::ID_SCOPE_GLOBAL);
        $this->assertNotEmpty($users);
        $this->assertCount(2, $users);

        $users = $dc->getUserResultSourcedIDsResourceLink($resourcelink, true, ToolProvider::ID_SCOPE_GLOBAL);
        $this->assertNotEmpty($users);
        $this->assertCount(1, $users);
    }

    /**
     * Test for data_connector::getSharesResourceLink().
     */
    public function test_get_shares_resource_link(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'testconsumername';
        $consumer->setKey('TestKey');
        $consumer->secret = 'testsecret';
        $consumer->idScope = ToolProvider::ID_SCOPE_GLOBAL;
        $consumer->save();

        $title = 'testcontexttitle';
        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->title = $title;
        $context->settings = $settings;
        $context->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->setContextId($context->getRecordId());
        $resourcelink->save();
        $shares = $dc->getSharesResourceLink($resourcelink);
        $this->assertEmpty($shares);

        $resourcelinkchild = ResourceLink::fromConsumer($consumer, 'testresourcelinkchildid');
        $resourcelinkchild->primaryResourceLinkId = $resourcelink->getRecordId();
        $resourcelinkchild->shareApproved = true;
        $resourcelinkchild->save();

        $resourcelinkchild2 = ResourceLink::fromConsumer($consumer, 'testresourcelinkchildid2');
        $resourcelinkchild2->primaryResourceLinkId = $resourcelink->getRecordId();
        $resourcelinkchild2->shareApproved = false;
        $resourcelinkchild2->save();

        $shares = $dc->getSharesResourceLink($resourcelink);
        $this->assertCount(2, $shares);
        $shareids = [$resourcelinkchild->getRecordId(), $resourcelinkchild2->getRecordId()];
        foreach ($shares as $share) {
            $this->assertTrue($share instanceof ResourceLinkShare);
            $this->assertTrue(in_array($share->resourceLinkId, $shareids));
            if ($share->resourceLinkId == $shareids[0]) {
                $this->assertTrue($share->approved);
            } else {
                $this->assertFalse($share->approved);
            }
        }
    }

    /**
     * Test for data_connector::loadConsumerNonce().
     */
    public function test_load_consumer_nonce(): void {
        $dc = new data_connector();

        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $nonce = new ConsumerNonce($consumer, 'testnonce');
        // Should still not be available since it has not been saved yet.
        $this->assertFalse($dc->loadConsumerNonce($nonce));
        // Save the nonce.
        $nonce->save();
        // Should now be available.
        $this->assertTrue($dc->loadConsumerNonce($nonce));
    }

    /**
     * Test for data_connector::loadConsumerNonce() for a nonce that has expired.
     */
    public function test_load_consumer_nonce_expired(): void {
        $dc = new data_connector();

        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $nonce = new ConsumerNonce($consumer, 'testnonce');
        $nonce->expires = time() - 100;
        // Save the nonce.
        $nonce->save();
        // Expired nonce should have been deleted.
        $this->assertFalse($dc->loadConsumerNonce($nonce));
    }

    /**
     * Test for data_connector::saveConsumerNonce().
     */
    public function test_save_consumer_nonce(): void {
        $dc = new data_connector();

        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $nonce = new ConsumerNonce($consumer, 'testnonce');

        // Save the nonce.
        $this->assertTrue($dc->saveConsumerNonce($nonce));
    }

    /**
     * Test for data_connector::loadResourceLinkShareKey().
     */
    public function test_load_resource_link_share_key(): void {
        $dc = new data_connector();

        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->save();

        $sharekey = new ResourceLinkShareKey($resourcelink);
        // Should still not be available since it has not been saved yet.
        $this->assertFalse($dc->loadResourceLinkShareKey($sharekey));
        // Save the share key.
        $sharekey->save();
        // Should now be available.
        $this->assertTrue($dc->loadResourceLinkShareKey($sharekey));

        // Check values.
        $this->assertEquals(strlen($sharekey->getId()), $sharekey->length);
        $this->assertEquals(ResourceLinkShareKey::DEFAULT_SHARE_KEY_LIFE, $sharekey->life);
        $this->assertNotNull($sharekey->expires);
        $this->assertFalse($sharekey->autoApprove);
    }

    /**
     * Test for data_connector::loadResourceLinkShareKey() with an expired share key.
     */
    public function test_load_resource_link_share_key_expired(): void {
        $dc = new data_connector();

        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->save();

        $sharekey = new ResourceLinkShareKey($resourcelink, 'testsharelinkid');
        $sharekey->expires = time() - 100;
        // ResourceLinkShareKey::save() adds a default expires time and cannot be modified.
        $dc->saveResourceLinkShareKey($sharekey);

        // Expired shared key should have been deleted.
        $this->assertFalse($dc->loadResourceLinkShareKey($sharekey));
    }

    /**
     * Test for data_connector::saveResourceLinkShareKey().
     */
    public function test_save_resource_link_share_key(): void {
        $dc = new data_connector();

        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->save();

        $expires = time() - 100;
        $sharekey = new ResourceLinkShareKey($resourcelink, 'testsharelinkid');
        $sharekey->expires = $expires;
        $sharekey->life = ResourceLinkShareKey::DEFAULT_SHARE_KEY_LIFE;

        $this->assertTrue($dc->saveResourceLinkShareKey($sharekey));

        // Check values.
        $this->assertEquals(strlen($sharekey->getId()), $sharekey->length);
        $this->assertEquals(ResourceLinkShareKey::DEFAULT_SHARE_KEY_LIFE, $sharekey->life);
        $this->assertEquals($expires, $sharekey->expires);
        $this->assertFalse($sharekey->autoApprove);
    }

    /**
     * Test for data_connector::deleteResourceLinkShareKey().
     */
    public function test_delete_resource_link_share_key(): void {
        $dc = new data_connector();

        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->save();

        $sharekey = new ResourceLinkShareKey($resourcelink, 'testsharelinkid');
        $sharekey->save();

        $this->assertTrue($dc->deleteResourceLinkShareKey($sharekey));

        $controlsharekey = new ResourceLinkShareKey($resourcelink, 'testsharelinkid');
        $controlsharekey->initialise();
        $this->assertEquals($controlsharekey, $sharekey);

        // This should no longer be in the DB.
        $this->assertFalse($dc->loadResourceLinkShareKey($sharekey));
    }

    /**
     * Test for data_connector::loadUser().
     */
    public function test_load_user(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->save();

        $user = User::fromResourceLink($resourcelink, '');
        $user->ltiResultSourcedId = 'testLtiResultSourcedId';

        // Should still not be available since it has not been saved yet.
        $this->assertFalse($dc->loadUser($user));

        // Save the user.
        $user->save();

        // Should now be available.
        $this->assertTrue($dc->loadUser($user));

        // Check loaded values.
        $created = $user->created;
        $updated = $user->updated;
        $this->assertNotNull($created);
        $this->assertNotNull($updated);
        $this->assertEquals('testLtiResultSourcedId', $user->ltiResultSourcedId);
        $this->assertEquals($resourcelink, $user->getResourceLink());
    }

    /**
     * Test for data_connector::saveUser().
     */
    public function test_save_user(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->save();

        $user = User::fromResourceLink($resourcelink, '');
        $user->ltiResultSourcedId = 'testLtiResultSourcedId';
        // Save user.
        $this->assertTrue($dc->saveUser($user));

        // Check loaded values.
        $created = $user->created;
        $updated = $user->updated;
        $this->assertNotNull($created);
        $this->assertNotNull($updated);
        $this->assertEquals('testLtiResultSourcedId', $user->ltiResultSourcedId);
        $this->assertEquals($resourcelink, $user->getResourceLink());

        // Update user.
        $user->ltiResultSourcedId = 'testLtiResultSourcedId2';

        // Save updated values.
        $this->assertTrue($dc->saveUser($user));

        // Check updated values.
        $this->assertEquals($created, $user->created);
        $this->assertGreaterThanOrEqual($updated, $user->updated);
        $this->assertEquals('testLtiResultSourcedId2', $user->ltiResultSourcedId);
    }

    /**
     * Test for data_connector::deleteUser().
     */
    public function test_delete_user(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->save();

        $user = User::fromResourceLink($resourcelink, '');
        $user->ltiResultSourcedId = 'testLtiResultSourcedId';
        $user->firstname = 'First';
        $user->lastname = 'Last';
        $user->fullname = 'Full name';
        $user->email = 'test@email.com';
        $user->roles = ['a', 'b'];
        $user->groups = ['1', '2'];
        $user->save();

        // Delete user.
        $this->assertTrue($dc->deleteUser($user));

        // User record should have been deleted from the DB.
        $this->assertFalse($dc->loadUser($user));

        // User object should have been initialised().
        $this->assertEmpty($user->firstname);
        $this->assertEmpty($user->lastname);
        $this->assertEmpty($user->fullname);
        $this->assertEmpty($user->email);
        $this->assertEmpty($user->roles);
        $this->assertEmpty($user->groups);
        $this->assertNull($user->ltiResultSourcedId);
        $this->assertNull($user->created);
        $this->assertNull($user->updated);
    }

    /**
     * Test for data_connector::get_contexts_from_consumer().
     */
    public function test_get_contexts_from_consumer(): void {
        $dc = new data_connector();
        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'testconsumername';
        $consumer->setKey('TestKey');
        $consumer->secret = 'testsecret';
        $consumer->save();

        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->settings = $settings;
        $context->save();
        $dc->loadContext($context);

        $consumer2 = new ToolConsumer(null, $dc);
        $consumer2->name = 'testconsumername2';
        $consumer2->setKey('TestKey2');
        $consumer2->secret = 'testsecret2';
        $consumer2->save();

        $context2 = Context::fromConsumer($consumer2, $lticontextid . '2');
        $context2->settings = $settings;
        $consumer2->save();

        $contexts = $dc->get_contexts_from_consumer($consumer);
        $this->assertCount(1, $contexts);
        $this->assertEquals($context, $contexts[0]);
    }

    /**
     * Test for data_connector::get_consumers_mapped_to_tool().
     */
    public function test_get_consumers_mapped_to_tool(): void {
        $generator = $this->getDataGenerator();
        // Create two tools belonging to the same course.
        $course1 = $generator->create_course();
        $data = new \stdClass();
        $data->courseid = $course1->id;
        $tool = $generator->create_lti_tool($data);
        $tool2 = $generator->create_lti_tool($data);

        $dc = new data_connector();
        $consumer = new ToolConsumer('key1', $dc);
        $consumer->name = 'testconsumername';
        $consumer->secret = 'testsecret';
        $consumer->save();

        $tp = new \enrol_lti\tool_provider($tool->id);
        $tp->consumer = $consumer;
        $tp->map_tool_to_consumer();

        $consumer2 = new ToolConsumer('key2', $dc);
        $consumer2->name = 'testconsumername2';
        $consumer2->secret = 'testsecret2';
        $consumer2->save();

        $tp2 = new \enrol_lti\tool_provider($tool2->id);
        $tp2->consumer = $consumer2;
        $tp2->map_tool_to_consumer();

        $consumers = $dc->get_consumers_mapped_to_tool($tool->id);
        $this->assertCount(1, $consumers);
        $this->assertEquals($consumer, $consumers[0]);

        $consumers2 = $dc->get_consumers_mapped_to_tool($tool2->id);
        $this->assertCount(1, $consumers2);
        $this->assertEquals($consumer2, $consumers2[0]);
    }

    /**
     * Test for data_connector::get_resourcelink_from_consumer()
     */
    public function test_get_resourcelink_from_consumer(): void {
        $dc = new data_connector();

        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        // No ResourceLink associated with the ToolConsumer yet.
        $this->assertNull($dc->get_resourcelink_from_consumer($consumer));

        // Create and save ResourceLink from ToolConsumer.
        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->save();
        $dc->loadResourceLink($resourcelink);

        // Assert that the resource link and the one fetched by get_resourcelink_from_consumer() are the same.
        $this->assertEquals($resourcelink, $dc->get_resourcelink_from_consumer($consumer));
    }

    /**
     * Test for data_connector::get_resourcelink_from_context()
     */
    public function test_get_resourcelink_from_context(): void {
        $dc = new data_connector();

        $consumer = new ToolConsumer(null, $dc);
        $consumer->name = 'TestName';
        $consumer->setKey('TestKey');
        $consumer->secret = 'TestSecret';
        $consumer->save();

        $settings = ['a', 'b', 'c'];
        $lticontextid = 'testlticontextid';
        $context = Context::fromConsumer($consumer, $lticontextid);
        $context->settings = $settings;
        $context->save();

        // No ResourceLink associated with the Context yet.
        $this->assertNull($dc->get_resourcelink_from_context($context));

        // Create and save ResourceLink from the Context.
        $resourcelink = ResourceLink::fromContext($context, 'testresourcelinkid');
        $resourcelink->save();
        $dc->loadResourceLink($resourcelink);

        // Assert that the resource link and the one fetched by get_resourcelink_from_context() are the same.
        $this->assertEquals($resourcelink, $dc->get_resourcelink_from_context($context));
    }
}
