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

namespace Google\Service\TPU;

class QueueingPolicy extends \Google\Model
{
  /**
   * Optional. A relative time after which resources may be created.
   *
   * @var string
   */
  public $validAfterDuration;
  /**
   * Optional. An absolute time after which resources may be created.
   *
   * @var string
   */
  public $validAfterTime;
  protected $validIntervalType = Interval::class;
  protected $validIntervalDataType = '';
  /**
   * Optional. A relative time after which resources should not be created. If
   * the request cannot be fulfilled by this time the request will be failed.
   *
   * @var string
   */
  public $validUntilDuration;
  /**
   * Optional. An absolute time after which resources should not be created. If
   * the request cannot be fulfilled by this time the request will be failed.
   *
   * @var string
   */
  public $validUntilTime;

  /**
   * Optional. A relative time after which resources may be created.
   *
   * @param string $validAfterDuration
   */
  public function setValidAfterDuration($validAfterDuration)
  {
    $this->validAfterDuration = $validAfterDuration;
  }
  /**
   * @return string
   */
  public function getValidAfterDuration()
  {
    return $this->validAfterDuration;
  }
  /**
   * Optional. An absolute time after which resources may be created.
   *
   * @param string $validAfterTime
   */
  public function setValidAfterTime($validAfterTime)
  {
    $this->validAfterTime = $validAfterTime;
  }
  /**
   * @return string
   */
  public function getValidAfterTime()
  {
    return $this->validAfterTime;
  }
  /**
   * Optional. An absolute time interval within which resources may be created.
   *
   * @param Interval $validInterval
   */
  public function setValidInterval(Interval $validInterval)
  {
    $this->validInterval = $validInterval;
  }
  /**
   * @return Interval
   */
  public function getValidInterval()
  {
    return $this->validInterval;
  }
  /**
   * Optional. A relative time after which resources should not be created. If
   * the request cannot be fulfilled by this time the request will be failed.
   *
   * @param string $validUntilDuration
   */
  public function setValidUntilDuration($validUntilDuration)
  {
    $this->validUntilDuration = $validUntilDuration;
  }
  /**
   * @return string
   */
  public function getValidUntilDuration()
  {
    return $this->validUntilDuration;
  }
  /**
   * Optional. An absolute time after which resources should not be created. If
   * the request cannot be fulfilled by this time the request will be failed.
   *
   * @param string $validUntilTime
   */
  public function setValidUntilTime($validUntilTime)
  {
    $this->validUntilTime = $validUntilTime;
  }
  /**
   * @return string
   */
  public function getValidUntilTime()
  {
    return $this->validUntilTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueueingPolicy::class, 'Google_Service_TPU_QueueingPolicy');
