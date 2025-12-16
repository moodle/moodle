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

class GoogleBigtableAdminV2MaterializedViewClusterState extends \Google\Model
{
  /**
   * The state of the materialized view is unknown in this cluster.
   */
  public const REPLICATION_STATE_STATE_NOT_KNOWN = 'STATE_NOT_KNOWN';
  /**
   * The cluster or view was recently created, and the materialized view must
   * finish backfilling before it can begin serving Data API requests.
   */
  public const REPLICATION_STATE_INITIALIZING = 'INITIALIZING';
  /**
   * The materialized view can serve Data API requests from this cluster.
   * Depending on materialization and replication delay, reads may not
   * immediately reflect the state of the materialized view in other clusters.
   */
  public const REPLICATION_STATE_READY = 'READY';
  /**
   * Output only. The state of the materialized view in this cluster.
   *
   * @var string
   */
  public $replicationState;

  /**
   * Output only. The state of the materialized view in this cluster.
   *
   * Accepted values: STATE_NOT_KNOWN, INITIALIZING, READY
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
class_alias(GoogleBigtableAdminV2MaterializedViewClusterState::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2MaterializedViewClusterState');
