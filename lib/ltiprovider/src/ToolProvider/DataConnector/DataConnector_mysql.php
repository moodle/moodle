<?php

namespace IMSGlobal\LTI\ToolProvider\DataConnector;

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\ConsumerNonce;
use IMSGlobal\LTI\ToolProvider\Context;
use IMSGlobal\LTI\ToolProvider\ResourceLink;
use IMSGlobal\LTI\ToolProvider\ResourceLinkShareKey;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use IMSGlobal\LTI\ToolProvider\User;

/**
 * Class to represent an LTI Data Connector for MySQL
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

###
#    NB This class assumes that a MySQL connection has already been opened to the appropriate schema
###


class DataConnector_mysql extends DataConnector
{

###
###  ToolConsumer methods
###

/**
 * Load tool consumer object.
 *
 * @param ToolConsumer $consumer ToolConsumer object
 *
 * @return boolean True if the tool consumer object was successfully loaded
 */
    public function loadToolConsumer($consumer)
    {

        $ok = false;
        if (!empty($consumer->getRecordId())) {
            $sql = sprintf('SELECT consumer_pk, name, consumer_key256, consumer_key, secret, lti_version, ' .
                           'consumer_name, consumer_version, consumer_guid, ' .
                           'profile, tool_proxy, settings, protected, enabled, ' .
                           'enable_from, enable_until, last_access, created, updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::CONSUMER_TABLE_NAME . ' ' .
                           "WHERE consumer_pk = %d",
                           $consumer->getRecordId());
        } else {
            $key256 = DataConnector::getConsumerKey($consumer->getKey());
            $sql = sprintf('SELECT consumer_pk, name, consumer_key256, consumer_key, secret, lti_version, ' .
                           'consumer_name, consumer_version, consumer_guid, ' .
                           'profile, tool_proxy, settings, protected, enabled, ' .
                           'enable_from, enable_until, last_access, created, updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::CONSUMER_TABLE_NAME . ' ' .
                           "WHERE consumer_key256 = %s",
                           DataConnector::quoted($key256));
        }
        $rsConsumer = mysql_query($sql);
        if ($rsConsumer) {
            while ($row = mysql_fetch_object($rsConsumer)) {
                if (empty($key256) || empty($row->consumer_key) || ($consumer->getKey() === $row->consumer_key)) {
                    $consumer->setRecordId(intval($row->consumer_pk));
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
            mysql_free_result($rsConsumer);
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
    public function saveToolConsumer($consumer)
    {

        $id = $consumer->getRecordId();
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
        if (empty($id)) {
            $sql = sprintf("INSERT INTO {$this->dbTableNamePrefix}" . DataConnector::CONSUMER_TABLE_NAME . ' (consumer_key256, consumer_key, name, ' .
                           'secret, lti_version, consumer_name, consumer_version, consumer_guid, profile, tool_proxy, settings, protected, enabled, ' .
                           'enable_from, enable_until, last_access, created, updated) ' .
                           'VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %s, %s, %s, %s, %s)',
                           DataConnector::quoted($key256), DataConnector::quoted($key), DataConnector::quoted($consumer->name),
                           DataConnector::quoted($consumer->secret), DataConnector::quoted($consumer->ltiVersion),
                           DataConnector::quoted($consumer->consumerName), DataConnector::quoted($consumer->consumerVersion), DataConnector::quoted($consumer->consumerGuid),
                           DataConnector::quoted($profile), DataConnector::quoted($consumer->toolProxy), DataConnector::quoted($settingsValue),
                           $protected, $enabled, DataConnector::quoted($from), DataConnector::quoted($until), DataConnector::quoted($last),
                           DataConnector::quoted($now), DataConnector::quoted($now));
        } else {
            $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::CONSUMER_TABLE_NAME . ' SET ' .
                           'consumer_key256 = %s, consumer_key = %s, ' .
                           'name = %s, secret= %s, lti_version = %s, consumer_name = %s, consumer_version = %s, consumer_guid = %s, ' .
                           'profile = %s, tool_proxy = %s, settings = %s, ' .
                           'protected = %d, enabled = %d, enable_from = %s, enable_until = %s, last_access = %s, updated = %s ' .
                           'WHERE consumer_pk = %d',
                           DataConnector::quoted($key256), DataConnector::quoted($key),
                           DataConnector::quoted($consumer->name),
                           DataConnector::quoted($consumer->secret), DataConnector::quoted($consumer->ltiVersion),
                           DataConnector::quoted($consumer->consumerName), DataConnector::quoted($consumer->consumerVersion), DataConnector::quoted($consumer->consumerGuid),
                           DataConnector::quoted($profile), DataConnector::quoted($consumer->toolProxy), DataConnector::quoted($settingsValue),
                           $protected, $enabled,
                           DataConnector::quoted($from), DataConnector::quoted($until), DataConnector::quoted($last),
                           DataConnector::quoted($now), $consumer->getRecordId());
        }
        $ok = mysql_query($sql);
        if ($ok) {
            if (empty($id)) {
                $consumer->setRecordId(mysql_insert_id());
                $consumer->created = $time;
            }
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
        mysql_query($sql);

// Delete any outstanding share keys for resource links for this consumer
        $sql = sprintf('DELETE sk ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' sk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON sk.resource_link_pk = rl.resource_link_pk ' .
                       'WHERE rl.consumer_pk = %d',
                       $consumer->getRecordId());
        mysql_query($sql);

// Delete any outstanding share keys for resource links for contexts in this consumer
        $sql = sprintf('DELETE sk ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' sk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON sk.resource_link_pk = rl.resource_link_pk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        mysql_query($sql);

// Delete any users in resource links for this consumer
        $sql = sprintf('DELETE u ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' u ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON u.resource_link_pk = rl.resource_link_pk ' .
                       'WHERE rl.consumer_pk = %d',
                       $consumer->getRecordId());
        mysql_query($sql);

// Delete any users in resource links for contexts in this consumer
        $sql = sprintf('DELETE u ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' u ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON u.resource_link_pk = rl.resource_link_pk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        mysql_query($sql);

// Update any resource links for which this consumer is acting as a primary resource link
        $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' prl ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON prl.primary_resource_link_pk = rl.resource_link_pk ' .
                       'SET prl.primary_resource_link_pk = NULL, prl.share_approved = NULL ' .
                       'WHERE rl.consumer_pk = %d',
                       $consumer->getRecordId());
        $ok = mysql_query($sql);

// Update any resource links for contexts in which this consumer is acting as a primary resource link
        $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' prl ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON prl.primary_resource_link_pk = rl.resource_link_pk ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
                       'SET prl.primary_resource_link_pk = NULL, prl.share_approved = NULL ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        $ok = mysql_query($sql);

// Delete any resource links for this consumer
        $sql = sprintf('DELETE rl ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
                       'WHERE rl.consumer_pk = %d',
                       $consumer->getRecordId());
        mysql_query($sql);

// Delete any resource links for contexts in this consumer
        $sql = sprintf('DELETE rl ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        mysql_query($sql);

// Delete any contexts for this consumer
        $sql = sprintf('DELETE c ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        mysql_query($sql);

// Delete consumer
        $sql = sprintf('DELETE c ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::CONSUMER_TABLE_NAME . ' c ' .
                       'WHERE c.consumer_pk = %d',
                       $consumer->getRecordId());
        $ok = mysql_query($sql);

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
        $rsConsumers = mysql_query($sql);
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
 *
 * @return boolean True if the context object was successfully loaded
 */
    public function loadContext($context)
    {

        $ok = false;
        if (!empty($context->getRecordId())) {
            $sql = sprintf('SELECT context_pk, consumer_pk, lti_context_id, settings, created, updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' ' .
                           'WHERE (context_pk = %d)',
                           $context->getRecordId());
        } else {
            $sql = sprintf('SELECT context_pk, consumer_pk, lti_context_id, settings, created, updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' ' .
                           'WHERE (consumer_pk = %d) AND (lti_context_id = %s)',
                           $context->getConsumer()->getRecordId(), DataConnector::quoted($context->ltiContextId));
        }
        $rs_context = mysql_query($sql);
        if ($rs_context) {
            $row = mysql_fetch_object($rs_context);
            if ($row) {
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
                $ok = true;
            }
        }

        return $ok;

    }

/**
 * Save context object.
 *
 * @param Context $context Context object
 *
 * @return boolean True if the context object was successfully saved
 */
    public function saveContext($context)
    {

        $time = time();
        $now = date("{$this->dateFormat} {$this->timeFormat}", $time);
        $settingsValue = serialize($context->getSettings());
        $id = $context->getRecordId();
        $consumer_pk = $context->getConsumer()->getRecordId();
        if (empty($id)) {
            $sql = sprintf("INSERT INTO {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' (consumer_pk, lti_context_id, ' .
                           'settings, created, updated) ' .
                           'VALUES (%d, %s, %s, %s, %s)',
               $consumer_pk, DataConnector::quoted($context->ltiContextId),
               DataConnector::quoted($settingsValue),
               DataConnector::quoted($now), DataConnector::quoted($now));
        } else {
            $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' SET ' .
                           'lti_context_id = %s, settings = %s, '.
                           'updated = %s' .
                           'WHERE (consumer_pk = %d) AND (context_pk = %d)',
               DataConnector::quoted($context->ltiContextId), DataConnector::quoted($settingsValue),
               DataConnector::quoted($now), $consumer_pk, $id);
        }
        $ok = mysql_query($sql);
        if ($ok) {
            if (empty($id)) {
                $context->setRecordId(mysql_insert_id());
                $context->created = $time;
            }
            $context->updated = $time;
        }

        return $ok;

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
        mysql_query($sql);

// Delete any users in resource links for this context
        $sql = sprintf('DELETE u ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' u ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON u.resource_link_pk = rl.resource_link_pk ' .
                       'WHERE rl.context_pk = %d',
                       $context->getRecordId());
        mysql_query($sql);

// Update any resource links for which this consumer is acting as a primary resource link
        $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' prl ' .
                       "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ON prl.primary_resource_link_pk = rl.resource_link_pk ' .
                       'SET prl.primary_resource_link_pk = null, prl.share_approved = null ' .
                       'WHERE rl.context_pk = %d',
                       $context->getRecordId());
        $ok = mysql_query($sql);

// Delete any resource links for this consumer
        $sql = sprintf('DELETE rl ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
                       'WHERE rl.context_pk = %d',
                       $context->getRecordId());
        mysql_query($sql);

// Delete context
        $sql = sprintf('DELETE c ' .
                       "FROM {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ',
                       'WHERE c.context_pk = %d',
                       $context->getRecordId());
        $ok = mysql_query($sql);
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
    public function loadResourceLink($resourceLink)
    {

        $ok = false;
        if (!empty($resourceLink->getRecordId())) {
            $sql = sprintf('SELECT resource_link_pk, context_pk, consumer_pk, lti_resource_link_id, settings, primary_resource_link_pk, share_approved, created, updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
                           'WHERE (resource_link_pk = %d)',
                           $resourceLink->getRecordId());
        } else if (!empty($resourceLink->getContext())) {
            $sql = sprintf('SELECT resource_link_pk, context_pk, consumer_pk, lti_resource_link_id, settings, primary_resource_link_pk, share_approved, created, updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
                           'WHERE (context_pk = %d) AND (lti_resource_link_id = %s)',
                           $resourceLink->getContext()->getRecordId(), DataConnector::quoted($resourceLink->getId()));
        } else {
            $sql = sprintf('SELECT r.resource_link_pk, r.context_pk, r.consumer_pk, r.lti_resource_link_id, r.settings, r.primary_resource_link_pk, r.share_approved, r.created, r.updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' r LEFT OUTER JOIN ' .
                           $this->dbTableNamePrefix . DataConnector::CONTEXT_TABLE_NAME . ' c ON r.context_pk = c.context_pk ' .
                           ' WHERE ((r.consumer_pk = %d) OR (c.consumer_pk = %d)) AND (lti_resource_link_id = %s)',
                           $resourceLink->getConsumer()->getRecordId(), $resourceLink->getConsumer()->getRecordId(), DataConnector::quoted($resourceLink->getId()));
        }
        $rsContext = mysql_query($sql);
        if ($rsContext) {
            $row = mysql_fetch_object($rsContext);
            if ($row) {
                $resourceLink->setRecordId(intval($row->resource_link_pk));
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

        if (is_null($resourceLink->shareApproved)) {
            $approved = 'NULL';
        } else if ($resourceLink->shareApproved) {
            $approved = '1';
        } else {
            $approved = '0';
        }
        if (empty($resourceLink->primaryResourceLinkId)) {
            $primaryResourceLinkId = 'NULL';
        } else {
            $primaryResourceLinkId = strval($resourceLink->primaryResourceLinkId);
        }
        $time = time();
        $now = date("{$this->dateFormat} {$this->timeFormat}", $time);
        $settingsValue = serialize($resourceLink->getSettings());
        if (!empty($resourceLink->getContext())) {
            $consumerId = 'NULL';
            $contextId = strval($resourceLink->getContext()->getRecordId());
        } else if (!empty($resourceLink->getContextId())) {
            $consumerId = 'NULL';
            $contextId = strval($resourceLink->getContextId());
        } else {
            $consumerId = strval($resourceLink->getConsumer()->getRecordId());
            $contextId = 'NULL';
        }
        $id = $resourceLink->getRecordId();
        if (empty($id)) {
            $sql = sprintf("INSERT INTO {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' (consumer_pk, context_pk, ' .
                           'lti_resource_link_id, settings, primary_resource_link_pk, share_approved, created, updated) ' .
                           'VALUES (%s, %s, %s, %s, %s, %s, %s, %s)',
                           $consumerId, $contextId, DataConnector::quoted($resourceLink->getId()),
                           DataConnector::quoted($settingsValue),
                           $primaryResourceLinkId, $approved, DataConnector::quoted($now), DataConnector::quoted($now));
        } else if ($contextId !== 'NULL') {
            $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' SET ' .
                           'consumer_pk = %s, lti_resource_link_id = %s, settings = %s, '.
                           'primary_resource_link_pk = %s, share_approved = %s, updated = %s ' .
                           'WHERE (context_pk = %s) AND (resource_link_pk = %d)',
                           $consumerId, DataConnector::quoted($resourceLink->getId()),
                           DataConnector::quoted($settingsValue), $primaryResourceLinkId, $approved, DataConnector::quoted($now),
                           $contextId, $id);
        } else {
            $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' SET ' .
                           'context_pk = %s, lti_resource_link_id = %s, settings = %s, '.
                           'primary_resource_link_pk = %s, share_approved = %s, updated = %s ' .
                           'WHERE (consumer_pk = %s) AND (resource_link_pk = %d)',
                           $contextId, DataConnector::quoted($resourceLink->getId()),
                           DataConnector::quoted($settingsValue), $primaryResourceLinkId, $approved, DataConnector::quoted($now),
                           $consumerId, $id);
        }
        $ok = mysql_query($sql);
        if ($ok) {
            if (empty($id)) {
                $resourceLink->setRecordId(mysql_insert_id());
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
        $ok = mysql_query($sql);

// Delete users
        if ($ok) {
            $sql = sprintf("DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' ' .
                           'WHERE (resource_link_pk = %d)',
                           $resourceLink->getRecordId());
            $ok = mysql_query($sql);
        }

// Update any resource links for which this is the primary resource link
        if ($ok) {
            $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
                           'SET primary_resource_link_pk = NULL ' .
                           'WHERE (primary_resource_link_pk = %d)',
                           $resourceLink->getRecordId());
            $ok = mysql_query($sql);
        }

// Delete resource link
        if ($ok) {
            $sql = sprintf("DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
                           'WHERE (resource_link_pk = %s)',
                           $resourceLink->getRecordId());
            $ok = mysql_query($sql);
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
        $rsUser = mysql_query($sql);
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
        $rsShare = mysql_query($sql);
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
    public function loadConsumerNonce($nonce)
    {

        $ok = true;

// Delete any expired nonce values
        $now = date("{$this->dateFormat} {$this->timeFormat}", time());
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::NONCE_TABLE_NAME . " WHERE expires <= '{$now}'";
        mysql_query($sql);

// Load the nonce
        $sql = sprintf("SELECT value AS T FROM {$this->dbTableNamePrefix}" . DataConnector::NONCE_TABLE_NAME . ' WHERE (consumer_pk = %d) AND (value = %s)',
                       $nonce->getConsumer()->getRecordId(), DataConnector::quoted($nonce->getValue()));
        $rs_nonce = mysql_query($sql);
        if ($rs_nonce) {
            $row = mysql_fetch_object($rs_nonce);
            if ($row === false) {
                $ok = false;
            }
        }

        return $ok;

    }

/**
 * Save nonce object.
 *
 * @param ConsumerNonce $nonce Nonce object
 *
 * @return boolean True if the nonce object was successfully saved
 */
    public function saveConsumerNonce($nonce)
    {

        $expires = date("{$this->dateFormat} {$this->timeFormat}", $nonce->expires);
        $sql = sprintf("INSERT INTO {$this->dbTableNamePrefix}" . DataConnector::NONCE_TABLE_NAME . " (consumer_pk, value, expires) VALUES (%d, %s, %s)",
                       $nonce->getConsumer()->getRecordId(), DataConnector::quoted($nonce->getValue()),
                       DataConnector::quoted($expires));
        $ok = mysql_query($sql);

        return $ok;

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
        mysql_query($sql);

// Load share key
        $id = mysql_real_escape_string($shareKey->getId());
        $sql = 'SELECT resource_link_pk, auto_approve, expires ' .
               "FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' ' .
               "WHERE share_key_id = '{$id}'";
        $rsShareKey = mysql_query($sql);
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
        $ok = mysql_query($sql);

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

        $ok = mysql_query($sql);

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
    public function loadUser($user)
    {

        $ok = false;
        if (!empty($user->getRecordId())) {
            $sql = sprintf('SELECT user_pk, resource_link_pk, lti_user_id, lti_result_sourcedid, created, updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' ' .
                           'WHERE (user_pk = %d)',
            $user->getRecordId());
        } else {
            $sql = sprintf('SELECT user_pk, resource_link_pk, lti_user_id, lti_result_sourcedid, created, updated ' .
                           "FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' ' .
                           'WHERE (resource_link_pk = %d) AND (lti_user_id = %s)',
                           $user->getResourceLink()->getRecordId(),
                           DataConnector::quoted($user->getId(ToolProvider\ToolProvider::ID_SCOPE_ID_ONLY)));
        }
        $rsUser = mysql_query($sql);
        if ($rsUser) {
            $row = mysql_fetch_object($rsUser);
            if ($row) {
                $user->setRecordId(intval($row->user_pk));
                $user->setResourceLinkId(intval($row->resource_link_pk));
                $user->ltiUserId = $row->lti_user_id;
                $user->ltiResultSourcedId = $row->lti_result_sourcedid;
                $user->created = strtotime($row->created);
                $user->updated = strtotime($row->updated);
                $ok = true;
            }
        }

        return $ok;

    }

/**
 * Save user object.
 *
 * @param User $user User object
 *
 * @return boolean True if the user object was successfully saved
 */
    public function saveUser($user)
    {

        $time = time();
        $now = date("{$this->dateFormat} {$this->timeFormat}", $time);
        if (is_null($user->created)) {
            $sql = sprintf("INSERT INTO {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' (resource_link_pk, ' .
                           'lti_user_id, lti_result_sourcedid, created, updated) ' .
                           'VALUES (%d, %s, %s, %s, %s)',
                           $user->getResourceLink()->getRecordId(),
                           DataConnector::quoted($user->getId(ToolProvider\ToolProvider::ID_SCOPE_ID_ONLY)), DataConnector::quoted($user->ltiResultSourcedId),
                           DataConnector::quoted($now), DataConnector::quoted($now));
        } else {
            $sql = sprintf("UPDATE {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' ' .
                           'SET lti_result_sourcedid = %s, updated = %s ' .
                           'WHERE (user_pk = %d)',
                           DataConnector::quoted($user->ltiResultSourcedId),
                           DataConnector::quoted($now),
                           $user->getRecordId());
        }
        $ok = mysql_query($sql);
        if ($ok) {
            if (is_null($user->created)) {
                $user->setRecordId(mysql_insert_id());
                $user->created = $time;
            }
            $user->updated = $time;
        }

        return $ok;

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
        $ok = mysql_query($sql);

        if ($ok) {
            $user->initialize();
        }

        return $ok;

    }

}
