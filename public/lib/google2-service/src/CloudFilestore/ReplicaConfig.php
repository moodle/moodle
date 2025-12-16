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

namespace Google\Service\CloudFilestore;

class ReplicaConfig extends \Google\Collection
{
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The replica is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The replica is ready.
   */
  public const STATE_READY = 'READY';
  /**
   * The replica is being removed.
   */
  public const STATE_REMOVING = 'REMOVING';
  /**
   * The replica is experiencing an issue and might be unusable. You can get
   * further details from the `stateReasons` field of the `ReplicaConfig`
   * object.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The replica is being promoted.
   */
  public const STATE_PROMOTING = 'PROMOTING';
  /**
   * The replica is being paused.
   */
  public const STATE_PAUSING = 'PAUSING';
  /**
   * The replica is paused.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The replica is being resumed.
   */
  public const STATE_RESUMING = 'RESUMING';
  protected $collection_key = 'stateReasons';
  /**
   * Output only. The timestamp of the latest replication snapshot taken on the
   * active instance and is already replicated safely.
   *
   * @var string
   */
  public $lastActiveSyncTime;
  /**
   * Optional. The name of the source instance for the replica, in the format
   * `projects/{project}/locations/{location}/instances/{instance}`. This field
   * is required when creating a replica.
   *
   * @var string
   */
  public $peerInstance;
  /**
   * Output only. The replica state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Additional information about the replication state, if
   * available.
   *
   * @var string[]
   */
  public $stateReasons;
  /**
   * Output only. The time when the replica state was updated.
   *
   * @var string
   */
  public $stateUpdateTime;

  /**
   * Output only. The timestamp of the latest replication snapshot taken on the
   * active instance and is already replicated safely.
   *
   * @param string $lastActiveSyncTime
   */
  public function setLastActiveSyncTime($lastActiveSyncTime)
  {
    $this->lastActiveSyncTime = $lastActiveSyncTime;
  }
  /**
   * @return string
   */
  public function getLastActiveSyncTime()
  {
    return $this->lastActiveSyncTime;
  }
  /**
   * Optional. The name of the source instance for the replica, in the format
   * `projects/{project}/locations/{location}/instances/{instance}`. This field
   * is required when creating a replica.
   *
   * @param string $peerInstance
   */
  public function setPeerInstance($peerInstance)
  {
    $this->peerInstance = $peerInstance;
  }
  /**
   * @return string
   */
  public function getPeerInstance()
  {
    return $this->peerInstance;
  }
  /**
   * Output only. The replica state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, REMOVING, FAILED,
   * PROMOTING, PAUSING, PAUSED, RESUMING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Additional information about the replication state, if
   * available.
   *
   * @param string[] $stateReasons
   */
  public function setStateReasons($stateReasons)
  {
    $this->stateReasons = $stateReasons;
  }
  /**
   * @return string[]
   */
  public function getStateReasons()
  {
    return $this->stateReasons;
  }
  /**
   * Output only. The time when the replica state was updated.
   *
   * @param string $stateUpdateTime
   */
  public function setStateUpdateTime($stateUpdateTime)
  {
    $this->stateUpdateTime = $stateUpdateTime;
  }
  /**
   * @return string
   */
  public function getStateUpdateTime()
  {
    return $this->stateUpdateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicaConfig::class, 'Google_Service_CloudFilestore_ReplicaConfig');
