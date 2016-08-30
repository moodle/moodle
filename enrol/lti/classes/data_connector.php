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

        if (!empty($id)) {
            $result = $DB->get_record($this->consumertable, ['id' => $id]);
        } else {
            $key256 = DataConnector::getConsumerKey($consumer->getKey());
            $result = $DB->get_record($this->consumertable, ['consumer_key256' => $key256]);
        }

        if ($result) {
            if (empty($key256) || empty($result->consumer_key) || ($consumer->getKey() === $result->consumer_key)) {
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
            'consumer_key256' => $key256,
            'consumer_key' => $key,
            'name' => $consumer->name,
            'secret' => $consumer->secret,
            'lti_version' => $consumer->ltiVersion,
            'consumer_name' => $consumer->consumerName,
            'consumer_version' => $consumer->consumerVersion,
            'consumer_guid' => $consumer->consumerGuid,
            'profile' => $profile,
            'tool_proxy' => $consumer->toolProxy,
            'settings' => $settingsvalue,
            'protected' => $protected,
            'enabled' => $enabled,
            'enable_from' => $consumer->enableFrom,
            'enable_until' => $consumer->enableUntil,
            'last_access' => $consumer->lastAccess,
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
        $deletecondition = ['consumer_pk' => $consumerpk];

        // Delete any nonce values for this consumer.
        $DB->delete_records($this->noncetable, $deletecondition);

        // Delete any outstanding share keys for resource links for this consumer.
        $where = "resource_link_pk IN (
                      SELECT rl.id
                        FROM {{$this->resourcelinktable}} rl
                       WHERE rl.consumer_pk = :consumer_pk
                  )";
        $DB->delete_records_select($this->sharekeytable, $where, $deletecondition);

        // Delete any outstanding share keys for resource links for contexts in this consumer.
        $where = "resource_link_pk IN (
                      SELECT rl.id
                        FROM {{$this->resourcelinktable}} rl
                  INNER JOIN {{$this->contexttable}} c
                          ON rl.context_pk = c.id
                       WHERE c.consumer_pk = :consumer_pk
                )";
        $DB->delete_records_select($this->sharekeytable, $where, $deletecondition);

        // Delete any users in resource links for this consumer.
        $where = "resource_link_pk IN (
                    SELECT rl.id
                      FROM {{$this->resourcelinktable}} rl
                     WHERE rl.consumer_pk = :consumer_pk
                )";
        $DB->delete_records_select($this->userresulttable, $where, $deletecondition);

        // Delete any users in resource links for contexts in this consumer.
        $where = "resource_link_pk IN (
                         SELECT rl.id
                           FROM {{$this->resourcelinktable}} rl
                     INNER JOIN {{$this->contexttable}} c
                             ON rl.context_pk = c.id
                          WHERE c.consumer_pk = :consumer_pk
                )";
        $DB->delete_records_select($this->userresulttable, $where, $deletecondition);

        // Update any resource links for which this consumer is acting as a primary resource link.
        $where = "primary_resource_link_pk IN (
                    SELECT rl.id
                      FROM {{$this->resourcelinktable}} rl
                     WHERE rl.consumer_pk = :consumer_pk
                )";
        $updaterecords = $DB->get_records_select($this->resourcelinktable, $where, $deletecondition);
        foreach ($updaterecords as $record) {
            $record->primary_resource_link_pk = null;
            $record->share_approved = null;
            $DB->update_record($this->resourcelinktable, $record);
        }

        // Update any resource links for contexts in which this consumer is acting as a primary resource link.
        $where = "primary_resource_link_pk IN (
                        SELECT rl.id
                          FROM {{$this->resourcelinktable}} rl
                    INNER JOIN {{$this->contexttable}} c
                            ON rl.context_pk = c.id
                         WHERE c.consumer_pk = :consumer_pk
                )";
        $updaterecords = $DB->get_records_select($this->resourcelinktable, $where, $deletecondition);
        foreach ($updaterecords as $record) {
            $record->primary_resource_link_pk = null;
            $record->share_approved = null;
            $DB->update_record($this->resourcelinktable, $record);
        }

        // Delete any resource links for contexts in this consumer.
        $where = "context_pk IN (
                      SELECT c.id
                        FROM {{$this->contexttable}} c
                       WHERE c.consumer_pk = :consumer_pk
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
            $consumer = new ToolProvider\ToolConsumer($row->consumer_key, $this);
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
                'consumer_pk' => $context->getConsumer()->getRecordId(),
                'lti_context_id' => $context->ltiContextId
            ];
        }
        if ($row = $DB->get_record($this->contexttable, $params)) {
            $context->setRecordId($row->id);
            $context->setConsumerId($row->consumer_pk);
            $context->ltiContextId = $row->lti_context_id;
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
                'consumer_pk' => $consumerpk,
                'lti_context_id' => $context->ltiContextId,
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
                'context_pk' => $consumerpk,
                'lti_context_id' => $context->ltiContextId,
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
        $where = "resource_link_pk IN (
                    SELECT rl.id
                      FROM {{$this->resourcelinktable}} rl
                     WHERE rl.context_pk = :id
               )";
        $DB->delete_records_select($this->sharekeytable, $where, $params);

        // Delete any users in resource links for this context.
        $DB->delete_records_select($this->userresulttable, $where, $params);

        // Update any resource links for which this consumer is acting as a primary resource link.
        $where = "primary_resource_link_pk IN (
                    SELECT rl.id
                      FROM {{$this->resourcelinktable}} rl
                     WHERE rl.context_pk = :id
               )";
        $updaterecords = $DB->get_records_select($this->resourcelinktable, $where, $params);
        foreach ($updaterecords as $record) {
            $record->primary_resource_link_pk = null;
            $record->share_approved = null;
            $DB->update_record($this->resourcelinktable, $record);
        }

        // Delete any resource links for this context.
        $DB->delete_records($this->resourcelinktable, ['context_pk' => $contextid]);

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
     * @param ResourceLink $resourcelink Resource_Link object
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
                'context_pk' => $resourcelink->getContext()->getRecordId(),
                'lti_resource_link_id' => $resourcelink->getId()
            ];
            $row = $DB->get_record($this->resourcelinktable, $params);
        } else {
            $sql = "SELECT r.*
                      FROM {{$this->resourcelinktable}} r
           LEFT OUTER JOIN {{$this->contexttable}} c
                        ON r.context_pk = c.id
                     WHERE (r.consumer_pk = ? OR c.consumer_pk = ?)
                           AND lti_resource_link_id = ?";
            $params = [
                $resourcelink->getConsumer()->getRecordId(),
                $resourcelink->getConsumer()->getRecordId(),
                $resourcelink->getId()
            ];
            $row = $DB->get_record_sql($sql, $params);
        }
        if ($row) {
            $resourcelink->setRecordId($row->id);
            if (!is_null($row->context_pk)) {
                $resourcelink->setContextId($row->context_pk);
            } else {
                $resourcelink->setContextId(null);
            }
            if (!is_null($row->consumer_pk)) {
                $resourcelink->setConsumerId($row->consumer_pk);
            } else {
                $resourcelink->setConsumerId(null);
            }
            $resourcelink->ltiResourceLinkId = $row->lti_resource_link_id;
            $settings = unserialize($row->settings);
            if (!is_array($settings)) {
                $settings = array();
            }
            $resourcelink->setSettings($settings);
            if (!is_null($row->primary_resource_link_pk)) {
                $resourcelink->primaryResourceLinkId = $row->primary_resource_link_pk;
            } else {
                $resourcelink->primaryResourceLinkId = null;
            }
            $resourcelink->shareApproved = (is_null($row->share_approved)) ? null : ($row->share_approved == 1);
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
            'consumer_pk' => $consumerid,
            'context_pk' => $contextid,
            'lti_resource_link_id' => $resourcelink->getId(),
            'settings' => $settingsvalue,
            'primary_resource_link_pk' => $primaryresourcelinkid,
            'share_approved' => $approved,
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
     * @param ResourceLink $resourcelink Resource_Link object
     * @return boolean True if the resource link object and its related records were successfully deleted.
     *                 Otherwise, a DML exception is thrown.
     */
    public function deleteResourceLink($resourcelink) {
        global $DB;

        $resourcelinkid = $resourcelink->getRecordId();

        // Delete any outstanding share keys for resource links for this consumer.
        $DB->delete_records($this->sharekeytable, ['resource_link_pk' => $resourcelinkid]);

        // Delete users.
        $DB->delete_records($this->userresulttable, ['resource_link_pk' => $resourcelinkid]);

        // Update any resource links for which this is the primary resource link.
        $records = $DB->get_records($this->resourcelinktable, ['primary_resource_link_pk' => $resourcelinkid]);
        foreach ($records as $record) {
            $record->primary_resource_link_pk = null;
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

        $params = ['resource_link_pk' => $resourcelink->getRecordId()];

        // Where clause for the subquery.
        $subwhere = "(id = :resource_link_pk AND primary_resource_link_pk IS NULL)";
        if (!$localonly) {
            $subwhere .= " OR (primary_resource_link_pk = :resource_link_pk2 AND share_approved = 1)";
            $params['resource_link_pk2'] = $resourcelink->getRecordId();
        }

        // The subquery.
        $subsql = "SELECT id
                     FROM {{$this->resourcelinktable}}
                    WHERE {$subwhere}";

        // Our main where clause.
        $where = "resource_link_pk IN ($subsql)";

        // Fields to be queried.
        $fields = 'id, lti_result_sourcedid, lti_user_id, created, updated';

        // Fetch records.
        $rs = $DB->get_recordset_select($this->userresulttable, $where, $params, '', $fields);
        foreach ($rs as $row) {
            $user = User::fromResourceLink($resourcelink, $row->lti_user_id);
            $user->setRecordId($row->id);
            $user->ltiResultSourcedId = $row->lti_result_sourcedid;
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
     * @param ResourceLink $resourcelink Resource_Link object
     * @return array Array of ResourceLinkShare objects
     */
    public function getSharesResourceLink($resourcelink) {
        global $DB;

        $shares = [];

        $params = ['primary_resource_link_pk' => $resourcelink->getRecordId()];
        $fields = 'id, share_approved, consumer_pk';
        $records = $DB->get_records($this->resourcelinktable, $params, 'consumer_pk', $fields);
        foreach ($records as $record) {
            $share = new ResourceLinkShare();
            $share->resourceLinkId = $record->id;
            $share->approved = $record->share_approved == 1;
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
            'consumer_pk' => $nonce->getConsumer()->getRecordId(),
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
            'consumer_pk' => $nonce->getConsumer()->getRecordId(),
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
     * @param ResourceLinkShareKey $sharekey Resource_Link share key object
     * @return boolean True if the resource link share key object was successfully loaded
     */
    public function loadResourceLinkShareKey($sharekey) {
        global $DB;

        // Clear expired share keys.
        $now = time();
        $where = "expires <= :expires";

        $DB->delete_records_select($this->sharekeytable, $where, ['expires' => $now]);

        // Load share key.
        $fields = 'resource_link_pk, auto_approve, expires';
        if ($sharekeyrecord = $DB->get_record($this->sharekeytable, ['share_key_id' => $sharekey->getId()], $fields)) {
            if ($sharekeyrecord->resource_link_pk == $sharekey->resourceLinkId) {
                $sharekey->autoApprove = $sharekeyrecord->auto_approve == 1;
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
            'share_key_id' => $sharekey->getId(),
            'resource_link_pk' => $sharekey->resourceLinkId,
            'auto_approve' => $approve,
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

        $DB->delete_records($this->sharekeytable, ['share_key_id' => $sharekey->getId()]);
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
        $fields = 'id, resource_link_pk, lti_user_id, lti_result_sourcedid, created, updated';
        if (!empty($userid)) {
            $row = $DB->get_record($this->userresulttable, ['id' => $userid], $fields);
        } else {
            $resourcelinkid = $user->getResourceLink()->getRecordId();
            $userid = $user->getId(ToolProvider\ToolProvider::ID_SCOPE_ID_ONLY);
            $row = $DB->get_record_select(
                $this->userresulttable,
                "resource_link_pk = ? AND lti_user_id = ?",
                [$resourcelinkid, $userid],
                $fields
            );
        }
        if ($row) {
            $user->setRecordId($row->id);
            $user->setResourceLinkId($row->resource_link_pk);
            $user->ltiUserId = $row->lti_user_id;
            $user->ltiResultSourcedId = $row->lti_result_sourcedid;
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
            'lti_result_sourcedid' => $user->ltiResultSourcedId,
            'updated' => $user->updated
        ];

        if ($isinsert) {
            $params['resource_link_pk'] = $user->getResourceLink()->getRecordId();
            $params['lti_user_id'] = $user->getId(ToolProvider\ToolProvider::ID_SCOPE_ID_ONLY);
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
     * Builds a ToolConsumer object from a record object from the DB.
     *
     * @param stdClass $record The DB record object.
     * @param ToolConsumer $consumer
     */
    protected function build_tool_consumer_object($record, ToolConsumer $consumer) {
        $consumer->setRecordId($record->id);
        $consumer->name = $record->name;
        $key = empty($record->consumer_key) ? $record->consumer_key256 : $record->consumer_key;
        $consumer->setKey($key);
        $consumer->secret = $record->secret;
        $consumer->ltiVersion = $record->lti_version;
        $consumer->consumerName = $record->consumer_name;
        $consumer->consumerVersion = $record->consumer_version;
        $consumer->consumerGuid = $record->consumer_guid;
        $consumer->profile = json_decode($record->profile);
        $consumer->toolProxy = $record->tool_proxy;
        $settings = unserialize($record->settings);
        if (!is_array($settings)) {
            $settings = array();
        }
        $consumer->setSettings($settings);
        $consumer->protected = $record->protected == 1;
        $consumer->enabled = $record->enabled == 1;
        $consumer->enableFrom = null;
        if (!is_null($record->enable_from)) {
            $consumer->enableFrom = $record->enable_from;
        }
        $consumer->enableUntil = null;
        if (!is_null($record->enable_until)) {
            $consumer->enableUntil = $record->enable_until;
        }
        $consumer->lastAccess = null;
        if (!is_null($record->last_access)) {
            $consumer->lastAccess = $record->last_access;
        }
        $consumer->created = $record->created;
        $consumer->updated = $record->updated;
    }
}
