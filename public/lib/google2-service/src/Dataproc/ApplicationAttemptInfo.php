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

namespace Google\Service\Dataproc;

class ApplicationAttemptInfo extends \Google\Model
{
  /**
   * @var string
   */
  public $appSparkVersion;
  /**
   * @var string
   */
  public $attemptId;
  /**
   * @var bool
   */
  public $completed;
  /**
   * @var string
   */
  public $durationMillis;
  /**
   * @var string
   */
  public $endTime;
  /**
   * @var string
   */
  public $lastUpdated;
  /**
   * @var string
   */
  public $sparkUser;
  /**
   * @var string
   */
  public $startTime;

  /**
   * @param string $appSparkVersion
   */
  public function setAppSparkVersion($appSparkVersion)
  {
    $this->appSparkVersion = $appSparkVersion;
  }
  /**
   * @return string
   */
  public function getAppSparkVersion()
  {
    return $this->appSparkVersion;
  }
  /**
   * @param string $attemptId
   */
  public function setAttemptId($attemptId)
  {
    $this->attemptId = $attemptId;
  }
  /**
   * @return string
   */
  public function getAttemptId()
  {
    return $this->attemptId;
  }
  /**
   * @param bool $completed
   */
  public function setCompleted($completed)
  {
    $this->completed = $completed;
  }
  /**
   * @return bool
   */
  public function getCompleted()
  {
    return $this->completed;
  }
  /**
   * @param string $durationMillis
   */
  public function setDurationMillis($durationMillis)
  {
    $this->durationMillis = $durationMillis;
  }
  /**
   * @return string
   */
  public function getDurationMillis()
  {
    return $this->durationMillis;
  }
  /**
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * @param string $lastUpdated
   */
  public function setLastUpdated($lastUpdated)
  {
    $this->lastUpdated = $lastUpdated;
  }
  /**
   * @return string
   */
  public function getLastUpdated()
  {
    return $this->lastUpdated;
  }
  /**
   * @param string $sparkUser
   */
  public function setSparkUser($sparkUser)
  {
    $this->sparkUser = $sparkUser;
  }
  /**
   * @return string
   */
  public function getSparkUser()
  {
    return $this->sparkUser;
  }
  /**
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationAttemptInfo::class, 'Google_Service_Dataproc_ApplicationAttemptInfo');
