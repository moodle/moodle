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

namespace Google\Service\AnalyticsHub;

class Replica extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const PRIMARY_STATE_PRIMARY_STATE_UNSPECIFIED = 'PRIMARY_STATE_UNSPECIFIED';
  /**
   * The replica is the primary replica.
   */
  public const PRIMARY_STATE_PRIMARY_REPLICA = 'PRIMARY_REPLICA';
  /**
   * Default value. This value is unused.
   */
  public const REPLICA_STATE_REPLICA_STATE_UNSPECIFIED = 'REPLICA_STATE_UNSPECIFIED';
  /**
   * The replica is backfilled and ready to use.
   */
  public const REPLICA_STATE_READY_TO_USE = 'READY_TO_USE';
  /**
   * The replica is unavailable, does not exist, or has not been backfilled yet.
   */
  public const REPLICA_STATE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Output only. The geographic location where the replica resides. See
   * [BigQuery locations](https://cloud.google.com/bigquery/docs/locations) for
   * supported locations. Eg. "us-central1".
   *
   * @var string
   */
  public $location;
  /**
   * Output only. Indicates that this replica is the primary replica.
   *
   * @var string
   */
  public $primaryState;
  /**
   * Output only. Assigned by Analytics Hub based on real BigQuery replication
   * state.
   *
   * @var string
   */
  public $replicaState;

  /**
   * Output only. The geographic location where the replica resides. See
   * [BigQuery locations](https://cloud.google.com/bigquery/docs/locations) for
   * supported locations. Eg. "us-central1".
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Output only. Indicates that this replica is the primary replica.
   *
   * Accepted values: PRIMARY_STATE_UNSPECIFIED, PRIMARY_REPLICA
   *
   * @param self::PRIMARY_STATE_* $primaryState
   */
  public function setPrimaryState($primaryState)
  {
    $this->primaryState = $primaryState;
  }
  /**
   * @return self::PRIMARY_STATE_*
   */
  public function getPrimaryState()
  {
    return $this->primaryState;
  }
  /**
   * Output only. Assigned by Analytics Hub based on real BigQuery replication
   * state.
   *
   * Accepted values: REPLICA_STATE_UNSPECIFIED, READY_TO_USE, UNAVAILABLE
   *
   * @param self::REPLICA_STATE_* $replicaState
   */
  public function setReplicaState($replicaState)
  {
    $this->replicaState = $replicaState;
  }
  /**
   * @return self::REPLICA_STATE_*
   */
  public function getReplicaState()
  {
    return $this->replicaState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Replica::class, 'Google_Service_AnalyticsHub_Replica');
