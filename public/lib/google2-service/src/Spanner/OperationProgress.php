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

namespace Google\Service\Spanner;

class OperationProgress extends \Google\Model
{
  /**
   * If set, the time at which this operation failed or was completed
   * successfully.
   *
   * @var string
   */
  public $endTime;
  /**
   * Percent completion of the operation. Values are between 0 and 100
   * inclusive.
   *
   * @var int
   */
  public $progressPercent;
  /**
   * Time the request was received.
   *
   * @var string
   */
  public $startTime;

  /**
   * If set, the time at which this operation failed or was completed
   * successfully.
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
   * Percent completion of the operation. Values are between 0 and 100
   * inclusive.
   *
   * @param int $progressPercent
   */
  public function setProgressPercent($progressPercent)
  {
    $this->progressPercent = $progressPercent;
  }
  /**
   * @return int
   */
  public function getProgressPercent()
  {
    return $this->progressPercent;
  }
  /**
   * Time the request was received.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationProgress::class, 'Google_Service_Spanner_OperationProgress');
