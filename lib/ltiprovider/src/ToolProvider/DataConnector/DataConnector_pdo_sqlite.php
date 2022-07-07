<?php

namespace IMSGlobal\LTI\ToolProvider\DataConnector;

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\Context;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use PDO;

/**
 * Class to represent an LTI Data Connector for PDO variations for SQLite connections
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */


class DataConnector_pdo_sqlite extends DataConnector_pdo
{

###
###  ToolConsumer methods
###

/**
 * Delete tool consumer object.
 *
 * @param ToolConsumer $consumer Consumer object
 *
 * @return boolean True if the tool consumer object was successfully deleted
 */
    public function deleteToolConsumer($consumer)
    {

        $id = $consumer->getRecordId();

// Delete any nonce values for this consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::NONCE_TABLE_NAME . ' WHERE consumer_pk = :id';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete any outstanding share keys for resource links for this consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . '.resource_link_pk = rl.resource_link_pk) AND (rl.consumer_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete any outstanding share keys for resource links for contexts in this consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
               "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . '.resource_link_pk = rl.resource_link_pk) AND (c.consumer_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete any users in resource links for this consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . '.resource_link_pk = rl.resource_link_pk) AND (rl.consumer_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete any users in resource links for contexts in this consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
               "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . '.resource_link_pk = rl.resource_link_pk) AND (c.consumer_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Update any resource links for which this consumer is acting as a primary resource link
        $sql = "UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
               'SET primary_resource_link_pk = NULL, share_approved = NULL ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . '.primary_resource_link_pk = rl.resource_link_pk) AND (rl.consumer_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Update any resource links for contexts in which this consumer is acting as a primary resource link
        $sql = "UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
               'SET primary_resource_link_pk = NULL, share_approved = NULL ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
               "INNER JOIN {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ON rl.context_pk = c.context_pk ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . '.primary_resource_link_pk = rl.resource_link_pk) AND (c.consumer_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete any resource links for this consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
               'WHERE consumer_pk = :id';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete any resource links for contexts in this consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' c ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . '.context_pk = c.context_pk) AND (c.consumer_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete any contexts for this consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' ' .
               'WHERE consumer_pk = :id';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::CONSUMER_TABLE_NAME . ' ' .
               'WHERE consumer_pk = :id';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $ok = $query->execute();

        if ($ok) {
            $consumer->initialize();
        }

        return $ok;

    }

###
###  Context methods
###

/**
 * Delete context object.
 *
 * @param Context $context Context object
 *
 * @return boolean True if the Context object was successfully deleted
 */
    public function deleteContext($context)
    {

        $id = $context->getRecordId();

// Delete any outstanding share keys for resource links for this context
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . '.resource_link_pk = rl.resource_link_pk) AND (rl.context_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete any users in resource links for this context
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . ' ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::USER_RESULT_TABLE_NAME . '.resource_link_pk = rl.resource_link_pk) AND (rl.context_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Update any resource links for which this consumer is acting as a primary resource link
        $sql = "UPDATE {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
               'SET primary_resource_link_pk = null, share_approved = null ' .
               "WHERE EXISTS (SELECT * FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' rl ' .
               "WHERE ({$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . '.primary_resource_link_pk = rl.resource_link_pk) AND (rl.context_pk = :id))';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete any resource links for this consumer
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::RESOURCE_LINK_TABLE_NAME . ' ' .
               'WHERE context_pk = :id';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();

// Delete context
        $sql = "DELETE FROM {$this->dbTableNamePrefix}" . DataConnector::CONTEXT_TABLE_NAME . ' ' .
               'WHERE context_pk = :id';
        $query = $this->db->prepare($sql);
        $query->bindValue('id', $id, PDO::PARAM_INT);
        $ok = $query->execute();

        if ($ok) {
            $context->initialize();
        }

        return $ok;

    }

}
