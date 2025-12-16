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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1ReplayResultsSummary extends \Google\Model
{
  /**
   * The number of replayed log entries with a difference between baseline and
   * simulated policies.
   *
   * @var int
   */
  public $differenceCount;
  /**
   * The number of log entries that could not be replayed.
   *
   * @var int
   */
  public $errorCount;
  /**
   * The total number of log entries replayed.
   *
   * @var int
   */
  public $logCount;
  protected $newestDateType = GoogleTypeDate::class;
  protected $newestDateDataType = '';
  protected $oldestDateType = GoogleTypeDate::class;
  protected $oldestDateDataType = '';
  /**
   * The number of replayed log entries with no difference between baseline and
   * simulated policies.
   *
   * @var int
   */
  public $unchangedCount;

  /**
   * The number of replayed log entries with a difference between baseline and
   * simulated policies.
   *
   * @param int $differenceCount
   */
  public function setDifferenceCount($differenceCount)
  {
    $this->differenceCount = $differenceCount;
  }
  /**
   * @return int
   */
  public function getDifferenceCount()
  {
    return $this->differenceCount;
  }
  /**
   * The number of log entries that could not be replayed.
   *
   * @param int $errorCount
   */
  public function setErrorCount($errorCount)
  {
    $this->errorCount = $errorCount;
  }
  /**
   * @return int
   */
  public function getErrorCount()
  {
    return $this->errorCount;
  }
  /**
   * The total number of log entries replayed.
   *
   * @param int $logCount
   */
  public function setLogCount($logCount)
  {
    $this->logCount = $logCount;
  }
  /**
   * @return int
   */
  public function getLogCount()
  {
    return $this->logCount;
  }
  /**
   * The date of the newest log entry replayed.
   *
   * @param GoogleTypeDate $newestDate
   */
  public function setNewestDate(GoogleTypeDate $newestDate)
  {
    $this->newestDate = $newestDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getNewestDate()
  {
    return $this->newestDate;
  }
  /**
   * The date of the oldest log entry replayed.
   *
   * @param GoogleTypeDate $oldestDate
   */
  public function setOldestDate(GoogleTypeDate $oldestDate)
  {
    $this->oldestDate = $oldestDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getOldestDate()
  {
    return $this->oldestDate;
  }
  /**
   * The number of replayed log entries with no difference between baseline and
   * simulated policies.
   *
   * @param int $unchangedCount
   */
  public function setUnchangedCount($unchangedCount)
  {
    $this->unchangedCount = $unchangedCount;
  }
  /**
   * @return int
   */
  public function getUnchangedCount()
  {
    return $this->unchangedCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1ReplayResultsSummary::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1ReplayResultsSummary');
