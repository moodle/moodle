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

class GoogleCloudDataplexV1DiscoveryEvent extends \Google\Model
{
  /**
   * An unspecified event type.
   */
  public const TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * An event representing discovery configuration in effect.
   */
  public const TYPE_CONFIG = 'CONFIG';
  /**
   * An event representing a metadata entity being created.
   */
  public const TYPE_ENTITY_CREATED = 'ENTITY_CREATED';
  /**
   * An event representing a metadata entity being updated.
   */
  public const TYPE_ENTITY_UPDATED = 'ENTITY_UPDATED';
  /**
   * An event representing a metadata entity being deleted.
   */
  public const TYPE_ENTITY_DELETED = 'ENTITY_DELETED';
  /**
   * An event representing a partition being created.
   */
  public const TYPE_PARTITION_CREATED = 'PARTITION_CREATED';
  /**
   * An event representing a partition being updated.
   */
  public const TYPE_PARTITION_UPDATED = 'PARTITION_UPDATED';
  /**
   * An event representing a partition being deleted.
   */
  public const TYPE_PARTITION_DELETED = 'PARTITION_DELETED';
  /**
   * An event representing a table being published.
   */
  public const TYPE_TABLE_PUBLISHED = 'TABLE_PUBLISHED';
  /**
   * An event representing a table being updated.
   */
  public const TYPE_TABLE_UPDATED = 'TABLE_UPDATED';
  /**
   * An event representing a table being skipped in publishing.
   */
  public const TYPE_TABLE_IGNORED = 'TABLE_IGNORED';
  /**
   * An event representing a table being deleted.
   */
  public const TYPE_TABLE_DELETED = 'TABLE_DELETED';
  protected $actionType = GoogleCloudDataplexV1DiscoveryEventActionDetails::class;
  protected $actionDataType = '';
  /**
   * The id of the associated asset.
   *
   * @var string
   */
  public $assetId;
  protected $configType = GoogleCloudDataplexV1DiscoveryEventConfigDetails::class;
  protected $configDataType = '';
  /**
   * The data location associated with the event.
   *
   * @var string
   */
  public $dataLocation;
  /**
   * The id of the associated datascan for standalone discovery.
   *
   * @var string
   */
  public $datascanId;
  protected $entityType = GoogleCloudDataplexV1DiscoveryEventEntityDetails::class;
  protected $entityDataType = '';
  /**
   * The id of the associated lake.
   *
   * @var string
   */
  public $lakeId;
  /**
   * The log message.
   *
   * @var string
   */
  public $message;
  protected $partitionType = GoogleCloudDataplexV1DiscoveryEventPartitionDetails::class;
  protected $partitionDataType = '';
  protected $tableType = GoogleCloudDataplexV1DiscoveryEventTableDetails::class;
  protected $tableDataType = '';
  /**
   * The type of the event being logged.
   *
   * @var string
   */
  public $type;
  /**
   * The id of the associated zone.
   *
   * @var string
   */
  public $zoneId;

  /**
   * Details about the action associated with the event.
   *
   * @param GoogleCloudDataplexV1DiscoveryEventActionDetails $action
   */
  public function setAction(GoogleCloudDataplexV1DiscoveryEventActionDetails $action)
  {
    $this->action = $action;
  }
  /**
   * @return GoogleCloudDataplexV1DiscoveryEventActionDetails
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * The id of the associated asset.
   *
   * @param string $assetId
   */
  public function setAssetId($assetId)
  {
    $this->assetId = $assetId;
  }
  /**
   * @return string
   */
  public function getAssetId()
  {
    return $this->assetId;
  }
  /**
   * Details about discovery configuration in effect.
   *
   * @param GoogleCloudDataplexV1DiscoveryEventConfigDetails $config
   */
  public function setConfig(GoogleCloudDataplexV1DiscoveryEventConfigDetails $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleCloudDataplexV1DiscoveryEventConfigDetails
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * The data location associated with the event.
   *
   * @param string $dataLocation
   */
  public function setDataLocation($dataLocation)
  {
    $this->dataLocation = $dataLocation;
  }
  /**
   * @return string
   */
  public function getDataLocation()
  {
    return $this->dataLocation;
  }
  /**
   * The id of the associated datascan for standalone discovery.
   *
   * @param string $datascanId
   */
  public function setDatascanId($datascanId)
  {
    $this->datascanId = $datascanId;
  }
  /**
   * @return string
   */
  public function getDatascanId()
  {
    return $this->datascanId;
  }
  /**
   * Details about the entity associated with the event.
   *
   * @param GoogleCloudDataplexV1DiscoveryEventEntityDetails $entity
   */
  public function setEntity(GoogleCloudDataplexV1DiscoveryEventEntityDetails $entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return GoogleCloudDataplexV1DiscoveryEventEntityDetails
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * The id of the associated lake.
   *
   * @param string $lakeId
   */
  public function setLakeId($lakeId)
  {
    $this->lakeId = $lakeId;
  }
  /**
   * @return string
   */
  public function getLakeId()
  {
    return $this->lakeId;
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
  /**
   * Details about the partition associated with the event.
   *
   * @param GoogleCloudDataplexV1DiscoveryEventPartitionDetails $partition
   */
  public function setPartition(GoogleCloudDataplexV1DiscoveryEventPartitionDetails $partition)
  {
    $this->partition = $partition;
  }
  /**
   * @return GoogleCloudDataplexV1DiscoveryEventPartitionDetails
   */
  public function getPartition()
  {
    return $this->partition;
  }
  /**
   * Details about the BigQuery table publishing associated with the event.
   *
   * @param GoogleCloudDataplexV1DiscoveryEventTableDetails $table
   */
  public function setTable(GoogleCloudDataplexV1DiscoveryEventTableDetails $table)
  {
    $this->table = $table;
  }
  /**
   * @return GoogleCloudDataplexV1DiscoveryEventTableDetails
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * The type of the event being logged.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, CONFIG, ENTITY_CREATED,
   * ENTITY_UPDATED, ENTITY_DELETED, PARTITION_CREATED, PARTITION_UPDATED,
   * PARTITION_DELETED, TABLE_PUBLISHED, TABLE_UPDATED, TABLE_IGNORED,
   * TABLE_DELETED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The id of the associated zone.
   *
   * @param string $zoneId
   */
  public function setZoneId($zoneId)
  {
    $this->zoneId = $zoneId;
  }
  /**
   * @return string
   */
  public function getZoneId()
  {
    return $this->zoneId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DiscoveryEvent::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DiscoveryEvent');
