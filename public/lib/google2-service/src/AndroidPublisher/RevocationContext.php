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

class RevocationContext extends \Google\Model
{
  protected $fullRefundType = RevocationContextFullRefund::class;
  protected $fullRefundDataType = '';
  protected $itemBasedRefundType = RevocationContextItemBasedRefund::class;
  protected $itemBasedRefundDataType = '';
  protected $proratedRefundType = RevocationContextProratedRefund::class;
  protected $proratedRefundDataType = '';

  /**
   * Optional. Used when users should be refunded the full amount of latest
   * charge on each item in the subscription.
   *
   * @param RevocationContextFullRefund $fullRefund
   */
  public function setFullRefund(RevocationContextFullRefund $fullRefund)
  {
    $this->fullRefund = $fullRefund;
  }
  /**
   * @return RevocationContextFullRefund
   */
  public function getFullRefund()
  {
    return $this->fullRefund;
  }
  /**
   * Optional. Used when a specific item should be refunded in a subscription
   * with add-on items.
   *
   * @param RevocationContextItemBasedRefund $itemBasedRefund
   */
  public function setItemBasedRefund(RevocationContextItemBasedRefund $itemBasedRefund)
  {
    $this->itemBasedRefund = $itemBasedRefund;
  }
  /**
   * @return RevocationContextItemBasedRefund
   */
  public function getItemBasedRefund()
  {
    return $this->itemBasedRefund;
  }
  /**
   * Optional. Used when users should be refunded a prorated amount they paid
   * for their subscription based on the amount of time remaining in a
   * subscription.
   *
   * @param RevocationContextProratedRefund $proratedRefund
   */
  public function setProratedRefund(RevocationContextProratedRefund $proratedRefund)
  {
    $this->proratedRefund = $proratedRefund;
  }
  /**
   * @return RevocationContextProratedRefund
   */
  public function getProratedRefund()
  {
    return $this->proratedRefund;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RevocationContext::class, 'Google_Service_AndroidPublisher_RevocationContext');
