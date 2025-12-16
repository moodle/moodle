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

namespace Google\Service\BigtableAdmin;

class ClusterState extends \Google\Collection
{
  /**
   * The replication state of the table is unknown in this cluster.
   */
  public const REPLICATION_STATE_STATE_NOT_KNOWN = 'STATE_NOT_KNOWN';
  /**
   * The cluster was recently created, and the table must finish copying over
   * pre-existing data from other clusters before it can begin receiving live
   * replication updates and serving Data API requests.
   */
  public const REPLICATION_STATE_INITIALIZING = 'INITIALIZING';
  /**
   * The table is temporarily unable to serve Data API requests from this
   * cluster due to planned internal maintenance.
   */
  public const REPLICATION_STATE_PLANNED_MAINTENANCE = 'PLANNED_MAINTENANCE';
  /**
   * The table is temporarily unable to serve Data API requests from this
   * cluster due to unplanned or emergency maintenance.
   */
  public const REPLICATION_STATE_UNPLANNED_MAINTENANCE = 'UNPLANNED_MAINTENANCE';
  /**
   * The table can serve Data API requests from this cluster. Depending on
   * replication delay, reads may not immediately reflect the state of the table
   * in other clusters.
   */
  public const REPLICATION_STATE_READY = 'READY';
  /**
   * The table is fully created and ready for use after a restore, and is being
   * optimized for performance. When optimizations are complete, the table will
   * transition to `READY` state.
   */
  public const REPLICATION_STATE_READY_OPTIMIZING = 'READY_OPTIMIZING';
  protected $collection_key = 'encryptionInfo';
  protected $encryptionInfoType = EncryptionInfo::class;
  protected $encryptionInfoDataType = 'array';
  /**
   * Output only. The state of replication for the table in this cluster.
   *
   * @var string
   */
  public $replicationState;

  /**
   * Output only. The encryption information for the table in this cluster. If
   * the encryption key protecting this resource is customer managed, then its
   * version can be rotated in Cloud Key Management Service (Cloud KMS). The
   * primary version of the key and its status will be reflected here when
   * changes propagate from Cloud KMS.
   *
   * @param EncryptionInfo[] $encryptionInfo
   */
  public function setEncryptionInfo($encryptionInfo)
  {
    $this->encryptionInfo = $encryptionInfo;
  }
  /**
   * @return EncryptionInfo[]
   */
  public function getEncryptionInfo()
  {
    return $this->encryptionInfo;
  }
  /**
   * Output only. The state of replication for the table in this cluster.
   *
   * Accepted values: STATE_NOT_KNOWN, INITIALIZING, PLANNED_MAINTENANCE,
   * UNPLANNED_MAINTENANCE, READY, READY_OPTIMIZING
   *
   * @param self::REPLICATION_STATE_* $replicationState
   */
  public function setReplicationState($replicationState)
  {
    $this->replicationState = $replicationState;
  }
  /**
   * @return self::REPLICATION_STATE_*
   */
  public function getReplicationState()
  {
    return $this->replicationState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterState::class, 'Google_Service_BigtableAdmin_ClusterState');
