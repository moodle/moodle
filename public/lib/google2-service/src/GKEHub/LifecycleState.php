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

class LifecycleState extends \Google\Model
{
  /**
   * State is unknown or not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The MembershipFeature is being enabled, and the MembershipFeature resource
   * is being created. Once complete, the corresponding MembershipFeature will
   * be enabled in this Hub.
   */
  public const STATE_ENABLING = 'ENABLING';
  /**
   * The MembershipFeature is enabled in this Hub, and the MembershipFeature
   * resource is fully available.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The MembershipFeature is being disabled in this Hub, and the
   * MembershipFeature resource is being deleted.
   */
  public const STATE_DISABLING = 'DISABLING';
  /**
   * The MembershipFeature resource is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The MembershipFeature resource is being updated by the Hub Service.
   */
  public const STATE_SERVICE_UPDATING = 'SERVICE_UPDATING';
  /**
   * Output only. The current state of the Feature resource in the Hub API.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The current state of the Feature resource in the Hub API.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLING, ACTIVE, DISABLING, UPDATING,
   * SERVICE_UPDATING
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
class_alias(LifecycleState::class, 'Google_Service_GKEHub_LifecycleState');
