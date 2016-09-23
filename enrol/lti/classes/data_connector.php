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
use IMSGlobal\LTI\ToolProvider\Context;
use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use IMSGlobal\LTI\ToolProvider\User;

/**
 * Extends the IMS Tool provider library data connector for moodle.
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_connector extends DataConnector {

    function __construct() {
        global $CFG;
        parent::__construct(null, 'enrol_lti_');
    }

    /**
     * Load tool consumer object.
     *
     * @param ToolConsumer $consumer ToolConsumer object
     *
     * @return boolean True if the tool consumer object was successfully loaded
     */
    public function loadToolConsumer($consumer) {
        global $DB;

        $table = $this->dbTableNamePrefix . DataConnector::CONSUMER_TABLE_NAME;

        $ok = false;
        $fields = 'id, name, consumer_key256, consumer_key, secret, lti_version, ' .
                  'consumer_name, consumer_version, consumer_guid, ' .
                  'profile, tool_proxy, settings, protected, enabled, ' .
                  'enable_from, enable_until, last_access, created, updated';
        $id = $consumer->getRecordId();

        $result = [];
        if (!empty($id)) {
            $result = $DB->get_records($table, ['id' => $id], '', $fields);
        } else {
            $key256 = DataConnector::getConsumerKey($consumer->getKey());
            $result = $DB->get_records($table, ['consumer_key256' => $key256], '', $fields);
        }
        // TODO Catch exceptions.
        foreach ($result as $row) {
            if (empty($key256) || empty($row->consumer_key) || ($consumer->getKey() === $row->consumer_key)) {
                $consumer->setRecordId(intval($row->id));
                $consumer->name = $row->name;
                $consumer->setkey(empty($row->consumer_key) ? $row->consumer_key256 : $row->consumer_key);
                $consumer->secret = $row->secret;
                $consumer->ltiVersion = $row->lti_version;
                $consumer->consumerName = $row->consumer_name;
                $consumer->consumerVersion = $row->consumer_version;
                $consumer->consumerGuid = $row->consumer_guid;
                $consumer->profile = json_decode($row->profile);
                $consumer->toolProxy = $row->tool_proxy;
                $settings = unserialize($row->settings);
                if (!is_array($settings)) {
                    $settings = array();
                }
                $consumer->setSettings($settings);
                $consumer->protected = (intval($row->protected) === 1);
                $consumer->enabled = (intval($row->enabled) === 1);
                $consumer->enableFrom = null;
                if (!is_null($row->enable_from)) {
                    $consumer->enableFrom = strtotime($row->enable_from);
                }
                $consumer->enableUntil = null;
                if (!is_null($row->enable_until)) {
                    $consumer->enableUntil = strtotime($row->enable_until);
                }
                $consumer->lastAccess = null;
                if (!is_null($row->last_access)) {
                    $consumer->lastAccess = strtotime($row->last_access);
                }
                $consumer->created = strtotime($row->created);
                $consumer->updated = strtotime($row->updated);
                $ok = true;
                break;
            }
        }

        return $ok;

    }

    /**
     * Save tool consumer object.
     *
     * @param ToolConsumer $consumer Consumer object
     *
     * @return boolean True if the tool consumer object was successfully saved
     */
    public function saveToolConsumer($consumer) {
        global $DB;

        $table = $this->dbTableNamePrefix . DataConnector::CONSUMER_TABLE_NAME;

        $key = $consumer->getKey();
        $key256 = DataConnector::getConsumerKey($key);
        if ($key === $key256) {
            $key = null;
        }
        $protected = ($consumer->protected) ? 1 : 0;
        $enabled = ($consumer->enabled)? 1 : 0;
        $profile = (!empty($consumer->profile)) ? json_encode($consumer->profile) : null;
        $settingsValue = serialize($consumer->getSettings());
        $time = time();
        $now = date("{$this->dateFormat} {$this->timeFormat}", $time);
        $from = null;
        if (!is_null($consumer->enableFrom)) {
            $from = date("{$this->dateFormat} {$this->timeFormat}", $consumer->enableFrom);
        }
        $until = null;
        if (!is_null($consumer->enableUntil)) {
            $until = date("{$this->dateFormat} {$this->timeFormat}", $consumer->enableUntil);
        }
        $last = null;
        if (!is_null($consumer->lastAccess)) {
            $last = date($this->dateFormat, $consumer->lastAccess);
        }
        $data = new \stdClass();
        $data->consumer_key256 = $key256;
        $data->consumer_key = $key;
        $data->name = $consumer->name;
        $data->secret = $consumer->secret;
        $data->lti_version = $consumer->ltiVersion;
        $data->consumer_name = $consumer->consumerName;
        $data->consumer_version = $consumer->consumerVersion;
        $data->consumer_guid = $consumer->consumerGuid;
        $data->profile = $profile;
        $data->tool_proxy = $consumer->toolProxy;
        $data->settings = $settingsValue;
        $data->protected = $protected;
        $data->enabled = $enabled;
        $data->enable_from = $from;
        $data->enable_until = $until;
        $data->last_access = $last;
        $data->updated = $now;

        $id = $consumer->getRecordId();
        if (empty($id)) {
            $data->created = $now;
            $id = $DB->insert_record($table, $data);
            $consumer->setRecordId($id);
            $consumer->created = $time;
            // TODO catch error and set $ok to false
            $ok = !empty($id);
        } else {
            $data->id = $id;
            $ok = $DB->update_record($table, $data);
        }
        if ($ok) {
            $consumer->updated = $time;
        }

        return $ok;

    }

/**
 * Delete tool consumer object.
 *
 * @param ToolConsumer $consumer Consumer object
 *
 * @return boolean True if the tool consumer object was successfully deleted
 */
    public function deleteToolConsumer($consumer)
    {

// Delete any nonce values for this consumer
        $sql = sprintf("DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::NONCE_TABLE_NAME . ' WHERE consumer_pk = %d',
                       $consumer->getRecordId());
        #mysql_query($sql);

// Delete any outstanding share keys for resource links for this consumer
        $sql = sprintf('DELETE sk ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' sk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON sk.resource_link_pk = rl.resource_link_pk ' .
                       'WHERE rl.consumer_pk = %d',
                       $consumer->getRecordId());
        #mysql_query($sql);

// Delete any outstanding share keys for resource links for contexts in this consumer
        $sql = sprintf('DELETE sk ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' sk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON sk.resource_link_pk = rl.resource_link_pk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        #mysql_query($sql);

// Delete any users in resource links for this consumer
        $sql = sprintf('DELETE u ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' u ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON u.resource_link_pk = rl.resource_link_pk ' .
                       'WHERE rl.consumer_pk = %d',
                       $consumer->getRecordId());
        #mysql_query($sql);

// Delete any users in resource links for contexts in this consumer
        $sql = sprintf('DELETE u ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' u ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON u.resource_link_pk = rl.resource_link_pk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        #mysql_query($sql);

// Update any resource links for which this consumer is acting as a primary resource link
        $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' prl ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON prl.primary_resource_link_pk = rl.resource_link_pk ' .
                       'SET prl.primary_resource_link_pk = NULL, prl.share_approved = NULL ' .
                       'WHERE rl.consumer_pk = %d',
                       $consumer->getRecordId());
        #$ok = mysql_query($sql);

// Update any resource links for contexts in which this consumer is acting as a primary resource link
        $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' prl ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON prl.primary_resource_link_pk = rl.resource_link_pk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
                       'SET prl.primary_resource_link_pk = NULL, prl.share_approved = NULL ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        #$ok = mysql_query($sql);

// Delete any resource links for this consumer
        $sql = sprintf('DELETE rl ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
                       'WHERE rl.consumer_pk = %d',
                       $consumer->getRecordId());
        #mysql_query($sql);

// Delete any resource links for contexts in this consumer
        $sql = sprintf('DELETE rl ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        #mysql_query($sql);

// Delete any contexts for this consumer
        $sql = sprintf('DELETE c ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        #mysql_query($sql);

// Delete consumer
        $sql = sprintf('DELETE c ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::CONSUMER_TABLE_NAME . ' c ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        #$ok = mysql_query($sql);

        if ($ok) {
            $consumer->initialize();
        }

        return $ok;

    }

###
#    Load all tool consumers from the database
###
    public function getToolConsumers()
    {

        $consumers = array();

        $sql = 'SELECT consumer_pk, consumer_key, consumer_key, name, secret, lti_version, consumer_name, consumer_version, consumer_guid, ' .
               'profile, tool_proxy, settings, ' .
               'protected, enabled, enable_from, enable_until, last_access, created, updated ' .
               "FROM {$this->dbTableNamePrefix}" . DataConnector::CONSUMER_TABLE_NAME . ' ' .
               'ORDER BY name';
        #$rsConsumers = mysql_query($sql);
        if ($rsConsumers) {
            while ($row = mysql_fetch_object($rsConsumers)) {
                $consumer = new ToolProvider\ToolConsumer($row->consumer_key, $this);
                $consumer->setRecordId(intval($row->consumer_pk));
                $consumer->name = $row->name;
                $consumer->secret = $row->secret;
                $consumer->ltiVersion = $row->lti_version;
                $consumer->consumerName = $row->consumer_name;
                $consumer->consumerVersion = $row->consumer_version;
                $consumer->consumerGuid = $row->consumer_guid;
                $consumer->profile = json_decode($row->profile);
                $consumer->toolProxy = $row->tool_proxy;
                $settings = unserialize($row->settings);
                if (!is_array($settings)) {
                    $settings = array();
                }
                $consumer->setSettings($settings);
                $consumer->protected = (intval($row->protected) === 1);
                $consumer->enabled = (intval($row->enabled) === 1);
                $consumer->enableFrom = null;
                if (!is_null($row->enable_from)) {
                    $consumer->enableFrom = strtotime($row->enable_from);
                }
                $consumer->enableUntil = null;
                if (!is_null($row->enable_until)) {
                    $consumer->enableUntil = strtotime($row->enable_until);
                }
                $consumer->lastAccess = null;
                if (!is_null($row->last_access)) {
                    $consumer->lastAccess = strtotime($row->last_access);
                }
                $consumer->created = strtotime($row->created);
                $consumer->updated = strtotime($row->updated);
                $consumers[] = $consumer;
            }
            mysql_free_result($rsConsumers);
        }

        return $consumers;

    }

###
###  ToolProxy methods
###

###
#    Load the tool proxy from the database
###
    public function loadToolProxy($toolProxy)
    {

        return false;

    }

###
#    Save the tool proxy to the database
###
    public function saveToolProxy($toolProxy)
    {

        return false;

    }

###
#    Delete the tool proxy from the database
###
    public function deleteToolProxy($toolProxy)
    {

        return false;

    }

###
###  Context methods
###

    /**
     * Load context object.
     *
     * @param Context $context Context object
     * @return boolean True if the context object was successfully loaded
     */
    public function loadContext($context) {
        global $DB;
        $table = $this->dbTableNamePrefix . DataConnector::CONTEXT_TABLE_NAME;
        if (!empty($context->getRecordId())) {
            $params = ['context_pk' => $context->getRecordId()];
        } else {
            $params = [
                'consumer_pk' => $context->getConsumer()->getRecordId(),
                'lti_context_id' => $context->ltiContextId
            ];
        }
        if ($row = $DB->get_record($table, $params)) {
            $context->setRecordId(intval($row->context_pk));
            $context->setConsumerId(intval($row->consumer_pk));
            $context->ltiContextId = $row->lti_context_id;
            $settings = unserialize($row->settings);
            if (!is_array($settings)) {
                $settings = array();
            }
            $context->setSettings($settings);
            $context->created = strtotime($row->created);
            $context->updated = strtotime($row->updated);
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
        $time = time();
        $now = date("{$this->dateFormat} {$this->timeFormat}", $time);
        $settingsValue = serialize($context->getSettings());
        $id = $context->getRecordId();
        $consumer_pk = $context->getConsumer()->getRecordId();
        $table = $this->dbTableNamePrefix . DataConnector::CONTEXT_TABLE_NAME;
        $isinsert = empty($id);
        if ($isinsert) {
            $params = [
                'consumer_pk' => $consumer_pk,
                'lti_context_id' => $context->ltiContextId,
                'settings' => $settingsValue,
                'created' => $now,
                'updated' => $now,
            ];
            $sql = "INSERT INTO {{$table}} (consumer_pk, lti_context_id, settings, created, updated) 
                         VALUES (:consumer_pk, :lti_context_id, :settings, :created, :updated)";
        } else {
            $params = [
                'lti_context_id' => $context->ltiContextId,
                'settings' => $settingsValue,
                'updated' => $now,
                'consumer_pk' => $consumer_pk,
                'context_pk' => $id
            ];
            $sql = "UPDATE {{$table}} 
                       SET lti_context_id = :lti_context_id, 
                           settings = :settings, 
                           updated = :updated 
                     WHERE consumer_pk = :consumer_pk 
                           AND context_pk = :context_pk";
        }

        if ($DB->execute($sql, $params)) {
            if ($isinsert) {
                // consumer_pk, lti_context_id, created and updated should be enough to identify the data we added.
                unset($params['settings']);
                if ($contextrecord = $DB->get_record($table, $params)) {
                    $context->setRecordId($contextrecord->context_pk);
                    $context->created = $time;
                }
            }
            $context->updated = $time;
            return true;
        }

        return false;
    }

/**
 * Delete context object.
 *
 * @param Context $context Context object
 *
 * @return boolean True if the Context object was successfully deleted
 */
    public function deleteContext($context)
    {

// Delete any outstanding share keys for resource links for this context
        $sql = sprintf('DELETE sk ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' sk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON sk.resource_link_pk = rl.resource_link_pk ' .
                       'WHERE rl.context_pk = %d',
                       $context->getRecordId());
        #mysql_query($sql);

// Delete any users in resource links for this context
        $sql = sprintf('DELETE u ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' u ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON u.resource_link_pk = rl.resource_link_pk ' .
                       'WHERE rl.context_pk = %d',
                       $context->getRecordId());
        #mysql_query($sql);

// Update any resource links for which this consumer is acting as a primary resource link
        $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' prl ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON prl.primary_resource_link_pk = rl.resource_link_pk ' .
                       'SET prl.primary_resource_link_pk = null, prl.share_approved = null ' .
                       'WHERE rl.context_pk = %d',
                       $context->getRecordId());
        #$ok = mysql_query($sql);

// Delete any resource links for this consumer
        $sql = sprintf('DELETE rl ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
                       'WHERE rl.context_pk = %d',
                       $context->getRecordId());
        #mysql_query($sql);

// Delete context
        $sql = sprintf('DELETE c ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ',
                       'WHERE c.context_pk = %d',
                       $context->getRecordId());
        #$ok = mysql_query($sql);
        if ($ok) {
            $context->initialize();
        }

        return $ok;

    }

###
###  ResourceLink methods
###

    /**
     * Load resource link object.
     *
     * @param ResourceLink $resourceLink Resource_Link object
     *
     * @return boolean True if the resource link object was successfully loaded
     */
    public function loadResourceLink($resourceLink) {
        global $DB;

        $table = $this->dbTableNamePrefix . DataConnector::RESOURCE_LINK_TABLE_NAME;

        $ok = false;
        $resourceid = $resourceLink->getRecordId();
        $fields = 'id, context_pk, consumer_pk, lti_resource_link_id, settings, primary_resource_link_pk, share_approved, created, updated';
        if (!empty($resourceid)) {
            $row = $DB->get_record(
                $table,
                array('id' => $resourceid),
                '',
                $fields
            );
        } else if (!empty($resourceLink->getContext())) {
            $row = $DB->get_record(
                $table,
                array(
                    'context_pk' => $resourceLink->getContext()->getRecordId(),
                    'lti_resource_link_id' => $resourceLink->getId()
                ),
                '',
                $fields
            );
        } else {
            $fields = 'r.id, r.context_pk, r.consumer_pk, r.lti_resource_link_id, r.settings, r.primary_resource_link_pk, r.share_approved, r.created, r.updated ';
            $sql = "SELECT $fields " ."FROM {{$table}} r LEFT OUTER JOIN " .
                   '{' . $this->dbTableNamePrefix . DataConnector::CONTEXT_TABLE_NAME . '} c ' .
                   'ON r.context_pk = c.context_pk ' .
                   ' WHERE ((r.consumer_pk = ?) OR (c.consumer_pk = ?)) AND (lti_resource_link_id = ?)';
            // TODO fix this to use proper xmldb instead of this crazy gross SQL!!
            $row = $DB->get_record_sql(
                $sql,
                array($resourceLink->getConsumer()->getRecordId(),
                $resourceLink->getConsumer()->getRecordId(),
                $resourceLink->getId())
            );
        }
        if ($row) {
            $resourceLink->setRecordId(intval($row->id));
            if (!is_null($row->context_pk)) {
                $resourceLink->setContextId(intval($row->context_pk));
            } else {
                $resourceLink->setContextId(null);
            }
            if (!is_null($row->consumer_pk)) {
                $resourceLink->setConsumerId(intval($row->consumer_pk));
            } else {
                $resourceLink->setConsumerId(null);
            }
            $resourceLink->ltiResourceLinkId = $row->lti_resource_link_id;
            $settings = unserialize($row->settings);
            if (!is_array($settings)) {
                $settings = array();
            }
            $resourceLink->setSettings($settings);
            if (!is_null($row->primary_resource_link_pk)) {
                $resourceLink->primaryResourceLinkId = intval($row->primary_resource_link_pk);
            } else {
                $resourceLink->primaryResourceLinkId = null;
            }
            $resourceLink->shareApproved = (is_null($row->share_approved)) ? null : (intval($row->share_approved) === 1);
            $resourceLink->created = strtotime($row->created);
            $resourceLink->updated = strtotime($row->updated);
            $ok = true;
        }

        return $ok;

    }

    /**
     * Save resource link object.
     *
     * @param ResourceLink $resourceLink Resource_Link object
     *
     * @return boolean True if the resource link object was successfully saved
     */
    public function saveResourceLink($resourceLink) {
        global $DB;

        $table = $this->dbTableNamePrefix . DataConnector::RESOURCE_LINK_TABLE_NAME;

        if (is_null($resourceLink->shareApproved)) {
            $approved = null;
        } else if ($resourceLink->shareApproved) {
            $approved = 1;
        } else {
            $approved = 0;
        }
        if (empty($resourceLink->primaryResourceLinkId)) {
            $primaryResourceLinkId = null;
        } else {
            $primaryResourceLinkId = strval($resourceLink->primaryResourceLinkId);
        }
        $time = time();
        $now = date("{$this->dateFormat} {$this->timeFormat}", $time);
        $settingsValue = serialize($resourceLink->getSettings());
        if (!empty($resourceLink->getContext())) {
            $consumerId = null;
            $contextId = strval($resourceLink->getContext()->getRecordId());
        } else if (!empty($resourceLink->getContextId())) {
            $consumerId = null;
            $contextId = strval($resourceLink->getContextId());
        } else {
            $consumerId = strval($resourceLink->getConsumer()->getRecordId());
            $contextId = null;
        }
        $id = $resourceLink->getRecordId();

        $data = new \stdClass();
        $data->consumer_pk = $consumerId;
        $data->lti_resource_link_id = $resourceLink->getId();
        $data->settings = $settingsValue;
        $data->primary_resource_link_pk = $primaryResourceLinkId;
        $data->share_approved = $approved;
        $data->updated = $now;

        $returnid = null;

        if (empty($id)) {
            $data->created = $now;
            $data->context_pk = $contextId;
            $returnid = $DB->insert_record($table, $data);
        } else if ($contextId !== 'NULL') {
            $sql = "UPDATE {{$table}} SET " .
                   'consumer_pk = ?, lti_resource_link_id = ?, settings = ?, '.
                   'primary_resource_link_pk = ?, share_approved = ?, updated = ? ' .
                   'WHERE (context_pk = ?) AND (id = ?)';
            $DB->execute($sql, [
                   $consumerId, $resourceLink->getId(),
                   $settingsValue, $primaryResourceLinkId, $approved, $now,
                   $contextId, $id
                ]
            );
            $returnid = $id;
            // TODO dml-ify.
        } else {
            $sql = "UPDATE {{$table}} SET " .
                   'context_pk = ?, lti_resource_link_id = ?, settings = ?, '.
                   'primary_resource_link_pk = ?, share_approved = ?, updated = ? ' .
                   'WHERE (consumer_pk = ?) AND (id = ?)';
            $DB->execute($sql, [
                $contextId, $resourceLink->getId(),
                       $settingsValue, $primaryResourceLinkId, $approved, $now,
                       $consumerId, $id
                ]
            );
            $returnid = $id;
            // TODO dml-ify.
        }
        $ok = !empty($returnid);
        if ($ok) {
            if (empty($id)) {
                $resourceLink->setRecordId($newid);
                $resourceLink->created = $time;
            }
            $resourceLink->updated = $time;
        }

        return $ok;

    }

/**
 * Delete resource link object.
 *
 * @param ResourceLink $resourceLink Resource_Link object
 *
 * @return boolean True if the resource link object was successfully deleted
 */
    public function deleteResourceLink($resourceLink)
    {

// Delete any outstanding share keys for resource links for this consumer
        $sql = sprintf("DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' ' .
                       'WHERE (resource_link_pk = %d)',
                       $resourceLink->getRecordId());
        #$ok = mysql_query($sql);

// Delete users
        if ($ok) {
            $sql = sprintf("DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' ' .
                           'WHERE (resource_link_pk = %d)',
                           $resourceLink->getRecordId());
            #$ok = mysql_query($sql);
        }

// Update any resource links for which this is the primary resource link
        if ($ok) {
            $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
                           'SET primary_resource_link_pk = NULL ' .
                           'WHERE (primary_resource_link_pk = %d)',
                           $resourceLink->getRecordId());
            #$ok = mysql_query($sql);
        }

// Delete resource link
        if ($ok) {
            $sql = sprintf("DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
                           'WHERE (id= %s)',
                           $resourceLink->getRecordId());
            #$ok = mysql_query($sql);
        }

        if ($ok) {
            $resourceLink->initialize();
        }

        return $ok;

    }

/**
 * Get array of user objects.
 *
 * Obtain an array of User objects for users with a result sourcedId.  The array may include users from other
 * resource links which are sharing this resource link.  It may also be optionally indexed by the user ID of a specified scope.
 *
 * @param ResourceLink $resourceLink      Resource link object
 * @param boolean     $localOnly True if only users within the resource link are to be returned (excluding users sharing this resource link)
 * @param int         $idScope     Scope value to use for user IDs
 *
 * @return array Array of User objects
 */
    public function getUserResultSourcedIDsResourceLink($resourceLink, $localOnly, $idScope)
    {

        $users = array();

        if ($localOnly) {
            $sql = sprintf('SELECT u.user_pk, u.lti_result_sourcedid, u.lti_user_id, u.created, u.updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' AS u '  .
                           "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' AS rl '  .
                           'ON u.resource_link_pk = rl.resource_link_pk ' .
                           "WHERE (rl.resource_link_pk = %d) AND (rl.primary_resource_link_pk IS NULL)",
                           $resourceLink->getRecordId());
        } else {
            $sql = sprintf('SELECT u.user_pk, u.lti_result_sourcedid, u.lti_user_id, u.created, u.updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' AS u '  .
                           "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' AS rl '  .
                           'ON u.resource_link_pk = rl.resource_link_pk ' .
                           'WHERE ((rl.resource_link_pk = %d) AND (rl.primary_resource_link_pk IS NULL)) OR ' .
                           '((rl.primary_resource_link_pk = %d) AND (share_approved = 1))',
                           $resourceLink->getRecordId(), $resourceLink->getRecordId());
        }
        #$rsUser = mysql_query($sql);
        if ($rsUser) {
            while ($row = mysql_fetch_object($rsUser)) {
                $user = ToolProvider\User::fromResourceLink($resourceLink, $row->lti_user_id);
                $user->setRecordId(intval($row->user_pk));
                $user->ltiResultSourcedId = $row->lti_result_sourcedid;
                $user->created = strtotime($row->created);
                $user->updated = strtotime($row->updated);
                if (is_null($idScope)) {
                    $users[] = $user;
                } else {
                    $users[$user->getId($idScope)] = $user;
                }
            }
        }

        return $users;

    }

/**
 * Get array of shares defined for this resource link.
 *
 * @param ResourceLink $resourceLink Resource_Link object
 *
 * @return array Array of ResourceLinkShare objects
 */
    public function getSharesResourceLink($resourceLink)
    {

        $shares = array();

        $sql = sprintf('SELECT consumer_pk, resource_link_pk, share_approved ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
                       'WHERE (primary_resource_link_pk = %d) ' .
                       'ORDER BY consumer_pk',
                       $resourceLink->getRecordId());
        #$rsShare = mysql_query($sql);
        if ($rsShare) {
            while ($row = mysql_fetch_object($rsShare)) {
                $share = new ToolProvider\ResourceLinkShare();
                $share->resourceLinkId = intval($row->resource_link_pk);
                $share->approved = (intval($row->share_approved) === 1);
                $shares[] = $share;
            }
        }

        return $shares;

    }


###
###  ConsumerNonce methods
###

    /**
     * Load nonce object.
     *
     * @param ConsumerNonce $nonce Nonce object
     *
     * @return boolean True if the nonce object was successfully loaded
     */
    public function loadConsumerNonce($nonce) {
        global $DB;

        $table = $this->dbTableNamePrefix . DataConnector::NONCE_TABLE_NAME;

        $ok = true;

        // Delete any expired nonce values
        $now = date("{$this->dateFormat} {$this->timeFormat}", time());
        $DB->delete_records_select($table, "expires <= ?", array($now));

        // Load the nonce
        $result = $DB->get_record($table, array('consumer_pk' => $nonce->getConsumer()->getRecordId(), 'value' => $nonce->getValue()), 'value');

        return !empty($result);

    }

    /**
     * Save nonce object.
     *
     * @param ConsumerNonce $nonce Nonce object
     *
     * @return boolean True if the nonce object was successfully saved
     */
    public function saveConsumerNonce($nonce) {
        global $DB;

        $table = $this->dbTableNamePrefix . DataConnector::NONCE_TABLE_NAME;

        $expires = date("{$this->dateFormat} {$this->timeFormat}", $nonce->expires);

        $data = new \stdClass();
        $data->consumer_pk = $nonce->getConsumer()->getRecordId();
        $data->value = $nonce->getValue();
        $data->expires = $expires;

        return $DB->insert_record($table, $data, false);

    }


###
###  ResourceLinkShareKey methods
###

/**
 * Load resource link share key object.
 *
 * @param ResourceLinkShareKey $shareKey Resource_Link share key object
 *
 * @return boolean True if the resource link share key object was successfully loaded
 */
    public function loadResourceLinkShareKey($shareKey)
    {

        $ok = false;

// Clear expired share keys
        $now = date("{$this->dateFormat} {$this->timeFormat}", time());
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . " WHERE expires <= '{$now}'";
        #mysql_query($sql);

// Load share key
        $id = mysql_real_escape_string($shareKey->getId());
        $sql = 'SELECT resource_link_pk, auto_approve, expires ' .
               "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' ' .
               "WHERE share_key_id = '{$id}'";
        #$rsShareKey = mysql_query($sql);
        if ($rsShareKey) {
            $row = mysql_fetch_object($rsShareKey);
            if ($row && (intval($row->resource_link_pk) === $shareKey->resourceLinkId)) {
                $shareKey->autoApprove = (intval($row->auto_approve) === 1);
                $shareKey->expires = strtotime($row->expires);
                $ok = true;
            }
        }

        return $ok;

    }

/**
 * Save resource link share key object.
 *
 * @param ResourceLinkShareKey $shareKey Resource link share key object
 *
 * @return boolean True if the resource link share key object was successfully saved
 */
    public function saveResourceLinkShareKey($shareKey)
    {

        if ($shareKey->autoApprove) {
            $approve = 1;
        } else {
            $approve = 0;
        }
        $expires = date("{$this->dateFormat} {$this->timeFormat}", $shareKey->expires);
        $sql = sprintf("INSERT INTO {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' ' .
                       '(share_key_id, resource_link_pk, auto_approve, expires) ' .
                       "VALUES (%s, %d, {$approve}, '{$expires}')",
                       DataConnector::quoted($shareKey->getId()), $shareKey->resourceLinkId);
        #$ok = mysql_query($sql);

        return $ok;

    }

/**
 * Delete resource link share key object.
 *
 * @param ResourceLinkShareKey $shareKey Resource link share key object
 *
 * @return boolean True if the resource link share key object was successfully deleted
 */
    public function deleteResourceLinkShareKey($shareKey)
    {

        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . " WHERE share_key_id = '{$shareKey->getId()}'";

        #$ok = mysql_query($sql);

        if ($ok) {
            $shareKey->initialize();
        }

        return $ok;

    }


###
###  User methods
###

/**
 * Load user object.
 *
 * @param User $user User object
 *
 * @return boolean True if the user object was successfully loaded
 */
    public function loadUser($user) {
        global $DB;

        $table = $this->dbTableNamePrefix . DataConnector::USER_RESULT_TABLE_NAME;

        $ok = false;
        $userid = $user->getRecordId();
        $fields = 'user_pk, resource_link_pk, lti_user_id, lti_result_sourcedid, created, updated';
        if (!empty($userid)) {
            $row = $DB->get_record($table, array('user_pk' => $userid), $fields);
        } else {
            $resourcelinkid = $user->getResourceLink()->getRecordId();
            $userid = $user->getId(ToolProvider\ToolProvider::ID_SCOPE_ID_ONLY);
            $row = $DB->get_record_select(
                $table,
                "resource_link_pk = ? AND lti_user_id = ?",
                array($resourcelinkid, $userid),
                $fields
            );
        }
        if ($row) {
            $user->setRecordId(intval($row->user_pk));
            $user->setResourceLinkId(intval($row->resource_link_pk));
            $user->ltiUserId = $row->lti_user_id;
            $user->ltiResultSourcedId = $row->lti_result_sourcedid;
            $user->created = strtotime($row->created);
            $user->updated = strtotime($row->updated);
            $ok = true;
        }

        return $ok;

    }

    /**
     * Save user object.
     *
     * @param User $user User object
     * @return boolean True if the user object was successfully saved
     */
    public function saveUser($user) {
        global $DB;

        $time = time();
        $now = date("{$this->dateFormat} {$this->timeFormat}", $time);
        $table = $this->dbTableNamePrefix . DataConnector::USER_RESULT_TABLE_NAME;
        $isinsert = is_null($user->created);
        if ($isinsert) {
            $params = [
                'resource_link_pk' => $user->getResourceLink()->getRecordId(),
                'lti_user_id' => $user->getId(ToolProvider\ToolProvider::ID_SCOPE_ID_ONLY),
                'lti_result_sourcedid' => $user->ltiResultSourcedId,
                'created' => $now,
                'updated' => $now,
            ];
            $sql = "INSERT INTO {{$table}} (resource_link_pk, lti_user_id, lti_result_sourcedid, created, updated) 
                         VALUES (:resource_link_pk, :lti_user_id, :lti_result_sourcedid, :created, :updated)";
        } else {
            $params = [
                'lti_result_sourcedid' => $user->ltiResultSourcedId,
                'updated' => $now,
                'user_pk' => $user->getRecordId()
            ];
            $sql = "UPDATE {{$table}} 
                       SET lti_result_sourcedid = :lti_result_sourcedid, 
                           updated = :updated 
                     WHERE user_pk = :user_pk";
        }

        if ($DB->execute($sql, $params)) {
            if ($isinsert) {
                if ($userrecord = $DB->get_record($table, $params)) {
                    $user->setRecordId($userrecord->user_pk);
                    $user->created = $time;
                }
            }
            $user->updated = $time;
            return true;
        }

        return false;
    }

/**
 * Delete user object.
 *
 * @param User $user User object
 *
 * @return boolean True if the user object was successfully deleted
 */
    public function deleteUser($user)
    {

        $sql = sprintf("DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' ' .
                       'WHERE (user_pk = %d)',
                       $user->getRecordId());
        #$ok = mysql_query($sql);

        if ($ok) {
            $user->initialize();
        }

        return $ok;

    }

}
