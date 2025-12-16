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

namespace Google\Service\NetworkSecurity;

class MirroringEndpointGroupAssociationLocationDetails extends \Google\Model
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The association is ready and in sync with the linked endpoint group.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The association is out of sync with the linked endpoint group. In most
   * cases, this is a result of a transient issue within the system (e.g. an
   * inaccessible location) and the system is expected to recover automatically.
   */
  public const STATE_OUT_OF_SYNC = 'OUT_OF_SYNC';
  /**
   * Output only. The cloud location, e.g. "us-central1-a" or "asia-south1".
   *
   * @var string
   */
  public $location;
  /**
   * Output only. The current state of the association in this location.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The cloud location, e.g. "us-central1-a" or "asia-south1".
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
   * Output only. The current state of the association in this location.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, OUT_OF_SYNC
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
class_alias(MirroringEndpointGroupAssociationLocationDetails::class, 'Google_Service_NetworkSecurity_MirroringEndpointGroupAssociationLocationDetails');
