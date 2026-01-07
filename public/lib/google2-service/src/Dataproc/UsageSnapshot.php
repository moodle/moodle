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

class UsageSnapshot extends \Google\Model
{
  /**
   * Optional. Accelerator type being used, if any
   *
   * @var string
   */
  public $acceleratorType;
  /**
   * Optional. Milli (one-thousandth) accelerator. (see Dataproc Serverless
   * pricing (https://cloud.google.com/dataproc-serverless/pricing))
   *
   * @var string
   */
  public $milliAccelerator;
  /**
   * Optional. Milli (one-thousandth) Dataproc Compute Units (DCUs) (see
   * Dataproc Serverless pricing (https://cloud.google.com/dataproc-
   * serverless/pricing)).
   *
   * @var string
   */
  public $milliDcu;
  /**
   * Optional. Milli (one-thousandth) Dataproc Compute Units (DCUs) charged at
   * premium tier (see Dataproc Serverless pricing
   * (https://cloud.google.com/dataproc-serverless/pricing)).
   *
   * @var string
   */
  public $milliDcuPremium;
  /**
   * Optional. Shuffle Storage in gigabytes (GB). (see Dataproc Serverless
   * pricing (https://cloud.google.com/dataproc-serverless/pricing))
   *
   * @var string
   */
  public $shuffleStorageGb;
  /**
   * Optional. Shuffle Storage in gigabytes (GB) charged at premium tier. (see
   * Dataproc Serverless pricing (https://cloud.google.com/dataproc-
   * serverless/pricing))
   *
   * @var string
   */
  public $shuffleStorageGbPremium;
  /**
   * Optional. The timestamp of the usage snapshot.
   *
   * @var string
   */
  public $snapshotTime;

  /**
   * Optional. Accelerator type being used, if any
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
   * Optional. Milli (one-thousandth) accelerator. (see Dataproc Serverless
   * pricing (https://cloud.google.com/dataproc-serverless/pricing))
   *
   * @param string $milliAccelerator
   */
  public function setMilliAccelerator($milliAccelerator)
  {
    $this->milliAccelerator = $milliAccelerator;
  }
  /**
   * @return string
   */
  public function getMilliAccelerator()
  {
    return $this->milliAccelerator;
  }
  /**
   * Optional. Milli (one-thousandth) Dataproc Compute Units (DCUs) (see
   * Dataproc Serverless pricing (https://cloud.google.com/dataproc-
   * serverless/pricing)).
   *
   * @param string $milliDcu
   */
  public function setMilliDcu($milliDcu)
  {
    $this->milliDcu = $milliDcu;
  }
  /**
   * @return string
   */
  public function getMilliDcu()
  {
    return $this->milliDcu;
  }
  /**
   * Optional. Milli (one-thousandth) Dataproc Compute Units (DCUs) charged at
   * premium tier (see Dataproc Serverless pricing
   * (https://cloud.google.com/dataproc-serverless/pricing)).
   *
   * @param string $milliDcuPremium
   */
  public function setMilliDcuPremium($milliDcuPremium)
  {
    $this->milliDcuPremium = $milliDcuPremium;
  }
  /**
   * @return string
   */
  public function getMilliDcuPremium()
  {
    return $this->milliDcuPremium;
  }
  /**
   * Optional. Shuffle Storage in gigabytes (GB). (see Dataproc Serverless
   * pricing (https://cloud.google.com/dataproc-serverless/pricing))
   *
   * @param string $shuffleStorageGb
   */
  public function setShuffleStorageGb($shuffleStorageGb)
  {
    $this->shuffleStorageGb = $shuffleStorageGb;
  }
  /**
   * @return string
   */
  public function getShuffleStorageGb()
  {
    return $this->shuffleStorageGb;
  }
  /**
   * Optional. Shuffle Storage in gigabytes (GB) charged at premium tier. (see
   * Dataproc Serverless pricing (https://cloud.google.com/dataproc-
   * serverless/pricing))
   *
   * @param string $shuffleStorageGbPremium
   */
  public function setShuffleStorageGbPremium($shuffleStorageGbPremium)
  {
    $this->shuffleStorageGbPremium = $shuffleStorageGbPremium;
  }
  /**
   * @return string
   */
  public function getShuffleStorageGbPremium()
  {
    return $this->shuffleStorageGbPremium;
  }
  /**
   * Optional. The timestamp of the usage snapshot.
   *
   * @param string $snapshotTime
   */
  public function setSnapshotTime($snapshotTime)
  {
    $this->snapshotTime = $snapshotTime;
  }
  /**
   * @return string
   */
  public function getSnapshotTime()
  {
    return $this->snapshotTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsageSnapshot::class, 'Google_Service_Dataproc_UsageSnapshot');
