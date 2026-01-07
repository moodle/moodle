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

class Seats extends \Google\Model
{
  /**
   * Identifies the resource as a subscription seat setting. Value:
   * `subscriptions#seats`
   *
   * @var string
   */
  public $kind;
  /**
   * Read-only field containing the current number of users that are assigned a
   * license for the product defined in `skuId`. This field's value is
   * equivalent to the numerical count of users returned by the Enterprise
   * License Manager API method: [`listForProductAndSku`](https://developers.goo
   * gle.com/workspace/admin/licensing/v1/reference/licenseAssignments/listForPr
   * oductAndSku).
   *
   * @var int
   */
  public $licensedNumberOfSeats;
  /**
   * This is a required property and is exclusive to subscriptions with
   * `FLEXIBLE` or `TRIAL` plans. This property sets the maximum number of
   * licensed users allowed on a subscription. This quantity can be increased up
   * to the maximum limit defined in the reseller's contract. The minimum
   * quantity is the current number of users in the customer account. *Note: *G
   * Suite subscriptions automatically assign a license to every user.
   *
   * @var int
   */
  public $maximumNumberOfSeats;
  /**
   * This is a required property and is exclusive to subscriptions with
   * `ANNUAL_MONTHLY_PAY` and `ANNUAL_YEARLY_PAY` plans. This property sets the
   * maximum number of licenses assignable to users on a subscription. The
   * reseller can add more licenses, but once set, the `numberOfSeats` cannot be
   * reduced until renewal. The reseller is invoiced based on the
   * `numberOfSeats` value regardless of how many of these user licenses are
   * assigned. *Note: *Google Workspace subscriptions automatically assign a
   * license to every user.
   *
   * @var int
   */
  public $numberOfSeats;

  /**
   * Identifies the resource as a subscription seat setting. Value:
   * `subscriptions#seats`
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
   * Read-only field containing the current number of users that are assigned a
   * license for the product defined in `skuId`. This field's value is
   * equivalent to the numerical count of users returned by the Enterprise
   * License Manager API method: [`listForProductAndSku`](https://developers.goo
   * gle.com/workspace/admin/licensing/v1/reference/licenseAssignments/listForPr
   * oductAndSku).
   *
   * @param int $licensedNumberOfSeats
   */
  public function setLicensedNumberOfSeats($licensedNumberOfSeats)
  {
    $this->licensedNumberOfSeats = $licensedNumberOfSeats;
  }
  /**
   * @return int
   */
  public function getLicensedNumberOfSeats()
  {
    return $this->licensedNumberOfSeats;
  }
  /**
   * This is a required property and is exclusive to subscriptions with
   * `FLEXIBLE` or `TRIAL` plans. This property sets the maximum number of
   * licensed users allowed on a subscription. This quantity can be increased up
   * to the maximum limit defined in the reseller's contract. The minimum
   * quantity is the current number of users in the customer account. *Note: *G
   * Suite subscriptions automatically assign a license to every user.
   *
   * @param int $maximumNumberOfSeats
   */
  public function setMaximumNumberOfSeats($maximumNumberOfSeats)
  {
    $this->maximumNumberOfSeats = $maximumNumberOfSeats;
  }
  /**
   * @return int
   */
  public function getMaximumNumberOfSeats()
  {
    return $this->maximumNumberOfSeats;
  }
  /**
   * This is a required property and is exclusive to subscriptions with
   * `ANNUAL_MONTHLY_PAY` and `ANNUAL_YEARLY_PAY` plans. This property sets the
   * maximum number of licenses assignable to users on a subscription. The
   * reseller can add more licenses, but once set, the `numberOfSeats` cannot be
   * reduced until renewal. The reseller is invoiced based on the
   * `numberOfSeats` value regardless of how many of these user licenses are
   * assigned. *Note: *Google Workspace subscriptions automatically assign a
   * license to every user.
   *
   * @param int $numberOfSeats
   */
  public function setNumberOfSeats($numberOfSeats)
  {
    $this->numberOfSeats = $numberOfSeats;
  }
  /**
   * @return int
   */
  public function getNumberOfSeats()
  {
    return $this->numberOfSeats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Seats::class, 'Google_Service_Reseller_Seats');
