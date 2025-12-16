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

namespace Google\Service\Bigquery;

class TableReplicationInfo extends \Google\Model
{
  /**
   * Default value.
   */
  public const REPLICATION_STATUS_REPLICATION_STATUS_UNSPECIFIED = 'REPLICATION_STATUS_UNSPECIFIED';
  /**
   * Replication is Active with no errors.
   */
  public const REPLICATION_STATUS_ACTIVE = 'ACTIVE';
  /**
   * Source object is deleted.
   */
  public const REPLICATION_STATUS_SOURCE_DELETED = 'SOURCE_DELETED';
  /**
   * Source revoked replication permissions.
   */
  public const REPLICATION_STATUS_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * Source configuration doesn’t allow replication.
   */
  public const REPLICATION_STATUS_UNSUPPORTED_CONFIGURATION = 'UNSUPPORTED_CONFIGURATION';
  /**
   * Optional. Output only. If source is a materialized view, this field
   * signifies the last refresh time of the source.
   *
   * @var string
   */
  public $replicatedSourceLastRefreshTime;
  protected $replicationErrorType = ErrorProto::class;
  protected $replicationErrorDataType = '';
  /**
   * Optional. Specifies the interval at which the source table is polled for
   * updates. It's Optional. If not specified, default replication interval
   * would be applied.
   *
   * @var string
   */
  public $replicationIntervalMs;
  /**
   * Optional. Output only. Replication status of configured replication.
   *
   * @var string
   */
  public $replicationStatus;
  protected $sourceTableType = TableReference::class;
  protected $sourceTableDataType = '';

  /**
   * Optional. Output only. If source is a materialized view, this field
   * signifies the last refresh time of the source.
   *
   * @param string $replicatedSourceLastRefreshTime
   */
  public function setReplicatedSourceLastRefreshTime($replicatedSourceLastRefreshTime)
  {
    $this->replicatedSourceLastRefreshTime = $replicatedSourceLastRefreshTime;
  }
  /**
   * @return string
   */
  public function getReplicatedSourceLastRefreshTime()
  {
    return $this->replicatedSourceLastRefreshTime;
  }
  /**
   * Optional. Output only. Replication error that will permanently stopped
   * table replication.
   *
   * @param ErrorProto $replicationError
   */
  public function setReplicationError(ErrorProto $replicationError)
  {
    $this->replicationError = $replicationError;
  }
  /**
   * @return ErrorProto
   */
  public function getReplicationError()
  {
    return $this->replicationError;
  }
  /**
   * Optional. Specifies the interval at which the source table is polled for
   * updates. It's Optional. If not specified, default replication interval
   * would be applied.
   *
   * @param string $replicationIntervalMs
   */
  public function setReplicationIntervalMs($replicationIntervalMs)
  {
    $this->replicationIntervalMs = $replicationIntervalMs;
  }
  /**
   * @return string
   */
  public function getReplicationIntervalMs()
  {
    return $this->replicationIntervalMs;
  }
  /**
   * Optional. Output only. Replication status of configured replication.
   *
   * Accepted values: REPLICATION_STATUS_UNSPECIFIED, ACTIVE, SOURCE_DELETED,
   * PERMISSION_DENIED, UNSUPPORTED_CONFIGURATION
   *
   * @param self::REPLICATION_STATUS_* $replicationStatus
   */
  public function setReplicationStatus($replicationStatus)
  {
    $this->replicationStatus = $replicationStatus;
  }
  /**
   * @return self::REPLICATION_STATUS_*
   */
  public function getReplicationStatus()
  {
    return $this->replicationStatus;
  }
  /**
   * Required. Source table reference that is replicated.
   *
   * @param TableReference $sourceTable
   */
  public function setSourceTable(TableReference $sourceTable)
  {
    $this->sourceTable = $sourceTable;
  }
  /**
   * @return TableReference
   */
  public function getSourceTable()
  {
    return $this->sourceTable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableReplicationInfo::class, 'Google_Service_Bigquery_TableReplicationInfo');
