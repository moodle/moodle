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

class Schedule extends \Google\Model
{
  /**
   * @var string
   */
  public $cronExpression;
  /**
   * @var string
   */
  public $description;
  /**
   * @var bool
   */
  public $disabled;
  /**
   * @var string
   */
  public $durationSec;
  /**
   * @var string
   */
  public $minNodeCount;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $timeZone;

  /**
   * @param string
   */
  public function setCronExpression($cronExpression)
  {
    $this->cronExpression = $cronExpression;
  }
  /**
   * @return string
   */
  public function getCronExpression()
  {
    return $this->cronExpression;
  }
  /**
   * @param string
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * @param bool
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * @param string
   */
  public function setDurationSec($durationSec)
  {
    $this->durationSec = $durationSec;
  }
  /**
   * @return string
   */
  public function getDurationSec()
  {
    return $this->durationSec;
  }
  /**
   * @param string
   */
  public function setMinNodeCount($minNodeCount)
  {
    $this->minNodeCount = $minNodeCount;
  }
  /**
   * @return string
   */
  public function getMinNodeCount()
  {
    return $this->minNodeCount;
  }
  /**
   * @param string
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * @param string
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Schedule::class, 'Google_Service_CloudAlloyDBAdmin_Schedule');
