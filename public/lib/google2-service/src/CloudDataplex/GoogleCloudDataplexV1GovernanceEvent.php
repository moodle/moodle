<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1GovernanceEvent extends \Google\Model
{
  /**
   * An unspecified event type.
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * Resource IAM policy update event.
   */
  public const EVENT_TYPE_RESOURCE_IAM_POLICY_UPDATE = 'RESOURCE_IAM_POLICY_UPDATE';
  /**
   * BigQuery table create event.
   */
  public const EVENT_TYPE_BIGQUERY_TABLE_CREATE = 'BIGQUERY_TABLE_CREATE';
  /**
   * BigQuery table update event.
   */
  public const EVENT_TYPE_BIGQUERY_TABLE_UPDATE = 'BIGQUERY_TABLE_UPDATE';
  /**
   * BigQuery table delete event.
   */
  public const EVENT_TYPE_BIGQUERY_TABLE_DELETE = 'BIGQUERY_TABLE_DELETE';
  /**
   * BigQuery connection create event.
   */
  public const EVENT_TYPE_BIGQUERY_CONNECTION_CREATE = 'BIGQUERY_CONNECTION_CREATE';
  /**
   * BigQuery connection update event.
   */
  public const EVENT_TYPE_BIGQUERY_CONNECTION_UPDATE = 'BIGQUERY_CONNECTION_UPDATE';
  /**
   * BigQuery connection delete event.
   */
  public const EVENT_TYPE_BIGQUERY_CONNECTION_DELETE = 'BIGQUERY_CONNECTION_DELETE';
  /**
   * BigQuery taxonomy created.
   */
  public const EVENT_TYPE_BIGQUERY_TAXONOMY_CREATE = 'BIGQUERY_TAXONOMY_CREATE';
  /**
   * BigQuery policy tag created.
   */
  public const EVENT_TYPE_BIGQUERY_POLICY_TAG_CREATE = 'BIGQUERY_POLICY_TAG_CREATE';
  /**
   * BigQuery policy tag deleted.
   */
  public const EVENT_TYPE_BIGQUERY_POLICY_TAG_DELETE = 'BIGQUERY_POLICY_TAG_DELETE';
  /**
   * BigQuery set iam policy for policy tag.
   */
  public const EVENT_TYPE_BIGQUERY_POLICY_TAG_SET_IAM_POLICY = 'BIGQUERY_POLICY_TAG_SET_IAM_POLICY';
  /**
   * Access policy update event.
   */
  public const EVENT_TYPE_ACCESS_POLICY_UPDATE = 'ACCESS_POLICY_UPDATE';
  /**
   * Number of resources matched with particular Query.
   */
  public const EVENT_TYPE_GOVERNANCE_RULE_MATCHED_RESOURCES = 'GOVERNANCE_RULE_MATCHED_RESOURCES';
  /**
   * Rule processing exceeds the allowed limit.
   */
  public const EVENT_TYPE_GOVERNANCE_RULE_SEARCH_LIMIT_EXCEEDS = 'GOVERNANCE_RULE_SEARCH_LIMIT_EXCEEDS';
  /**
   * Rule processing errors.
   */
  public const EVENT_TYPE_GOVERNANCE_RULE_ERRORS = 'GOVERNANCE_RULE_ERRORS';
  /**
   * Governance rule processing Event.
   */
  public const EVENT_TYPE_GOVERNANCE_RULE_PROCESSING = 'GOVERNANCE_RULE_PROCESSING';
  protected $entityType = GoogleCloudDataplexV1GovernanceEventEntity::class;
  protected $entityDataType = '';
  /**
   * The type of the event.
   *
   * @var string
   */
  public $eventType;
  /**
   * The log message.
   *
   * @var string
   */
  public $message;

  /**
   * Entity resource information if the log event is associated with a specific
   * entity.
   *
   * @param GoogleCloudDataplexV1GovernanceEventEntity $entity
   */
  public function setEntity(GoogleCloudDataplexV1GovernanceEventEntity $entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return GoogleCloudDataplexV1GovernanceEventEntity
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * The type of the event.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, RESOURCE_IAM_POLICY_UPDATE,
   * BIGQUERY_TABLE_CREATE, BIGQUERY_TABLE_UPDATE, BIGQUERY_TABLE_DELETE,
   * BIGQUERY_CONNECTION_CREATE, BIGQUERY_CONNECTION_UPDATE,
   * BIGQUERY_CONNECTION_DELETE, BIGQUERY_TAXONOMY_CREATE,
   * BIGQUERY_POLICY_TAG_CREATE, BIGQUERY_POLICY_TAG_DELETE,
   * BIGQUERY_POLICY_TAG_SET_IAM_POLICY, ACCESS_POLICY_UPDATE,
   * GOVERNANCE_RULE_MATCHED_RESOURCES, GOVERNANCE_RULE_SEARCH_LIMIT_EXCEEDS,
   * GOVERNANCE_RULE_ERRORS, GOVERNANCE_RULE_PROCESSING
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * The log message.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1GovernanceEvent::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1GovernanceEvent');
