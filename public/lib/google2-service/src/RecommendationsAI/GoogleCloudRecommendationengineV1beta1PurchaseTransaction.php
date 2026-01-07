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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1PurchaseTransaction extends \Google\Model
{
  /**
   * Optional. All the costs associated with the product. These can be
   * manufacturing costs, shipping expenses not borne by the end user, or any
   * other costs. Total product cost such that profit = revenue - (sum(taxes) +
   * sum(costs)) If product_cost is not set, then profit = revenue - tax -
   * shipping - sum(CatalogItem.costs). If CatalogItem.cost is not specified for
   * one of the items, CatalogItem.cost based profit *cannot* be calculated for
   * this Transaction.
   *
   * @var float[]
   */
  public $costs;
  /**
   * Required. Currency code. Use three-character ISO-4217 code. This field is
   * not required if the event type is `refund`.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Optional. The transaction ID with a length limit of 128 bytes.
   *
   * @var string
   */
  public $id;
  /**
   * Required. Total revenue or grand total associated with the transaction.
   * This value include shipping, tax, or other adjustments to total revenue
   * that you want to include as part of your revenue calculations. This field
   * is not required if the event type is `refund`.
   *
   * @var float
   */
  public $revenue;
  /**
   * Optional. All the taxes associated with the transaction.
   *
   * @var float[]
   */
  public $taxes;

  /**
   * Optional. All the costs associated with the product. These can be
   * manufacturing costs, shipping expenses not borne by the end user, or any
   * other costs. Total product cost such that profit = revenue - (sum(taxes) +
   * sum(costs)) If product_cost is not set, then profit = revenue - tax -
   * shipping - sum(CatalogItem.costs). If CatalogItem.cost is not specified for
   * one of the items, CatalogItem.cost based profit *cannot* be calculated for
   * this Transaction.
   *
   * @param float[] $costs
   */
  public function setCosts($costs)
  {
    $this->costs = $costs;
  }
  /**
   * @return float[]
   */
  public function getCosts()
  {
    return $this->costs;
  }
  /**
   * Required. Currency code. Use three-character ISO-4217 code. This field is
   * not required if the event type is `refund`.
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
   * Optional. The transaction ID with a length limit of 128 bytes.
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
   * Required. Total revenue or grand total associated with the transaction.
   * This value include shipping, tax, or other adjustments to total revenue
   * that you want to include as part of your revenue calculations. This field
   * is not required if the event type is `refund`.
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
   * Optional. All the taxes associated with the transaction.
   *
   * @param float[] $taxes
   */
  public function setTaxes($taxes)
  {
    $this->taxes = $taxes;
  }
  /**
   * @return float[]
   */
  public function getTaxes()
  {
    return $this->taxes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1PurchaseTransaction::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1PurchaseTransaction');
