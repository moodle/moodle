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

namespace Google\Service\Dataproc;

class ClusterStatus extends \Google\Model
{
  /**
   * The cluster state is unknown.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * The cluster is being created and set up. It is not ready for use.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The cluster is currently running and healthy. It is ready for use.Note: The
   * cluster state changes from "creating" to "running" status after the master
   * node(s), first two primary worker nodes (and the last primary worker node
   * if primary workers > 2) are running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The cluster encountered an error. It is not ready for use.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The cluster has encountered an error while being updated. Jobs can be
   * submitted to the cluster, but the cluster cannot be updated.
   */
  public const STATE_ERROR_DUE_TO_UPDATE = 'ERROR_DUE_TO_UPDATE';
  /**
   * The cluster is being deleted. It cannot be used.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The cluster is being updated. It continues to accept and process jobs.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The cluster is being stopped. It cannot be used.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * The cluster is currently stopped. It is not ready for use.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * The cluster is being started. It is not ready for use.
   */
  public const STATE_STARTING = 'STARTING';
  /**
   * The cluster is being repaired. It is not ready for use.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  /**
   * Cluster creation is currently waiting for resources to be available. Once
   * all resources are available, it will transition to CREATING and then
   * RUNNING.
   */
  public const STATE_SCHEDULED = 'SCHEDULED';
  /**
   * The cluster substate is unknown.
   */
  public const SUBSTATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The cluster is known to be in an unhealthy state (for example, critical
   * daemons are not running or HDFS capacity is exhausted).Applies to RUNNING
   * state.
   */
  public const SUBSTATE_UNHEALTHY = 'UNHEALTHY';
  /**
   * The agent-reported status is out of date (may occur if Dataproc loses
   * communication with Agent).Applies to RUNNING state.
   */
  public const SUBSTATE_STALE_STATUS = 'STALE_STATUS';
  /**
   * Optional. Output only. Details of cluster's state.
   *
   * @var string
   */
  public $detail;
  /**
   * Output only. The cluster's state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Time when this state was entered (see JSON representation of
   * Timestamp (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @var string
   */
  public $stateStartTime;
  /**
   * Output only. Additional state information that includes status reported by
   * the agent.
   *
   * @var string
   */
  public $substate;

  /**
   * Optional. Output only. Details of cluster's state.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Output only. The cluster's state.
   *
   * Accepted values: UNKNOWN, CREATING, RUNNING, ERROR, ERROR_DUE_TO_UPDATE,
   * DELETING, UPDATING, STOPPING, STOPPED, STARTING, REPAIRING, SCHEDULED
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
   * Output only. Time when this state was entered (see JSON representation of
   * Timestamp (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).
   *
   * @param string $stateStartTime
   */
  public function setStateStartTime($stateStartTime)
  {
    $this->stateStartTime = $stateStartTime;
  }
  /**
   * @return string
   */
  public function getStateStartTime()
  {
    return $this->stateStartTime;
  }
  /**
   * Output only. Additional state information that includes status reported by
   * the agent.
   *
   * Accepted values: UNSPECIFIED, UNHEALTHY, STALE_STATUS
   *
   * @param self::SUBSTATE_* $substate
   */
  public function setSubstate($substate)
  {
    $this->substate = $substate;
  }
  /**
   * @return self::SUBSTATE_*
   */
  public function getSubstate()
  {
    return $this->substate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterStatus::class, 'Google_Service_Dataproc_ClusterStatus');
