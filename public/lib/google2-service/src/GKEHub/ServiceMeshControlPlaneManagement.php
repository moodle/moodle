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

namespace Google\Service\GKEHub;

class ServiceMeshControlPlaneManagement extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const IMPLEMENTATION_IMPLEMENTATION_UNSPECIFIED = 'IMPLEMENTATION_UNSPECIFIED';
  /**
   * A Google build of istiod is used for the managed control plane.
   */
  public const IMPLEMENTATION_ISTIOD = 'ISTIOD';
  /**
   * Traffic director is used for the managed control plane.
   */
  public const IMPLEMENTATION_TRAFFIC_DIRECTOR = 'TRAFFIC_DIRECTOR';
  /**
   * The control plane implementation is being updated.
   */
  public const IMPLEMENTATION_UPDATING = 'UPDATING';
  /**
   * Unspecified
   */
  public const STATE_LIFECYCLE_STATE_UNSPECIFIED = 'LIFECYCLE_STATE_UNSPECIFIED';
  /**
   * DISABLED means that the component is not enabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * FAILED_PRECONDITION means that provisioning cannot proceed because of some
   * characteristic of the member cluster.
   */
  public const STATE_FAILED_PRECONDITION = 'FAILED_PRECONDITION';
  /**
   * PROVISIONING means that provisioning is in progress.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * ACTIVE means that the component is ready for use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * STALLED means that provisioning could not be done.
   */
  public const STATE_STALLED = 'STALLED';
  /**
   * NEEDS_ATTENTION means that the component is ready, but some user
   * intervention is required. (For example that the user should migrate
   * workloads to a new control plane revision.)
   */
  public const STATE_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * DEGRADED means that the component is ready, but operating in a degraded
   * state.
   */
  public const STATE_DEGRADED = 'DEGRADED';
  /**
   * DEPROVISIONING means that deprovisioning is in progress.
   */
  public const STATE_DEPROVISIONING = 'DEPROVISIONING';
  protected $collection_key = 'details';
  protected $detailsType = ServiceMeshStatusDetails::class;
  protected $detailsDataType = 'array';
  /**
   * Output only. Implementation of managed control plane.
   *
   * @var string
   */
  public $implementation;
  /**
   * LifecycleState of control plane management.
   *
   * @var string
   */
  public $state;

  /**
   * Explanation of state.
   *
   * @param ServiceMeshStatusDetails[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return ServiceMeshStatusDetails[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Output only. Implementation of managed control plane.
   *
   * Accepted values: IMPLEMENTATION_UNSPECIFIED, ISTIOD, TRAFFIC_DIRECTOR,
   * UPDATING
   *
   * @param self::IMPLEMENTATION_* $implementation
   */
  public function setImplementation($implementation)
  {
    $this->implementation = $implementation;
  }
  /**
   * @return self::IMPLEMENTATION_*
   */
  public function getImplementation()
  {
    return $this->implementation;
  }
  /**
   * LifecycleState of control plane management.
   *
   * Accepted values: LIFECYCLE_STATE_UNSPECIFIED, DISABLED,
   * FAILED_PRECONDITION, PROVISIONING, ACTIVE, STALLED, NEEDS_ATTENTION,
   * DEGRADED, DEPROVISIONING
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceMeshControlPlaneManagement::class, 'Google_Service_GKEHub_ServiceMeshControlPlaneManagement');
