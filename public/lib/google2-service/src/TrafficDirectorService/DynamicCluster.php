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

namespace Google\Service\TrafficDirectorService;

class DynamicCluster extends \Google\Model
{
  /**
   * Resource status is not available/unknown.
   */
  public const CLIENT_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Client requested this resource but hasn't received any update from
   * management server. The client will not fail requests, but will queue them
   * until update arrives or the client times out waiting for the resource.
   */
  public const CLIENT_STATUS_REQUESTED = 'REQUESTED';
  /**
   * This resource has been requested by the client but has either not been
   * delivered by the server or was previously delivered by the server and then
   * subsequently removed from resources provided by the server. For more
   * information, please refer to the :ref:`"Knowing When a Requested Resource
   * Does Not Exist" ` section.
   */
  public const CLIENT_STATUS_DOES_NOT_EXIST = 'DOES_NOT_EXIST';
  /**
   * Client received this resource and replied with ACK.
   */
  public const CLIENT_STATUS_ACKED = 'ACKED';
  /**
   * Client received this resource and replied with NACK.
   */
  public const CLIENT_STATUS_NACKED = 'NACKED';
  /**
   * Client received an error from the control plane. The attached config dump
   * is the most recent accepted one. If no config is accepted yet, the attached
   * config dump will be empty.
   */
  public const CLIENT_STATUS_RECEIVED_ERROR = 'RECEIVED_ERROR';
  /**
   * Client timed out waiting for the resource from the control plane.
   */
  public const CLIENT_STATUS_TIMEOUT = 'TIMEOUT';
  /**
   * The client status of this resource. [#not-implemented-hide:]
   *
   * @var string
   */
  public $clientStatus;
  /**
   * The cluster config.
   *
   * @var array[]
   */
  public $cluster;
  protected $errorStateType = UpdateFailureState::class;
  protected $errorStateDataType = '';
  /**
   * The timestamp when the Cluster was last updated.
   *
   * @var string
   */
  public $lastUpdated;
  /**
   * This is the per-resource version information. This version is currently
   * taken from the :ref:`version_info ` field at the time that the cluster was
   * loaded. In the future, discrete per-cluster versions may be supported by
   * the API.
   *
   * @var string
   */
  public $versionInfo;

  /**
   * The client status of this resource. [#not-implemented-hide:]
   *
   * Accepted values: UNKNOWN, REQUESTED, DOES_NOT_EXIST, ACKED, NACKED,
   * RECEIVED_ERROR, TIMEOUT
   *
   * @param self::CLIENT_STATUS_* $clientStatus
   */
  public function setClientStatus($clientStatus)
  {
    $this->clientStatus = $clientStatus;
  }
  /**
   * @return self::CLIENT_STATUS_*
   */
  public function getClientStatus()
  {
    return $this->clientStatus;
  }
  /**
   * The cluster config.
   *
   * @param array[] $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return array[]
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Set if the last update failed, cleared after the next successful update.
   * The ``error_state`` field contains the rejected version of this particular
   * resource along with the reason and timestamp. For successfully updated or
   * acknowledged resource, this field should be empty. [#not-implemented-hide:]
   *
   * @param UpdateFailureState $errorState
   */
  public function setErrorState(UpdateFailureState $errorState)
  {
    $this->errorState = $errorState;
  }
  /**
   * @return UpdateFailureState
   */
  public function getErrorState()
  {
    return $this->errorState;
  }
  /**
   * The timestamp when the Cluster was last updated.
   *
   * @param string $lastUpdated
   */
  public function setLastUpdated($lastUpdated)
  {
    $this->lastUpdated = $lastUpdated;
  }
  /**
   * @return string
   */
  public function getLastUpdated()
  {
    return $this->lastUpdated;
  }
  /**
   * This is the per-resource version information. This version is currently
   * taken from the :ref:`version_info ` field at the time that the cluster was
   * loaded. In the future, discrete per-cluster versions may be supported by
   * the API.
   *
   * @param string $versionInfo
   */
  public function setVersionInfo($versionInfo)
  {
    $this->versionInfo = $versionInfo;
  }
  /**
   * @return string
   */
  public function getVersionInfo()
  {
    return $this->versionInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicCluster::class, 'Google_Service_TrafficDirectorService_DynamicCluster');
