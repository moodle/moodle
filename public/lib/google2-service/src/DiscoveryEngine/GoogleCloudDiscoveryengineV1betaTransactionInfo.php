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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaTransactionInfo extends \Google\Model
{
  /**
   * @var float
   */
  public $cost;
  /**
   * @var string
   */
  public $currency;
  /**
   * @var float
   */
  public $discountValue;
  /**
   * @var float
   */
  public $tax;
  /**
   * @var string
   */
  public $transactionId;
  /**
   * @var float
   */
  public $value;

  /**
   * @param float
   */
  public function setCost($cost)
  {
    $this->cost = $cost;
  }
  /**
   * @return float
   */
  public function getCost()
  {
    return $this->cost;
  }
  /**
   * @param string
   */
  public function setCurrency($currency)
  {
    $this->currency = $currency;
  }
  /**
   * @return string
   */
  public function getCurrency()
  {
    return $this->currency;
  }
  /**
   * @param float
   */
  public function setDiscountValue($discountValue)
  {
    $this->discountValue = $discountValue;
  }
  /**
   * @return float
   */
  public function getDiscountValue()
  {
    return $this->discountValue;
  }
  /**
   * @param float
   */
  public function setTax($tax)
  {
    $this->tax = $tax;
  }
  /**
   * @return float
   */
  public function getTax()
  {
    return $this->tax;
  }
  /**
   * @param string
   */
  public function setTransactionId($transactionId)
  {
    $this->transactionId = $transactionId;
  }
  /**
   * @return string
   */
  public function getTransactionId()
  {
    return $this->transactionId;
  }
  /**
   * @param float
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return float
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaTransactionInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaTransactionInfo');
