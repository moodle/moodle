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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV1beta1RunAssetDiscoveryResponse extends \Google\Model
{
  /**
   * Asset discovery run state was unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Asset discovery run completed successfully.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * Asset discovery run was cancelled with tasks still pending, as another run
   * for the same organization was started with a higher priority.
   */
  public const STATE_SUPERSEDED = 'SUPERSEDED';
  /**
   * Asset discovery run was killed and terminated.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * The duration between asset discovery run start and end
   *
   * @var string
   */
  public $duration;
  /**
   * The state of an asset discovery run.
   *
   * @var string
   */
  public $state;

  /**
   * The duration between asset discovery run start and end
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * The state of an asset discovery run.
   *
   * Accepted values: STATE_UNSPECIFIED, COMPLETED, SUPERSEDED, TERMINATED
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
class_alias(GoogleCloudSecuritycenterV1beta1RunAssetDiscoveryResponse::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV1beta1RunAssetDiscoveryResponse');
