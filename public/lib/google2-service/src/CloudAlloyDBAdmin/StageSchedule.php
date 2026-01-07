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

namespace Google\Service\CloudAlloyDBAdmin;

class StageSchedule extends \Google\Model
{
  /**
   * @var string
   */
  public $actualEndTime;
  /**
   * @var string
   */
  public $actualStartTime;
  /**
   * @var string
   */
  public $estimatedEndTime;
  /**
   * @var string
   */
  public $estimatedStartTime;

  /**
   * @param string
   */
  public function setActualEndTime($actualEndTime)
  {
    $this->actualEndTime = $actualEndTime;
  }
  /**
   * @return string
   */
  public function getActualEndTime()
  {
    return $this->actualEndTime;
  }
  /**
   * @param string
   */
  public function setActualStartTime($actualStartTime)
  {
    $this->actualStartTime = $actualStartTime;
  }
  /**
   * @return string
   */
  public function getActualStartTime()
  {
    return $this->actualStartTime;
  }
  /**
   * @param string
   */
  public function setEstimatedEndTime($estimatedEndTime)
  {
    $this->estimatedEndTime = $estimatedEndTime;
  }
  /**
   * @return string
   */
  public function getEstimatedEndTime()
  {
    return $this->estimatedEndTime;
  }
  /**
   * @param string
   */
  public function setEstimatedStartTime($estimatedStartTime)
  {
    $this->estimatedStartTime = $estimatedStartTime;
  }
  /**
   * @return string
   */
  public function getEstimatedStartTime()
  {
    return $this->estimatedStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StageSchedule::class, 'Google_Service_CloudAlloyDBAdmin_StageSchedule');
