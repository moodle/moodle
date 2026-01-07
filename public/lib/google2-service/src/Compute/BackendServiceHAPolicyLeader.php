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

namespace Google\Service\Compute;

class BackendServiceHAPolicyLeader extends \Google\Model
{
  /**
   * A fully-qualified URL (starting with https://www.googleapis.com/) of the
   * zonal Network Endpoint Group (NEG) with `GCE_VM_IP` endpoints that the
   * leader is attached to.
   *
   * The leader's backendGroup must already be specified as a backend of this
   * backend service. Removing a backend that is designated as the leader's
   * backendGroup is not permitted.
   *
   * @var string
   */
  public $backendGroup;
  protected $networkEndpointType = BackendServiceHAPolicyLeaderNetworkEndpoint::class;
  protected $networkEndpointDataType = '';

  /**
   * A fully-qualified URL (starting with https://www.googleapis.com/) of the
   * zonal Network Endpoint Group (NEG) with `GCE_VM_IP` endpoints that the
   * leader is attached to.
   *
   * The leader's backendGroup must already be specified as a backend of this
   * backend service. Removing a backend that is designated as the leader's
   * backendGroup is not permitted.
   *
   * @param string $backendGroup
   */
  public function setBackendGroup($backendGroup)
  {
    $this->backendGroup = $backendGroup;
  }
  /**
   * @return string
   */
  public function getBackendGroup()
  {
    return $this->backendGroup;
  }
  /**
   * The network endpoint within the leader.backendGroup that is designated as
   * the leader.
   *
   * This network endpoint cannot be detached from the NEG specified in the
   * haPolicy.leader.backendGroup until the leader is updated with another
   * network endpoint, or the leader is removed from the haPolicy.
   *
   * @param BackendServiceHAPolicyLeaderNetworkEndpoint $networkEndpoint
   */
  public function setNetworkEndpoint(BackendServiceHAPolicyLeaderNetworkEndpoint $networkEndpoint)
  {
    $this->networkEndpoint = $networkEndpoint;
  }
  /**
   * @return BackendServiceHAPolicyLeaderNetworkEndpoint
   */
  public function getNetworkEndpoint()
  {
    return $this->networkEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendServiceHAPolicyLeader::class, 'Google_Service_Compute_BackendServiceHAPolicyLeader');
