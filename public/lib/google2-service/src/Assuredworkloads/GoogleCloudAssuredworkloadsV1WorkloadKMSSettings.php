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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1WorkloadKMSSettings extends \Google\Model
{
  /**
   * Required. Input only. Immutable. The time at which the Key Management
   * Service will automatically create a new version of the crypto key and mark
   * it as the primary.
   *
   * @var string
   */
  public $nextRotationTime;
  /**
   * Required. Input only. Immutable. [next_rotation_time] will be advanced by
   * this period when the Key Management Service automatically rotates a key.
   * Must be at least 24 hours and at most 876,000 hours.
   *
   * @var string
   */
  public $rotationPeriod;

  /**
   * Required. Input only. Immutable. The time at which the Key Management
   * Service will automatically create a new version of the crypto key and mark
   * it as the primary.
   *
   * @param string $nextRotationTime
   */
  public function setNextRotationTime($nextRotationTime)
  {
    $this->nextRotationTime = $nextRotationTime;
  }
  /**
   * @return string
   */
  public function getNextRotationTime()
  {
    return $this->nextRotationTime;
  }
  /**
   * Required. Input only. Immutable. [next_rotation_time] will be advanced by
   * this period when the Key Management Service automatically rotates a key.
   * Must be at least 24 hours and at most 876,000 hours.
   *
   * @param string $rotationPeriod
   */
  public function setRotationPeriod($rotationPeriod)
  {
    $this->rotationPeriod = $rotationPeriod;
  }
  /**
   * @return string
   */
  public function getRotationPeriod()
  {
    return $this->rotationPeriod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1WorkloadKMSSettings::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1WorkloadKMSSettings');
