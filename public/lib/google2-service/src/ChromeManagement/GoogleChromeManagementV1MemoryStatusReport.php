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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1MemoryStatusReport extends \Google\Model
{
  /**
   * Output only. Number of page faults during this collection
   *
   * @var int
   */
  public $pageFaults;
  /**
   * Output only. The timestamp in milliseconds representing time at which this
   * report was sampled.
   *
   * @var string
   */
  public $reportTime;
  /**
   * Output only. Frequency the report is sampled.
   *
   * @var string
   */
  public $sampleFrequency;
  /**
   * Output only. Amount of free RAM in bytes (unreliable due to Garbage
   * Collection).
   *
   * @var string
   */
  public $systemRamFreeBytes;

  /**
   * Output only. Number of page faults during this collection
   *
   * @param int $pageFaults
   */
  public function setPageFaults($pageFaults)
  {
    $this->pageFaults = $pageFaults;
  }
  /**
   * @return int
   */
  public function getPageFaults()
  {
    return $this->pageFaults;
  }
  /**
   * Output only. The timestamp in milliseconds representing time at which this
   * report was sampled.
   *
   * @param string $reportTime
   */
  public function setReportTime($reportTime)
  {
    $this->reportTime = $reportTime;
  }
  /**
   * @return string
   */
  public function getReportTime()
  {
    return $this->reportTime;
  }
  /**
   * Output only. Frequency the report is sampled.
   *
   * @param string $sampleFrequency
   */
  public function setSampleFrequency($sampleFrequency)
  {
    $this->sampleFrequency = $sampleFrequency;
  }
  /**
   * @return string
   */
  public function getSampleFrequency()
  {
    return $this->sampleFrequency;
  }
  /**
   * Output only. Amount of free RAM in bytes (unreliable due to Garbage
   * Collection).
   *
   * @param string $systemRamFreeBytes
   */
  public function setSystemRamFreeBytes($systemRamFreeBytes)
  {
    $this->systemRamFreeBytes = $systemRamFreeBytes;
  }
  /**
   * @return string
   */
  public function getSystemRamFreeBytes()
  {
    return $this->systemRamFreeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1MemoryStatusReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1MemoryStatusReport');
