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

namespace Google\Service\Reseller;

class ChangePlanRequest extends \Google\Model
{
  /**
   * Google-issued code (100 char max) for discounted pricing on subscription
   * plans. Deal code must be included in `changePlan` request in order to
   * receive discounted rate. This property is optional. If a deal code has
   * already been added to a subscription, this property may be left empty and
   * the existing discounted rate will still apply (if not empty, only provide
   * the deal code that is already present on the subscription). If a deal code
   * has never been added to a subscription and this property is left blank,
   * regular pricing will apply.
   *
   * @var string
   */
  public $dealCode;
  /**
   * Identifies the resource as a subscription change plan request. Value:
   * `subscriptions#changePlanRequest`
   *
   * @var string
   */
  public $kind;
  /**
   * The `planName` property is required. This is the name of the subscription's
   * payment plan. For more information about the Google payment plans, see API
   * concepts. Possible values are: - `ANNUAL_MONTHLY_PAY` - The annual
   * commitment plan with monthly payments *Caution: *`ANNUAL_MONTHLY_PAY` is
   * returned as `ANNUAL` in all API responses. - `ANNUAL_YEARLY_PAY` - The
   * annual commitment plan with yearly payments - `FLEXIBLE` - The flexible
   * plan - `TRIAL` - The 30-day free trial plan
   *
   * @var string
   */
  public $planName;
  /**
   * This is an optional property. This purchase order (PO) information is for
   * resellers to use for their company tracking usage. If a `purchaseOrderId`
   * value is given it appears in the API responses and shows up in the invoice.
   * The property accepts up to 80 plain text characters.
   *
   * @var string
   */
  public $purchaseOrderId;
  protected $seatsType = Seats::class;
  protected $seatsDataType = '';

  /**
   * Google-issued code (100 char max) for discounted pricing on subscription
   * plans. Deal code must be included in `changePlan` request in order to
   * receive discounted rate. This property is optional. If a deal code has
   * already been added to a subscription, this property may be left empty and
   * the existing discounted rate will still apply (if not empty, only provide
   * the deal code that is already present on the subscription). If a deal code
   * has never been added to a subscription and this property is left blank,
   * regular pricing will apply.
   *
   * @param string $dealCode
   */
  public function setDealCode($dealCode)
  {
    $this->dealCode = $dealCode;
  }
  /**
   * @return string
   */
  public function getDealCode()
  {
    return $this->dealCode;
  }
  /**
   * Identifies the resource as a subscription change plan request. Value:
   * `subscriptions#changePlanRequest`
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The `planName` property is required. This is the name of the subscription's
   * payment plan. For more information about the Google payment plans, see API
   * concepts. Possible values are: - `ANNUAL_MONTHLY_PAY` - The annual
   * commitment plan with monthly payments *Caution: *`ANNUAL_MONTHLY_PAY` is
   * returned as `ANNUAL` in all API responses. - `ANNUAL_YEARLY_PAY` - The
   * annual commitment plan with yearly payments - `FLEXIBLE` - The flexible
   * plan - `TRIAL` - The 30-day free trial plan
   *
   * @param string $planName
   */
  public function setPlanName($planName)
  {
    $this->planName = $planName;
  }
  /**
   * @return string
   */
  public function getPlanName()
  {
    return $this->planName;
  }
  /**
   * This is an optional property. This purchase order (PO) information is for
   * resellers to use for their company tracking usage. If a `purchaseOrderId`
   * value is given it appears in the API responses and shows up in the invoice.
   * The property accepts up to 80 plain text characters.
   *
   * @param string $purchaseOrderId
   */
  public function setPurchaseOrderId($purchaseOrderId)
  {
    $this->purchaseOrderId = $purchaseOrderId;
  }
  /**
   * @return string
   */
  public function getPurchaseOrderId()
  {
    return $this->purchaseOrderId;
  }
  /**
   * This is a required property. The seats property is the number of user seat
   * licenses.
   *
   * @param Seats $seats
   */
  public function setSeats(Seats $seats)
  {
    $this->seats = $seats;
  }
  /**
   * @return Seats
   */
  public function getSeats()
  {
    return $this->seats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChangePlanRequest::class, 'Google_Service_Reseller_ChangePlanRequest');
