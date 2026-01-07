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

class UsageMetrics extends \Google\Model
{
  /**
   * Optional. DEPRECATED Accelerator type being used, if any
   *
   * @var string
   */
  public $acceleratorType;
  /**
   * Optional. DEPRECATED Accelerator usage in (milliAccelerator x seconds) (see
   * Dataproc Serverless pricing (https://cloud.google.com/dataproc-
   * serverless/pricing)).
   *
   * @var string
   */
  public $milliAcceleratorSeconds;
  /**
   * Optional. DCU (Dataproc Compute Units) usage in (milliDCU x seconds) (see
   * Dataproc Serverless pricing (https://cloud.google.com/dataproc-
   * serverless/pricing)).
   *
   * @var string
   */
  public $milliDcuSeconds;
  /**
   * Optional. Shuffle storage usage in (GB x seconds) (see Dataproc Serverless
   * pricing (https://cloud.google.com/dataproc-serverless/pricing)).
   *
   * @var string
   */
  public $shuffleStorageGbSeconds;
  /**
   * Optional. The timestamp of the usage metrics.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. DEPRECATED Accelerator type being used, if any
   *
   * @param string $acceleratorType
   */
  public function setAcceleratorType($acceleratorType)
  {
    $this->acceleratorType = $acceleratorType;
  }
  /**
   * @return string
   */
  public function getAcceleratorType()
  {
    return $this->acceleratorType;
  }
  /**
   * Optional. DEPRECATED Accelerator usage in (milliAccelerator x seconds) (see
   * Dataproc Serverless pricing (https://cloud.google.com/dataproc-
   * serverless/pricing)).
   *
   * @param string $milliAcceleratorSeconds
   */
  public function setMilliAcceleratorSeconds($milliAcceleratorSeconds)
  {
    $this->milliAcceleratorSeconds = $milliAcceleratorSeconds;
  }
  /**
   * @return string
   */
  public function getMilliAcceleratorSeconds()
  {
    return $this->milliAcceleratorSeconds;
  }
  /**
   * Optional. DCU (Dataproc Compute Units) usage in (milliDCU x seconds) (see
   * Dataproc Serverless pricing (https://cloud.google.com/dataproc-
   * serverless/pricing)).
   *
   * @param string $milliDcuSeconds
   */
  public function setMilliDcuSeconds($milliDcuSeconds)
  {
    $this->milliDcuSeconds = $milliDcuSeconds;
  }
  /**
   * @return string
   */
  public function getMilliDcuSeconds()
  {
    return $this->milliDcuSeconds;
  }
  /**
   * Optional. Shuffle storage usage in (GB x seconds) (see Dataproc Serverless
   * pricing (https://cloud.google.com/dataproc-serverless/pricing)).
   *
   * @param string $shuffleStorageGbSeconds
   */
  public function setShuffleStorageGbSeconds($shuffleStorageGbSeconds)
  {
    $this->shuffleStorageGbSeconds = $shuffleStorageGbSeconds;
  }
  /**
   * @return string
   */
  public function getShuffleStorageGbSeconds()
  {
    return $this->shuffleStorageGbSeconds;
  }
  /**
   * Optional. The timestamp of the usage metrics.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsageMetrics::class, 'Google_Service_Dataproc_UsageMetrics');
