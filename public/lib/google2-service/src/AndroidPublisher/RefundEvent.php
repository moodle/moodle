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

class RefundEvent extends \Google\Model
{
  /**
   * Refund reason unspecified. This value is not used.
   */
  public const REFUND_REASON_REFUND_REASON_UNSPECIFIED = 'REFUND_REASON_UNSPECIFIED';
  /**
   * The order was refunded for a reason other than the listed reasons here.
   */
  public const REFUND_REASON_OTHER = 'OTHER';
  /**
   * The order was charged back.
   */
  public const REFUND_REASON_CHARGEBACK = 'CHARGEBACK';
  /**
   * The time when the order was fully refunded.
   *
   * @var string
   */
  public $eventTime;
  protected $refundDetailsType = RefundDetails::class;
  protected $refundDetailsDataType = '';
  /**
   * The reason the order was refunded.
   *
   * @var string
   */
  public $refundReason;

  /**
   * The time when the order was fully refunded.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Details for the full refund.
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
   * The reason the order was refunded.
   *
   * Accepted values: REFUND_REASON_UNSPECIFIED, OTHER, CHARGEBACK
   *
   * @param self::REFUND_REASON_* $refundReason
   */
  public function setRefundReason($refundReason)
  {
    $this->refundReason = $refundReason;
  }
  /**
   * @return self::REFUND_REASON_*
   */
  public function getRefundReason()
  {
    return $this->refundReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RefundEvent::class, 'Google_Service_AndroidPublisher_RefundEvent');
