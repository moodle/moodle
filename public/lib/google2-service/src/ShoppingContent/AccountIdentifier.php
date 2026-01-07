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

namespace Google\Service\ShoppingContent;

class AccountIdentifier extends \Google\Model
{
  /**
   * The aggregator ID, set for aggregators and subaccounts (in that case, it
   * represents the aggregator of the subaccount).
   *
   * @var string
   */
  public $aggregatorId;
  /**
   * The merchant account ID, set for individual accounts and subaccounts.
   *
   * @var string
   */
  public $merchantId;

  /**
   * The aggregator ID, set for aggregators and subaccounts (in that case, it
   * represents the aggregator of the subaccount).
   *
   * @param string $aggregatorId
   */
  public function setAggregatorId($aggregatorId)
  {
    $this->aggregatorId = $aggregatorId;
  }
  /**
   * @return string
   */
  public function getAggregatorId()
  {
    return $this->aggregatorId;
  }
  /**
   * The merchant account ID, set for individual accounts and subaccounts.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountIdentifier::class, 'Google_Service_ShoppingContent_AccountIdentifier');
