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

class SubscriptionTransferInfo extends \Google\Model
{
  /**
   * The `skuId` of the current resold subscription. This is populated only when
   * the customer has a subscription with a legacy SKU and the subscription
   * resource is populated with the `skuId` of the SKU recommended for the
   * transfer.
   *
   * @var string
   */
  public $currentLegacySkuId;
  /**
   * When inserting a subscription, this is the minimum number of seats listed
   * in the transfer order for this product. For example, if the customer has 20
   * users, the reseller cannot place a transfer order of 15 seats. The minimum
   * is 20 seats.
   *
   * @var int
   */
  public $minimumTransferableSeats;
  /**
   * The time when transfer token or intent to transfer will expire. The time is
   * in milliseconds using UNIX Epoch format.
   *
   * @var string
   */
  public $transferabilityExpirationTime;

  /**
   * The `skuId` of the current resold subscription. This is populated only when
   * the customer has a subscription with a legacy SKU and the subscription
   * resource is populated with the `skuId` of the SKU recommended for the
   * transfer.
   *
   * @param string $currentLegacySkuId
   */
  public function setCurrentLegacySkuId($currentLegacySkuId)
  {
    $this->currentLegacySkuId = $currentLegacySkuId;
  }
  /**
   * @return string
   */
  public function getCurrentLegacySkuId()
  {
    return $this->currentLegacySkuId;
  }
  /**
   * When inserting a subscription, this is the minimum number of seats listed
   * in the transfer order for this product. For example, if the customer has 20
   * users, the reseller cannot place a transfer order of 15 seats. The minimum
   * is 20 seats.
   *
   * @param int $minimumTransferableSeats
   */
  public function setMinimumTransferableSeats($minimumTransferableSeats)
  {
    $this->minimumTransferableSeats = $minimumTransferableSeats;
  }
  /**
   * @return int
   */
  public function getMinimumTransferableSeats()
  {
    return $this->minimumTransferableSeats;
  }
  /**
   * The time when transfer token or intent to transfer will expire. The time is
   * in milliseconds using UNIX Epoch format.
   *
   * @param string $transferabilityExpirationTime
   */
  public function setTransferabilityExpirationTime($transferabilityExpirationTime)
  {
    $this->transferabilityExpirationTime = $transferabilityExpirationTime;
  }
  /**
   * @return string
   */
  public function getTransferabilityExpirationTime()
  {
    return $this->transferabilityExpirationTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionTransferInfo::class, 'Google_Service_Reseller_SubscriptionTransferInfo');
