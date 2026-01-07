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

class UpdateInstanceMetadata extends \Google\Model
{
  /**
   * Not specified.
   */
  public const EXPECTED_FULFILLMENT_PERIOD_FULFILLMENT_PERIOD_UNSPECIFIED = 'FULFILLMENT_PERIOD_UNSPECIFIED';
  /**
   * Normal fulfillment period. The operation is expected to complete within
   * minutes.
   */
  public const EXPECTED_FULFILLMENT_PERIOD_FULFILLMENT_PERIOD_NORMAL = 'FULFILLMENT_PERIOD_NORMAL';
  /**
   * Extended fulfillment period. It can take up to an hour for the operation to
   * complete.
   */
  public const EXPECTED_FULFILLMENT_PERIOD_FULFILLMENT_PERIOD_EXTENDED = 'FULFILLMENT_PERIOD_EXTENDED';
  /**
   * The time at which this operation was cancelled. If set, this operation is
   * in the process of undoing itself (which is guaranteed to succeed) and
   * cannot be cancelled again.
   *
   * @var string
   */
  public $cancelTime;
  /**
   * The time at which this operation failed or was completed successfully.
   *
   * @var string
   */
  public $endTime;
  /**
   * The expected fulfillment period of this update operation.
   *
   * @var string
   */
  public $expectedFulfillmentPeriod;
  protected $instanceType = Instance::class;
  protected $instanceDataType = '';
  /**
   * The time at which UpdateInstance request was received.
   *
   * @var string
   */
  public $startTime;

  /**
   * The time at which this operation was cancelled. If set, this operation is
   * in the process of undoing itself (which is guaranteed to succeed) and
   * cannot be cancelled again.
   *
   * @param string $cancelTime
   */
  public function setCancelTime($cancelTime)
  {
    $this->cancelTime = $cancelTime;
  }
  /**
   * @return string
   */
  public function getCancelTime()
  {
    return $this->cancelTime;
  }
  /**
   * The time at which this operation failed or was completed successfully.
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
   * The expected fulfillment period of this update operation.
   *
   * Accepted values: FULFILLMENT_PERIOD_UNSPECIFIED, FULFILLMENT_PERIOD_NORMAL,
   * FULFILLMENT_PERIOD_EXTENDED
   *
   * @param self::EXPECTED_FULFILLMENT_PERIOD_* $expectedFulfillmentPeriod
   */
  public function setExpectedFulfillmentPeriod($expectedFulfillmentPeriod)
  {
    $this->expectedFulfillmentPeriod = $expectedFulfillmentPeriod;
  }
  /**
   * @return self::EXPECTED_FULFILLMENT_PERIOD_*
   */
  public function getExpectedFulfillmentPeriod()
  {
    return $this->expectedFulfillmentPeriod;
  }
  /**
   * The desired end state of the update.
   *
   * @param Instance $instance
   */
  public function setInstance(Instance $instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return Instance
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * The time at which UpdateInstance request was received.
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
class_alias(UpdateInstanceMetadata::class, 'Google_Service_Spanner_UpdateInstanceMetadata');
