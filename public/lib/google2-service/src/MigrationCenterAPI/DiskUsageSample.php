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

namespace Google\Service\MigrationCenterAPI;

class DiskUsageSample extends \Google\Model
{
  /**
   * Optional. Average IOPS sampled over a short window. Must be non-negative.
   * If read or write are set, the sum of read and write will override the value
   * of the average_iops.
   *
   * @var float
   */
  public $averageIops;
  /**
   * Optional. Average read IOPS sampled over a short window. Must be non-
   * negative. If both read and write are zero they are ignored.
   *
   * @var float
   */
  public $averageReadIops;
  /**
   * Optional. Average write IOPS sampled over a short window. Must be non-
   * negative. If both read and write are zero they are ignored.
   *
   * @var float
   */
  public $averageWriteIops;

  /**
   * Optional. Average IOPS sampled over a short window. Must be non-negative.
   * If read or write are set, the sum of read and write will override the value
   * of the average_iops.
   *
   * @param float $averageIops
   */
  public function setAverageIops($averageIops)
  {
    $this->averageIops = $averageIops;
  }
  /**
   * @return float
   */
  public function getAverageIops()
  {
    return $this->averageIops;
  }
  /**
   * Optional. Average read IOPS sampled over a short window. Must be non-
   * negative. If both read and write are zero they are ignored.
   *
   * @param float $averageReadIops
   */
  public function setAverageReadIops($averageReadIops)
  {
    $this->averageReadIops = $averageReadIops;
  }
  /**
   * @return float
   */
  public function getAverageReadIops()
  {
    return $this->averageReadIops;
  }
  /**
   * Optional. Average write IOPS sampled over a short window. Must be non-
   * negative. If both read and write are zero they are ignored.
   *
   * @param float $averageWriteIops
   */
  public function setAverageWriteIops($averageWriteIops)
  {
    $this->averageWriteIops = $averageWriteIops;
  }
  /**
   * @return float
   */
  public function getAverageWriteIops()
  {
    return $this->averageWriteIops;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskUsageSample::class, 'Google_Service_MigrationCenterAPI_DiskUsageSample');
