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

class TrialMetadata extends \Google\Model
{
  /**
   * End time of the trial cluster.
   *
   * @var string
   */
  public $endTime;
  /**
   * grace end time of the cluster.
   *
   * @var string
   */
  public $graceEndTime;
  /**
   * start time of the trial cluster.
   *
   * @var string
   */
  public $startTime;
  /**
   * Upgrade time of trial cluster to Standard cluster.
   *
   * @var string
   */
  public $upgradeTime;

  /**
   * End time of the trial cluster.
   *
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
   * grace end time of the cluster.
   *
   * @param string $graceEndTime
   */
  public function setGraceEndTime($graceEndTime)
  {
    $this->graceEndTime = $graceEndTime;
  }
  /**
   * @return string
   */
  public function getGraceEndTime()
  {
    return $this->graceEndTime;
  }
  /**
   * start time of the trial cluster.
   *
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
  /**
   * Upgrade time of trial cluster to Standard cluster.
   *
   * @param string $upgradeTime
   */
  public function setUpgradeTime($upgradeTime)
  {
    $this->upgradeTime = $upgradeTime;
  }
  /**
   * @return string
   */
  public function getUpgradeTime()
  {
    return $this->upgradeTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrialMetadata::class, 'Google_Service_CloudAlloyDBAdmin_TrialMetadata');
