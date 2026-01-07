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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2ServiceScaling extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const SCALING_MODE_SCALING_MODE_UNSPECIFIED = 'SCALING_MODE_UNSPECIFIED';
  /**
   * Scale based on traffic between min and max instances.
   */
  public const SCALING_MODE_AUTOMATIC = 'AUTOMATIC';
  /**
   * Scale to exactly min instances and ignore max instances.
   */
  public const SCALING_MODE_MANUAL = 'MANUAL';
  /**
   * Optional. total instance count for the service in manual scaling mode. This
   * number of instances is divided among all revisions with specified traffic
   * based on the percent of traffic they are receiving.
   *
   * @var int
   */
  public $manualInstanceCount;
  /**
   * Optional. total max instances for the service. This number of instances is
   * divided among all revisions with specified traffic based on the percent of
   * traffic they are receiving.
   *
   * @var int
   */
  public $maxInstanceCount;
  /**
   * Optional. total min instances for the service. This number of instances is
   * divided among all revisions with specified traffic based on the percent of
   * traffic they are receiving.
   *
   * @var int
   */
  public $minInstanceCount;
  /**
   * Optional. The scaling mode for the service.
   *
   * @var string
   */
  public $scalingMode;

  /**
   * Optional. total instance count for the service in manual scaling mode. This
   * number of instances is divided among all revisions with specified traffic
   * based on the percent of traffic they are receiving.
   *
   * @param int $manualInstanceCount
   */
  public function setManualInstanceCount($manualInstanceCount)
  {
    $this->manualInstanceCount = $manualInstanceCount;
  }
  /**
   * @return int
   */
  public function getManualInstanceCount()
  {
    return $this->manualInstanceCount;
  }
  /**
   * Optional. total max instances for the service. This number of instances is
   * divided among all revisions with specified traffic based on the percent of
   * traffic they are receiving.
   *
   * @param int $maxInstanceCount
   */
  public function setMaxInstanceCount($maxInstanceCount)
  {
    $this->maxInstanceCount = $maxInstanceCount;
  }
  /**
   * @return int
   */
  public function getMaxInstanceCount()
  {
    return $this->maxInstanceCount;
  }
  /**
   * Optional. total min instances for the service. This number of instances is
   * divided among all revisions with specified traffic based on the percent of
   * traffic they are receiving.
   *
   * @param int $minInstanceCount
   */
  public function setMinInstanceCount($minInstanceCount)
  {
    $this->minInstanceCount = $minInstanceCount;
  }
  /**
   * @return int
   */
  public function getMinInstanceCount()
  {
    return $this->minInstanceCount;
  }
  /**
   * Optional. The scaling mode for the service.
   *
   * Accepted values: SCALING_MODE_UNSPECIFIED, AUTOMATIC, MANUAL
   *
   * @param self::SCALING_MODE_* $scalingMode
   */
  public function setScalingMode($scalingMode)
  {
    $this->scalingMode = $scalingMode;
  }
  /**
   * @return self::SCALING_MODE_*
   */
  public function getScalingMode()
  {
    return $this->scalingMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2ServiceScaling::class, 'Google_Service_CloudRun_GoogleCloudRunV2ServiceScaling');
