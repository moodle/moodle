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

class Scan extends \Google\Model
{
  /**
   * Additional information provided by the implementer.
   *
   * @var array[]
   */
  public $details;
  /**
   * The upper bound for when the scan is defined.
   *
   * @var string
   */
  public $endTime;
  /**
   * The unique name of the scan, specific to the Database service implementing
   * this interface.
   *
   * @var string
   */
  public $name;
  protected $scanDataType = ScanData::class;
  protected $scanDataDataType = '';
  /**
   * A range of time (inclusive) for when the scan is defined. The lower bound
   * for when the scan is defined.
   *
   * @var string
   */
  public $startTime;

  /**
   * Additional information provided by the implementer.
   *
   * @param array[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return array[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The upper bound for when the scan is defined.
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
   * The unique name of the scan, specific to the Database service implementing
   * this interface.
   *
   * @param string $name
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
   * Output only. Cloud Key Visualizer scan data. Note, this field is not
   * available to the ListScans method.
   *
   * @param ScanData $scanData
   */
  public function setScanData(ScanData $scanData)
  {
    $this->scanData = $scanData;
  }
  /**
   * @return ScanData
   */
  public function getScanData()
  {
    return $this->scanData;
  }
  /**
   * A range of time (inclusive) for when the scan is defined. The lower bound
   * for when the scan is defined.
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
class_alias(Scan::class, 'Google_Service_Spanner_Scan');
