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

namespace Google\Service\SecretManager;

class Rotation extends \Google\Model
{
  /**
   * Optional. Timestamp in UTC at which the Secret is scheduled to rotate.
   * Cannot be set to less than 300s (5 min) in the future and at most
   * 3153600000s (100 years). next_rotation_time MUST be set if rotation_period
   * is set.
   *
   * @var string
   */
  public $nextRotationTime;
  /**
   * Input only. The Duration between rotation notifications. Must be in seconds
   * and at least 3600s (1h) and at most 3153600000s (100 years). If
   * rotation_period is set, next_rotation_time must be set. next_rotation_time
   * will be advanced by this period when the service automatically sends
   * rotation notifications.
   *
   * @var string
   */
  public $rotationPeriod;

  /**
   * Optional. Timestamp in UTC at which the Secret is scheduled to rotate.
   * Cannot be set to less than 300s (5 min) in the future and at most
   * 3153600000s (100 years). next_rotation_time MUST be set if rotation_period
   * is set.
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
   * Input only. The Duration between rotation notifications. Must be in seconds
   * and at least 3600s (1h) and at most 3153600000s (100 years). If
   * rotation_period is set, next_rotation_time must be set. next_rotation_time
   * will be advanced by this period when the service automatically sends
   * rotation notifications.
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
class_alias(Rotation::class, 'Google_Service_SecretManager_Rotation');
