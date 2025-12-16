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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2PurchaseTransaction extends \Google\Model
{
  /**
   * All the costs associated with the products. These can be manufacturing
   * costs, shipping expenses not borne by the end user, or any other costs,
   * such that: * Profit = revenue - tax - cost
   *
   * @var float
   */
  public $cost;
  /**
   * Required. Currency code. Use three-character ISO-4217 code.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * The transaction ID with a length limit of 128 characters.
   *
   * @var string
   */
  public $id;
  /**
   * Required. Total non-zero revenue or grand total associated with the
   * transaction. This value include shipping, tax, or other adjustments to
   * total revenue that you want to include as part of your revenue
   * calculations.
   *
   * @var float
   */
  public $revenue;
  /**
   * All the taxes associated with the transaction.
   *
   * @var float
   */
  public $tax;

  /**
   * All the costs associated with the products. These can be manufacturing
   * costs, shipping expenses not borne by the end user, or any other costs,
   * such that: * Profit = revenue - tax - cost
   *
   * @param float $cost
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
   * Required. Currency code. Use three-character ISO-4217 code.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * The transaction ID with a length limit of 128 characters.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. Total non-zero revenue or grand total associated with the
   * transaction. This value include shipping, tax, or other adjustments to
   * total revenue that you want to include as part of your revenue
   * calculations.
   *
   * @param float $revenue
   */
  public function setRevenue($revenue)
  {
    $this->revenue = $revenue;
  }
  /**
   * @return float
   */
  public function getRevenue()
  {
    return $this->revenue;
  }
  /**
   * All the taxes associated with the transaction.
   *
   * @param float $tax
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2PurchaseTransaction::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2PurchaseTransaction');
