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

class RefundExternalTransactionRequest extends \Google\Model
{
  protected $fullRefundType = FullRefund::class;
  protected $fullRefundDataType = '';
  protected $partialRefundType = PartialRefund::class;
  protected $partialRefundDataType = '';
  /**
   * Required. The time that the transaction was refunded.
   *
   * @var string
   */
  public $refundTime;

  /**
   * A full-amount refund.
   *
   * @param FullRefund $fullRefund
   */
  public function setFullRefund(FullRefund $fullRefund)
  {
    $this->fullRefund = $fullRefund;
  }
  /**
   * @return FullRefund
   */
  public function getFullRefund()
  {
    return $this->fullRefund;
  }
  /**
   * A partial refund.
   *
   * @param PartialRefund $partialRefund
   */
  public function setPartialRefund(PartialRefund $partialRefund)
  {
    $this->partialRefund = $partialRefund;
  }
  /**
   * @return PartialRefund
   */
  public function getPartialRefund()
  {
    return $this->partialRefund;
  }
  /**
   * Required. The time that the transaction was refunded.
   *
   * @param string $refundTime
   */
  public function setRefundTime($refundTime)
  {
    $this->refundTime = $refundTime;
  }
  /**
   * @return string
   */
  public function getRefundTime()
  {
    return $this->refundTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RefundExternalTransactionRequest::class, 'Google_Service_AndroidPublisher_RefundExternalTransactionRequest');
