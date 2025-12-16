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

namespace Google\Service\WorkloadManager;

class SqlserverValidationValidationDetail extends \Google\Collection
{
  /**
   * Unspecified type.
   */
  public const TYPE_SQLSERVER_VALIDATION_TYPE_UNSPECIFIED = 'SQLSERVER_VALIDATION_TYPE_UNSPECIFIED';
  /**
   * The Sqlserver system named OS.
   */
  public const TYPE_OS = 'OS';
  /**
   * The LOG_DISK_SEPARATION table.
   */
  public const TYPE_DB_LOG_DISK_SEPARATION = 'DB_LOG_DISK_SEPARATION';
  /**
   * The MAX_PARALLELISM table.
   */
  public const TYPE_DB_MAX_PARALLELISM = 'DB_MAX_PARALLELISM';
  /**
   * The CXPACKET_WAITS table.
   */
  public const TYPE_DB_CXPACKET_WAITS = 'DB_CXPACKET_WAITS';
  /**
   * The TRANSACTION_LOG_HANDLING table.
   */
  public const TYPE_DB_TRANSACTION_LOG_HANDLING = 'DB_TRANSACTION_LOG_HANDLING';
  /**
   * The VIRTUAL_LOG_FILE_COUNT table.
   */
  public const TYPE_DB_VIRTUAL_LOG_FILE_COUNT = 'DB_VIRTUAL_LOG_FILE_COUNT';
  /**
   * The BUFFER_POOL_EXTENSION table.
   */
  public const TYPE_DB_BUFFER_POOL_EXTENSION = 'DB_BUFFER_POOL_EXTENSION';
  /**
   * The MAX_SERVER_MEMORY table.
   */
  public const TYPE_DB_MAX_SERVER_MEMORY = 'DB_MAX_SERVER_MEMORY';
  /**
   * The INSTANCE_METRICS table.
   */
  public const TYPE_INSTANCE_METRICS = 'INSTANCE_METRICS';
  /**
   * The DB_INDEX_FRAGMENTATION table.
   */
  public const TYPE_DB_INDEX_FRAGMENTATION = 'DB_INDEX_FRAGMENTATION';
  /**
   * The DB_TABLE_INDEX_COMPRESSION table.
   */
  public const TYPE_DB_TABLE_INDEX_COMPRESSION = 'DB_TABLE_INDEX_COMPRESSION';
  /**
   * The DB_BACKUP_POLICY table.
   */
  public const TYPE_DB_BACKUP_POLICY = 'DB_BACKUP_POLICY';
  protected $collection_key = 'details';
  protected $detailsType = SqlserverValidationDetails::class;
  protected $detailsDataType = 'array';
  /**
   * Optional. The Sqlserver system that the validation data is from.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Details wraps map that represents collected data names and
   * values.
   *
   * @param SqlserverValidationDetails[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return SqlserverValidationDetails[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Optional. The Sqlserver system that the validation data is from.
   *
   * Accepted values: SQLSERVER_VALIDATION_TYPE_UNSPECIFIED, OS,
   * DB_LOG_DISK_SEPARATION, DB_MAX_PARALLELISM, DB_CXPACKET_WAITS,
   * DB_TRANSACTION_LOG_HANDLING, DB_VIRTUAL_LOG_FILE_COUNT,
   * DB_BUFFER_POOL_EXTENSION, DB_MAX_SERVER_MEMORY, INSTANCE_METRICS,
   * DB_INDEX_FRAGMENTATION, DB_TABLE_INDEX_COMPRESSION, DB_BACKUP_POLICY
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlserverValidationValidationDetail::class, 'Google_Service_WorkloadManager_SqlserverValidationValidationDetail');
