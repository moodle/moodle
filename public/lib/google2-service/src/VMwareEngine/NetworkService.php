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

namespace Google\Service\VMwareEngine;

class NetworkService extends \Google\Model
{
  /**
   * Unspecified service state. This is the default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Service is not provisioned.
   */
  public const STATE_UNPROVISIONED = 'UNPROVISIONED';
  /**
   * Service is in the process of being provisioned/deprovisioned.
   */
  public const STATE_RECONCILING = 'RECONCILING';
  /**
   * Service is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * True if the service is enabled; false otherwise.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Output only. State of the service. New values may be added to this enum
   * when appropriate.
   *
   * @var string
   */
  public $state;

  /**
   * True if the service is enabled; false otherwise.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Output only. State of the service. New values may be added to this enum
   * when appropriate.
   *
   * Accepted values: STATE_UNSPECIFIED, UNPROVISIONED, RECONCILING, ACTIVE
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
class_alias(NetworkService::class, 'Google_Service_VMwareEngine_NetworkService');
