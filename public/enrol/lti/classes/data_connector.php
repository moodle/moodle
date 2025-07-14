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
 * Extends the IMS Tool provider library data connector for moodle.
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_lti;

defined('MOODLE_INTERNAL') || die;

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\ConsumerNonce;
use IMSGlobal\LTI\ToolProvider\Context;
use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use IMSGlobal\LTI\ToolProvider\ResourceLink;
use IMSGlobal\LTI\ToolProvider\ResourceLinkShare;
use IMSGlobal\LTI\ToolProvider\ResourceLinkShareKey;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use IMSGlobal\LTI\ToolProvider\ToolProxy;
use IMSGlobal\LTI\ToolProvider\User;
use stdClass;

/**
 * Extends the IMS Tool provider library data connector for moodle.
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_connector extends DataConnector {

    /** @var string Tool consumer table name. */
    protected $consumertable;
    /** @var string Context table name. */
    protected $contexttable;
    /** @var string Consumer nonce table name. */
    protected $noncetable;
    /** @var string Resource link table name. */
    protected $resourcelinktable;
    /** @var string Resource link share key table name. */
    protected $sharekeytable;
    /** @var string Tool proxy table name. */
    protected $toolproxytable;
    /** @var string User result table name. */
    protected $userresulttable;

    /**
     * data_connector constructor.
     */
    public function __construct() {
        parent::__construct(null, 'enrol_lti_');

        // Set up table names.
        $this->consumertable = $this->dbTableNamePrefix . DataConnector::CONSUMER_TABLE_NAME;
        $this->contexttable = $this->dbTableNamePrefix . DataConnector::CONTEXT_TABLE_NAME;
        $this->noncetable = $this->dbTableNamePrefix . DataConnector::NONCE_TABLE_NAME;
        $this->resourcelinktable = $this->dbTableNamePrefix . DataConnector::RESOURCE_LINK_TABLE_NAME;
        $this->sharekeytable = $this->dbTableNamePrefix . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME;
        $this->toolproxytable = $this->dbTableNamePrefix . DataConnector::TOOL_PROXY_TABLE_NAME;
        $this->userresulttable = $this->dbTableNamePrefix . DataConnector::USER_RESULT_TABLE_NAME;
    }

    /**
     * Load tool consumer object.
     *
     * @param ToolConsumer $consumer ToolConsumer object
     * @return boolean True if the tool consumer object was successfully loaded
     */
    public function loadToolConsumer($consumer) {
        global $DB;

        $id = $consumer->getRecordId();
        $key = $consumer->getKey();
        $result = false;

        if (!empty($id)) {
            $result = $DB->get_record($this->consumertable, ['id' => $id]);
        } else if (!empty($key)) {
            $key256 = DataConnector::getConsumerKey($key);
            $result = $DB->get_record($this->consumertable, ['consumerkey256' => $key256]);
        }

        if ($result) {
            if (empty($key256) || empty($result->consumerkey) || ($key === $result->consumerkey)) {
                $this->build_tool_consumer_object($result, $consumer);
                return true;
            }
        }

        return false;
    }

    /**
     * Save tool consumer object.
     *
     * @param ToolConsumer $consumer Consumer object
     * @return boolean True if the tool consumer object was successfully saved
     */
    public function saveToolConsumer($consumer) {
        global $DB;

        $key = $consumer->getKey();
        $key256 = DataConnector::getConsumerKey($key);
        if ($key === $key256) {
            $key = null;
        }
        $protected = ($consumer->protected) ? 1 : 0;
        $enabled = ($consumer->enabled) ? 1 : 0;
        $profile = (!empty($consumer->profile)) ? json_encode($consumer->profile) : null;
        $settingsvalue = serialize($consumer->getSettings());
        $now = time();
        $consumer->updated = $now;
        $data = [
            'consumerkey256' => $key256,
            'consumerkey' => $key,
            'name' => $consumer->name,
            'secret' => $consumer->secret,
            'ltiversion' => $consumer->ltiVersion,
            'consumername' => $consumer->consumerName,
            'consumerversion' => $consumer->consumerVersion,
            'consumerguid' => $consumer->consumerGuid,
            'profile' => $profile,
            'toolproxy' => $consumer->toolProxy,
            'settings' => $settingsvalue,
            'protected' => $protected,
            'enabled' => $enabled,
            'enablefrom' => $consumer->enableFrom,
            'enableuntil' => $consumer->enableUntil,
            'lastaccess' => $consumer->lastAccess,
            'updated' => $consumer->updated,
        ];

        $id = $consumer->getRecordId();

        if (empty($id)) {
            $consumer->created = $now;
            $data['created'] = $consumer->created;
            $id = $DB->insert_record($this->consumertable, (object) $data);
            if ($id) {
                $consumer->setRecordId($id);
                return true;
            }
        } else {
            $data['id'] = $id;
            return $DB->update_record($this->consumertable, (object) $data);
        }

        return false;
    }

    /**
     * Delete tool consumer object and related records.
     *
     * @param ToolConsumer $consumer Consumer object
     * @return boolean True if the tool consumer object was successfully deleted
     */
    public function deleteToolConsumer($consumer) {
        global $DB;

        $consumerpk = $consumer->getRecordId();
        $deletecondition = ['consumerid' => $consumerpk];

        // Delete any nonce values for this consumer.
        $DB->delete_records($this->noncetable, $deletecondition);

        // Delete any outstanding share keys for resource links for this consumer.
        $where = "resourcelinkid IN (
                      SELECT rl.id
                        FROM {{$this->resourcelinktable}} rl
                       WHERE rl.consumerid = :consumerid
                  )";
        $DB->delete_records_select($this->sharekeytable, $where, $deletecondition);

        // Delete any outstanding share keys for resource links for contexts in this consumer.
        $where = "resourcelinkid IN (
                      SELECT rl.id
                        FROM {{$this->resourcelinktable}} rl
                  INNER JOIN {{$this->contexttable}} c
                          ON rl.contextid = c.id
                       WHERE c.consumerid = :consumerid
                )";
        $DB->delete_records_select($this->sharekeytable, $where, $deletecondition);

        // Delete any users in resource links for this consumer.
        $where = "resourcelinkid IN (
                    SELECT rl.id
                      FROM {{$this->resourcelinktable}} rl
                     WHERE rl.consumerid = :consumerid
                )";
        $DB->delete_records_select($this->userresulttable, $where, $deletecondition);

        // Delete any users in resource links for contexts in this consumer.
        $where = "resourcelinkid IN (
                         SELECT rl.id
                           FROM {{$this->resourcelinktable}} rl
                     INNER JOIN {{$this->contexttable}} c
                             ON rl.contextid = c.id
                          WHERE c.consumerid = :consumerid
                )";
        $DB->delete_records_select($this->userresulttable, $where, $deletecondition);

        // Update any resource links for which this consumer is acting as a primary resource link.
        $where = "primaryresourcelinkid IN (
                    SELECT rl.id
                      FROM {{$this->resourcelinktable}} rl
                     WHERE rl.consumerid = :consumerid
                )";
        $updaterecords = $DB->get_records_select($this->resourcelinktable, $where, $deletecondition);
        foreach ($updaterecords as $record) {
            $record->primaryresourcelinkid = null;
            $record->shareapproved = null;
            $DB->update_record($this->resourcelinktable, $record);
        }

        // Update any resource links for contexts in which this consumer is acting as a primary resource link.
        $where = "primaryresourcelinkid IN (
                        SELECT rl.id
                          FROM {{$this->resourcelinktable}} rl
                    INNER JOIN {{$this->contexttable}} c
                            ON rl.contextid = c.id
                         WHERE c.consumerid = :consumerid
                )";
        $updaterecords = $DB->get_records_select($this->resourcelinktable, $where, $deletecondition);
        foreach ($updaterecords as $record) {
            $record->primaryresourcelinkid = null;
            $record->shareapproved = null;
            $DB->update_record($this->resourcelinktable, $record);
        }

        // Delete any resource links for contexts in this consumer.
        $where = "contextid IN (
                      SELECT c.id
                        FROM {{$this->contexttable}} c
                       WHERE c.consumerid = :consumerid
                )";
        $DB->delete_records_select($this->resourcelinktable, $where, $deletecondition);

        // Delete any resource links for this consumer.
        $DB->delete_records($this->resourcelinktable, $deletecondition);

        // Delete any contexts for this consumer.
        $DB->delete_records($this->contexttable, $deletecondition);

        // Delete consumer.
        $DB->delete_records($this->consumertable, ['id' => $consumerpk]);

        $consumer->initialize();

        return true;
    }

    /**
     * Load all tool consumers from the database.
     * @return array
     */
    public function getToolConsumers() {
        global $DB;
        $consumers = [];

        $rsconsumers = $DB->get_recordset($this->consumertable, null, 'name');
        foreach ($rsconsumers as $row) {
            $consumer = new ToolProvider\ToolConsumer($row->consumerkey, $this);
            $this->build_tool_consumer_object($row, $consumer);
            $consumers[] = $consumer;
        }
        $rsconsumers->close();

        return $consumers;
    }

    /*
     * ToolProxy methods.
     */

    /**
     * Load the tool proxy from the database.
     *
     * @param ToolProxy $toolproxy
     * @return bool
     */
    public function loadToolProxy($toolproxy) {
        return false;
    }

    /**
     * Save the tool proxy to the database.
     *
     * @param ToolProxy $toolproxy
     * @return bool
     */
    public function saveToolProxy($toolproxy) {
        return false;
    }

    /**
     * Delete the tool proxy from the database.
     *
     * @param ToolProxy $toolproxy
     * @return bool
     */
    public function deleteToolProxy($toolproxy) {
        return false;
    }

    /*
     * Context methods.
     */

    /**
     * Load context object.
     *
     * @param Context $context Context object
     * @return boolean True if the context object was successfully loaded
     */
    public function loadContext($context) {
        global $DB;

        if (!empty($context->getRecordId())) {
            $params = ['id' => $context->getRecordId()];
        } else {
            $params = [
                'consumerid' => $context->getConsumer()->getRecordId(),
                'lticontextkey' => $context->ltiContextId
            ];
        }
        if ($row = $DB->get_record($this->contexttable, $params)) {
            $context->setRecordId($row->id);
            $context->setConsumerId($row->consumerid);
            $context->ltiContextId = $row->lticontextkey;
            $context->type = $row->type;
            $settings = unserialize($row->settings);
            if (!is_array($settings)) {
                $settings = array();
            }
            $context->setSettings($settings);
            $context->created = $row->created;
            $context->updated = $row->updated;
            return true;
        }

        return false;
    }

    /**
     * Save context object.
     *
     * @param Context $context Context object
     * @return boolean True if the context object was successfully saved
     */
    public function saveContext($context) {
        global $DB;
        $now = time();
        $context->updated = $now;
        $settingsvalue = serialize($context->getSettings());
        $id = $context->getRecordId();
        $consumerpk = $context->getConsumer()->getRecordId();

        $isinsert = empty($id);
        if ($isinsert) {
            $context->created = $now;
            $params = [
                'consumerid' => $consumerpk,
                'lticontextkey' => $context->ltiContextId,
                'type' => $context->type,
                'settings' => $settingsvalue,
                'created' => $context->created,
                'updated' => $context->updated,
            ];
            $id = $DB->insert_record($this->contexttable, (object) $params);
            if ($id) {
                $context->setRecordId($id);
                return true;
            }
        } else {
            $data = (object) [
                'id' => $id,
                'contextid' => $consumerpk,
                'lticontextkey' => $context->ltiContextId,
                'type' => $context->type,
                'settings' => $settingsvalue,
                'updated' => $context->updated,
            ];
            return $DB->update_record($this->contexttable, $data);
        }

        return false;
    }

    /**
     * Delete context object.
     *
     * @param Context $context Context object
     * @return boolean True if the Context object was successfully deleted
     */
    public function deleteContext($context) {
        global $DB;

        $contextid = $context->getRecordId();

        $params = ['id' => $contextid];

        // Delete any outstanding share keys for resource links for this context.
        $where = "resourcelinkid IN (
                    SELECT rl.id
                      FROM {{$this->resourcelinktable}} rl
                     WHERE rl.contextid = :id
               )";
        $DB->delete_records_select($this->sharekeytable, $where, $params);

        // Delete any users in resource links for this context.
        $DB->delete_records_select($this->userresulttable, $where, $params);

        // Update any resource links for which this consumer is acting as a primary resource link.
        $where = "primaryresourcelinkid IN (
                    SELECT rl.id
                      FROM {{$this->resourcelinktable}} rl
                     WHERE rl.contextid = :id
               )";
        $updaterecords = $DB->get_records_select($this->resourcelinktable, $where, $params);
        foreach ($updaterecords as $record) {
            $record->primaryresourcelinkid = null;
            $record->shareapproved = null;
            $DB->update_record($this->resourcelinktable, $record);
        }

        // Delete any resource links for this context.
        $DB->delete_records($this->resourcelinktable, ['contextid' => $contextid]);

        // Delete context.
        $DB->delete_records($this->contexttable, $params);

        $context->initialize();

        return true;
    }

    /*
     * ResourceLink methods
     */

    /**
     * Load resource link object.
     *
     * @param ResourceLink $resourcelink ResourceLink object
     * @return boolean True if the resource link object was successfully loaded
     */
    public function loadResourceLink($resourcelink) {
        global $DB;

        $resourceid = $resourcelink->getRecordId();
        if (!empty($resourceid)) {
            $params = ['id' => $resourceid];
            $row = $DB->get_record($this->resourcelinktable, $params);
        } else if (!empty($resourcelink->getContext())) {
            $params = [
                'contextid' => $resourcelink->getContext()->getRecordId(),
                'ltiresourcelinkkey' => $resourcelink->getId()
            ];
            $row = $DB->get_record($this->resourcelinktable, $params);
        } else {
            $sql = "SELECT r.*
                      FROM {{$this->resourcelinktable}} r
           LEFT OUTER JOIN {{$this->contexttable}} c
                        ON r.contextid = c.id
                     WHERE (r.consumerid = ? OR c.consumerid = ?)
                           AND ltiresourcelinkkey = ?";
            $params = [
                $resourcelink->getConsumer()->getRecordId(),
                $resourcelink->getConsumer()->getRecordId(),
                $resourcelink->getId()
            ];
            $row = $DB->get_record_sql($sql, $params);
        }
        if ($row) {
            $resourcelink->setRecordId($row->id);
            if (!is_null($row->contextid)) {
                $resourcelink->setContextId($row->contextid);
            } else {
                $resourcelink->setContextId(null);
            }
            if (!is_null($row->consumerid)) {
                $resourcelink->setConsumerId($row->consumerid);
            } else {
                $resourcelink->setConsumerId(null);
            }
            $resourcelink->ltiResourceLinkId = $row->ltiresourcelinkkey;
            $settings = unserialize($row->settings);
            if (!is_array($settings)) {
                $settings = array();
            }
            $resourcelink->setSettings($settings);
            if (!is_null($row->primaryresourcelinkid)) {
                $resourcelink->primaryResourceLinkId = $row->primaryresourcelinkid;
            } else {
                $resourcelink->primaryResourceLinkId = null;
            }
            $resourcelink->shareApproved = (is_null($row->shareapproved)) ? null : ($row->shareapproved == 1);
            $resourcelink->created = $row->created;
            $resourcelink->updated = $row->updated;
            return true;
        }

        return false;
    }

    /**
     * Save resource link object.
     *
     * @param ResourceLink $resourcelink Resource_Link object
     * @return boolean True if the resource link object was successfully saved
     */
    public function saveResourceLink($resourcelink) {
        global $DB;

        if (is_null($resourcelink->shareApproved)) {
            $approved = null;
        } else if ($resourcelink->shareApproved) {
            $approved = 1;
        } else {
            $approved = 0;
        }
        if (empty($resourcelink->primaryResourceLinkId)) {
            $primaryresourcelinkid = null;
        } else {
            $primaryresourcelinkid = $resourcelink->primaryResourceLinkId;
        }
        $now = time();
        $resourcelink->updated = $now;
        $settingsvalue = serialize($resourcelink->getSettings());
        if (!empty($resourcelink->getContext())) {
            $consumerid = null;
            $contextid = $resourcelink->getContext()->getRecordId();
        } else if (!empty($resourcelink->getContextId())) {
            $consumerid = null;
            $contextid = $resourcelink->getContextId();
        } else {
            $consumerid = $resourcelink->getConsumer()->getRecordId();
            $contextid = null;
        }
        $id = $resourcelink->getRecordId();

        $data = [
            'consumerid' => $consumerid,
            'contextid' => $contextid,
            'ltiresourcelinkkey' => $resourcelink->getId(),
            'settings' => $settingsvalue,
            'primaryresourcelinkid' => $primaryresourcelinkid,
            'shareapproved' => $approved,
            'updated' => $resourcelink->updated,
        ];

        $returnid = null;

        if (empty($id)) {
            $resourcelink->created = $now;
            $data['created'] = $resourcelink->created;
            $id = $DB->insert_record($this->resourcelinktable, (object) $data);
            if ($id) {
                $resourcelink->setRecordId($id);
                return true;
            }

        } else {
            $data['id'] = $id;
            return $DB->update_record($this->resourcelinktable, (object) $data);
        }

        return false;
    }

    /**
     * Delete resource link object.
     *
     * @param ResourceLink $resourcelink ResourceLink object
     * @return boolean True if the resource link object and its related records were successfully deleted.
     *                 Otherwise, a DML exception is thrown.
     */
    public function deleteResourceLink($resourcelink) {
        global $DB;

        $resourcelinkid = $resourcelink->getRecordId();

        // Delete any outstanding share keys for resource links for this consumer.
        $DB->delete_records($this->sharekeytable, ['resourcelinkid' => $resourcelinkid]);

        // Delete users.
        $DB->delete_records($this->userresulttable, ['resourcelinkid' => $resourcelinkid]);

        // Update any resource links for which this is the primary resource link.
        $records = $DB->get_records($this->resourcelinktable, ['primaryresourcelinkid' => $resourcelinkid]);
        foreach ($records as $record) {
            $record->primaryresourcelinkid = null;
            $DB->update_record($this->resourcelinktable, $record);
        }

        // Delete resource link.
        $DB->delete_records($this->resourcelinktable, ['id' => $resourcelinkid]);

        $resourcelink->initialize();

        return true;
    }

    /**
     * Get array of user objects.
     *
     * Obtain an array of User objects for users with a result sourcedId.  The array may include users from other
     * resource links which are sharing this resource link.  It may also be optionally indexed by the user ID of a specified scope.
     *
     * @param ResourceLink $resourcelink Resource link object
     * @param boolean $localonly True if only users within the resource link are to be returned
     *                           (excluding users sharing this resource link)
     * @param int $idscope Scope value to use for user IDs
     * @return array Array of User objects
     */
    public function getUserResultSourcedIDsResourceLink($resourcelink, $localonly, $idscope) {
        global $DB;

        $users = [];

        $params = ['resourcelinkid' => $resourcelink->getRecordId()];

        // Where clause for the subquery.
        $subwhere = "(id = :resourcelinkid AND primaryresourcelinkid IS NULL)";
        if (!$localonly) {
            $subwhere .= " OR (primaryresourcelinkid = :resourcelinkid2 AND shareapproved = 1)";
            $params['resourcelinkid2'] = $resourcelink->getRecordId();
        }

        // The subquery.
        $subsql = "SELECT id
                     FROM {{$this->resourcelinktable}}
                    WHERE {$subwhere}";

        // Our main where clause.
        $where = "resourcelinkid IN ($subsql)";

        // Fields to be queried.
        $fields = 'id, ltiresultsourcedid, ltiuserkey, created, updated';

        // Fetch records.
        $rs = $DB->get_recordset_select($this->userresulttable, $where, $params, '', $fields);
        foreach ($rs as $row) {
            $user = User::fromResourceLink($resourcelink, $row->ltiuserkey);
            $user->setRecordId($row->id);
            $user->ltiResultSourcedId = $row->ltiresultsourcedid;
            $user->created = $row->created;
            $user->updated = $row->updated;
            if (is_null($idscope)) {
                $users[] = $user;
            } else {
                $users[$user->getId($idscope)] = $user;
            }
        }
        $rs->close();

        return $users;
    }

    /**
     * Get array of shares defined for this resource link.
     *
     * @param ResourceLink $resourcelink ResourceLink object
     * @return array Array of ResourceLinkShare objects
     */
    public function getSharesResourceLink($resourcelink) {
        global $DB;

        $shares = [];

        $params = ['primaryresourcelinkid' => $resourcelink->getRecordId()];
        $fields = 'id, shareapproved, consumerid';
        $records = $DB->get_records($this->resourcelinktable, $params, 'consumerid', $fields);
        foreach ($records as $record) {
            $share = new ResourceLinkShare();
            $share->resourceLinkId = $record->id;
            $share->approved = $record->shareapproved == 1;
            $shares[] = $share;
        }

        return $shares;
    }

    /*
     * ConsumerNonce methods
     */

    /**
     * Load nonce object.
     *
     * @param ConsumerNonce $nonce Nonce object
     * @return boolean True if the nonce object was successfully loaded
     */
    public function loadConsumerNonce($nonce) {
        global $DB;

        // Delete any expired nonce values.
        $now = time();
        $DB->delete_records_select($this->noncetable, "expires <= ?", [$now]);

        // Load the nonce.
        $params = [
            'consumerid' => $nonce->getConsumer()->getRecordId(),
            'value' => $nonce->getValue()
        ];
        $result = $DB->get_field($this->noncetable, 'value', $params);

        return !empty($result);
    }

    /**
     * Save nonce object.
     *
     * @param ConsumerNonce $nonce Nonce object
     * @return boolean True if the nonce object was successfully saved
     */
    public function saveConsumerNonce($nonce) {
        global $DB;

        $data = [
            'consumerid' => $nonce->getConsumer()->getRecordId(),
            'value' => $nonce->getValue(),
            'expires' => $nonce->expires
        ];

        return $DB->insert_record($this->noncetable, (object) $data, false);
    }

    /*
     * ResourceLinkShareKey methods.
     */

    /**
     * Load resource link share key object.
     *
     * @param ResourceLinkShareKey $sharekey ResourceLink share key object
     * @return boolean True if the resource link share key object was successfully loaded
     */
    public function loadResourceLinkShareKey($sharekey) {
        global $DB;

        // Clear expired share keys.
        $now = time();
        $where = "expires <= :expires";

        $DB->delete_records_select($this->sharekeytable, $where, ['expires' => $now]);

        // Load share key.
        $fields = 'resourcelinkid, autoapprove, expires';
        if ($sharekeyrecord = $DB->get_record($this->sharekeytable, ['sharekey' => $sharekey->getId()], $fields)) {
            if ($sharekeyrecord->resourcelinkid == $sharekey->resourceLinkId) {
                $sharekey->autoApprove = $sharekeyrecord->autoapprove == 1;
                $sharekey->expires = $sharekeyrecord->expires;
                return true;
            }
        }

        return false;
    }

    /**
     * Save resource link share key object.
     *
     * @param ResourceLinkShareKey $sharekey Resource link share key object
     * @return boolean True if the resource link share key object was successfully saved
     */
    public function saveResourceLinkShareKey($sharekey) {
        global $DB;

        if ($sharekey->autoApprove) {
            $approve = 1;
        } else {
            $approve = 0;
        }

        $expires = $sharekey->expires;

        $params = [
            'sharekey' => $sharekey->getId(),
            'resourcelinkid' => $sharekey->resourceLinkId,
            'autoapprove' => $approve,
            'expires' => $expires
        ];

        return $DB->insert_record($this->sharekeytable, (object) $params, false);
    }

    /**
     * Delete resource link share key object.
     *
     * @param ResourceLinkShareKey $sharekey Resource link share key object
     * @return boolean True if the resource link share key object was successfully deleted
     */
    public function deleteResourceLinkShareKey($sharekey) {
        global $DB;

        $DB->delete_records($this->sharekeytable, ['sharekey' => $sharekey->getId()]);
        $sharekey->initialize();

        return true;
    }

    /*
     * User methods
     */

    /**
     * Load user object.
     *
     * @param User $user User object
     * @return boolean True if the user object was successfully loaded
     */
    public function loadUser($user) {
        global $DB;

        $userid = $user->getRecordId();
        $fields = 'id, resourcelinkid, ltiuserkey, ltiresultsourcedid, created, updated';
        if (!empty($userid)) {
            $row = $DB->get_record($this->userresulttable, ['id' => $userid], $fields);
        } else {
            $resourcelinkid = $user->getResourceLink()->getRecordId();
            $userid = $user->getId(ToolProvider\ToolProvider::ID_SCOPE_ID_ONLY);
            $row = $DB->get_record_select(
                $this->userresulttable,
                "resourcelinkid = ? AND ltiuserkey = ?",
                [$resourcelinkid, $userid],
                $fields
            );
        }
        if ($row) {
            $user->setRecordId($row->id);
            $user->setResourceLinkId($row->resourcelinkid);
            $user->ltiUserId = $row->ltiuserkey;
            $user->ltiResultSourcedId = $row->ltiresultsourcedid;
            $user->created = $row->created;
            $user->updated = $row->updated;
            return true;
        }

        return false;
    }

    /**
     * Save user object.
     *
     * @param User $user User object
     * @return boolean True if the user object was successfully saved
     */
    public function saveUser($user) {
        global $DB;

        $now = time();
        $isinsert = is_null($user->created);
        $user->updated = $now;

        $params = [
            'ltiresultsourcedid' => $user->ltiResultSourcedId,
            'updated' => $user->updated
        ];

        if ($isinsert) {
            $params['resourcelinkid'] = $user->getResourceLink()->getRecordId();
            $params['ltiuserkey'] = $user->getId(ToolProvider\ToolProvider::ID_SCOPE_ID_ONLY);
            $user->created = $now;
            $params['created'] = $user->created;
            $id = $DB->insert_record($this->userresulttable, (object) $params);
            if ($id) {
                $user->setRecordId($id);
                return true;
            }

        } else {
            $params['id'] = $user->getRecordId();
            return $DB->update_record($this->userresulttable, (object) $params);
        }

        return false;
    }

    /**
     * Delete user object.
     *
     * @param User $user User object
     * @return boolean True if the user object was successfully deleted
     */
    public function deleteUser($user) {
        global $DB;

        $DB->delete_records($this->userresulttable, ['id' => $user->getRecordId()]);
        $user->initialize();

        return true;
    }

    /**
     * Fetches the list of Context objects that are linked to a ToolConsumer.
     *
     * @param ToolConsumer $consumer
     * @return Context[]
     */
    public function get_contexts_from_consumer(ToolConsumer $consumer) {
        global $DB;

        $contexts = [];
        $contextrecords = $DB->get_records($this->contexttable, ['consumerid' => $consumer->getRecordId()], '', 'lticontextkey');
        foreach ($contextrecords as $record) {
            $context = Context::fromConsumer($consumer, $record->lticontextkey);
            $contexts[] = $context;
        }

        return $contexts;
    }

    /**
     * Fetches a resource link record that is associated with a ToolConsumer.
     *
     * @param ToolConsumer $consumer
     * @return ResourceLink
     */
    public function get_resourcelink_from_consumer(ToolConsumer $consumer) {
        global $DB;

        $resourcelink = null;
        if ($resourcelinkrecord = $DB->get_record($this->resourcelinktable, ['consumerid' => $consumer->getRecordId()],
            'ltiresourcelinkkey')) {
            $resourcelink = ResourceLink::fromConsumer($consumer, $resourcelinkrecord->ltiresourcelinkkey);
        }

        return $resourcelink;
    }

    /**
     * Fetches a resource link record that is associated with a Context object.
     *
     * @param Context $context
     * @return ResourceLink
     */
    public function get_resourcelink_from_context(Context $context) {
        global $DB;

        $resourcelink = null;
        if ($resourcelinkrecord = $DB->get_record($this->resourcelinktable, ['contextid' => $context->getRecordId()],
            'ltiresourcelinkkey')) {
            $resourcelink = ResourceLink::fromContext($context, $resourcelinkrecord->ltiresourcelinkkey);
        }

        return $resourcelink;
    }


    /**
     * Fetches the list of ToolConsumer objects that are linked to a tool.
     *
     * @param int $toolid
     * @return ToolConsumer[]
     */
    public function get_consumers_mapped_to_tool($toolid) {
        global $DB;

        $consumers = [];
        $consumerrecords = $DB->get_records('enrol_lti_tool_consumer_map', ['toolid' => $toolid], '', 'consumerid');
        foreach ($consumerrecords as $record) {
            $consumers[] = ToolConsumer::fromRecordId($record->consumerid, $this);
        }
        return $consumers;
    }

    /**
     * Builds a ToolConsumer object from a record object from the DB.
     *
     * @param stdClass $record The DB record object.
     * @param ToolConsumer $consumer
     */
    protected function build_tool_consumer_object($record, ToolConsumer $consumer) {
        $consumer->setRecordId($record->id);
        $consumer->name = $record->name;
        $key = empty($record->consumerkey) ? $record->consumerkey256 : $record->consumerkey;
        $consumer->setKey($key);
        $consumer->secret = $record->secret;
        $consumer->ltiVersion = $record->ltiversion;
        $consumer->consumerName = $record->consumername;
        $consumer->consumerVersion = $record->consumerversion;
        $consumer->consumerGuid = $record->consumerguid;
        $consumer->profile = json_decode($record->profile ?? '');
        $consumer->toolProxy = $record->toolproxy;
        $settings = unserialize($record->settings);
        if (!is_array($settings)) {
            $settings = array();
        }
        $consumer->setSettings($settings);
        $consumer->protected = $record->protected == 1;
        $consumer->enabled = $record->enabled == 1;
        $consumer->enableFrom = null;
        if (!is_null($record->enablefrom)) {
            $consumer->enableFrom = $record->enablefrom;
        }
        $consumer->enableUntil = null;
        if (!is_null($record->enableuntil)) {
            $consumer->enableUntil = $record->enableuntil;
        }
        $consumer->lastAccess = null;
        if (!is_null($record->lastaccess)) {
            $consumer->lastAccess = $record->lastaccess;
        }
        $consumer->created = $record->created;
        $consumer->updated = $record->updated;
    }
}
