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

namespace Google\Service\Container;

class PodAutoscaling extends \Google\Model
{
  /**
   * HPA_PROFILE_UNSPECIFIED is used when no custom HPA profile is set.
   */
  public const HPA_PROFILE_HPA_PROFILE_UNSPECIFIED = 'HPA_PROFILE_UNSPECIFIED';
  /**
   * Customers explicitly opt-out of HPA profiles.
   */
  public const HPA_PROFILE_NONE = 'NONE';
  /**
   * PERFORMANCE is used when customers opt-in to the performance HPA profile.
   * In this profile we support a higher number of HPAs per cluster and faster
   * metrics collection for workload autoscaling.
   */
  public const HPA_PROFILE_PERFORMANCE = 'PERFORMANCE';
  /**
   * Selected Horizontal Pod Autoscaling profile.
   *
   * @var string
   */
  public $hpaProfile;

  /**
   * Selected Horizontal Pod Autoscaling profile.
   *
   * Accepted values: HPA_PROFILE_UNSPECIFIED, NONE, PERFORMANCE
   *
   * @param self::HPA_PROFILE_* $hpaProfile
   */
  public function setHpaProfile($hpaProfile)
  {
    $this->hpaProfile = $hpaProfile;
  }
  /**
   * @return self::HPA_PROFILE_*
   */
  public function getHpaProfile()
  {
    return $this->hpaProfile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PodAutoscaling::class, 'Google_Service_Container_PodAutoscaling');
