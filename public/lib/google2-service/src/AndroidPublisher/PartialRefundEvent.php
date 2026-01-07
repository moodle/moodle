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

namespace Google\Service\AndroidPublisher;

class PartialRefundEvent extends \Google\Model
{
  /**
   * State unspecified. This value is not used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The partial refund has been created, but not yet processed.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The partial refund was processed successfully.
   */
  public const STATE_PROCESSED_SUCCESSFULLY = 'PROCESSED_SUCCESSFULLY';
  /**
   * The time when the partial refund was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time when the partial refund was processed.
   *
   * @var string
   */
  public $processTime;
  protected $refundDetailsType = RefundDetails::class;
  protected $refundDetailsDataType = '';
  /**
   * The state of the partial refund.
   *
   * @var string
   */
  public $state;

  /**
   * The time when the partial refund was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The time when the partial refund was processed.
   *
   * @param string $processTime
   */
  public function setProcessTime($processTime)
  {
    $this->processTime = $processTime;
  }
  /**
   * @return string
   */
  public function getProcessTime()
  {
    return $this->processTime;
  }
  /**
   * Details for the partial refund.
   *
   * @param RefundDetails $refundDetails
   */
  public function setRefundDetails(RefundDetails $refundDetails)
  {
    $this->refundDetails = $refundDetails;
  }
  /**
   * @return RefundDetails
   */
  public function getRefundDetails()
  {
    return $this->refundDetails;
  }
  /**
   * The state of the partial refund.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, PROCESSED_SUCCESSFULLY
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartialRefundEvent::class, 'Google_Service_AndroidPublisher_PartialRefundEvent');
