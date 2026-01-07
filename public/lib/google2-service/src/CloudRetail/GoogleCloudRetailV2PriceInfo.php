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

class GoogleCloudRetailV2PriceInfo extends \Google\Model
{
  /**
   * The costs associated with the sale of a particular product. Used for gross
   * profit reporting. * Profit = price - cost Google Merchant Center property
   * [cost_of_goods_sold](https://support.google.com/merchants/answer/9017895).
   *
   * @var float
   */
  public $cost;
  /**
   * The 3-letter currency code defined in [ISO
   * 4217](https://www.iso.org/iso-4217-currency-codes.html). If this field is
   * an unrecognizable currency code, an INVALID_ARGUMENT error is returned. The
   * Product.Type.VARIANT Products with the same Product.primary_product_id must
   * share the same currency_code. Otherwise, a FAILED_PRECONDITION error is
   * returned.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Price of the product without any discount. If zero, by default set to be
   * the price. If set, original_price should be greater than or equal to price,
   * otherwise an INVALID_ARGUMENT error is thrown.
   *
   * @var float
   */
  public $originalPrice;
  /**
   * Price of the product. Google Merchant Center property
   * [price](https://support.google.com/merchants/answer/6324371). Schema.org
   * property [Offer.price](https://schema.org/price).
   *
   * @var float
   */
  public $price;
  /**
   * The timestamp when the price starts to be effective. This can be set as a
   * future timestamp, and the price is only used for search after
   * price_effective_time. If so, the original_price must be set and
   * original_price is used before price_effective_time. Do not set if price is
   * always effective because it will cause additional latency during search.
   *
   * @var string
   */
  public $priceEffectiveTime;
  /**
   * The timestamp when the price stops to be effective. The price is used for
   * search before price_expire_time. If this field is set, the original_price
   * must be set and original_price is used after price_expire_time. Do not set
   * if price is always effective because it will cause additional latency
   * during search.
   *
   * @var string
   */
  public $priceExpireTime;
  protected $priceRangeType = GoogleCloudRetailV2PriceInfoPriceRange::class;
  protected $priceRangeDataType = '';

  /**
   * The costs associated with the sale of a particular product. Used for gross
   * profit reporting. * Profit = price - cost Google Merchant Center property
   * [cost_of_goods_sold](https://support.google.com/merchants/answer/9017895).
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
   * The 3-letter currency code defined in [ISO
   * 4217](https://www.iso.org/iso-4217-currency-codes.html). If this field is
   * an unrecognizable currency code, an INVALID_ARGUMENT error is returned. The
   * Product.Type.VARIANT Products with the same Product.primary_product_id must
   * share the same currency_code. Otherwise, a FAILED_PRECONDITION error is
   * returned.
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
   * Price of the product without any discount. If zero, by default set to be
   * the price. If set, original_price should be greater than or equal to price,
   * otherwise an INVALID_ARGUMENT error is thrown.
   *
   * @param float $originalPrice
   */
  public function setOriginalPrice($originalPrice)
  {
    $this->originalPrice = $originalPrice;
  }
  /**
   * @return float
   */
  public function getOriginalPrice()
  {
    return $this->originalPrice;
  }
  /**
   * Price of the product. Google Merchant Center property
   * [price](https://support.google.com/merchants/answer/6324371). Schema.org
   * property [Offer.price](https://schema.org/price).
   *
   * @param float $price
   */
  public function setPrice($price)
  {
    $this->price = $price;
  }
  /**
   * @return float
   */
  public function getPrice()
  {
    return $this->price;
  }
  /**
   * The timestamp when the price starts to be effective. This can be set as a
   * future timestamp, and the price is only used for search after
   * price_effective_time. If so, the original_price must be set and
   * original_price is used before price_effective_time. Do not set if price is
   * always effective because it will cause additional latency during search.
   *
   * @param string $priceEffectiveTime
   */
  public function setPriceEffectiveTime($priceEffectiveTime)
  {
    $this->priceEffectiveTime = $priceEffectiveTime;
  }
  /**
   * @return string
   */
  public function getPriceEffectiveTime()
  {
    return $this->priceEffectiveTime;
  }
  /**
   * The timestamp when the price stops to be effective. The price is used for
   * search before price_expire_time. If this field is set, the original_price
   * must be set and original_price is used after price_expire_time. Do not set
   * if price is always effective because it will cause additional latency
   * during search.
   *
   * @param string $priceExpireTime
   */
  public function setPriceExpireTime($priceExpireTime)
  {
    $this->priceExpireTime = $priceExpireTime;
  }
  /**
   * @return string
   */
  public function getPriceExpireTime()
  {
    return $this->priceExpireTime;
  }
  /**
   * Output only. The price range of all the child Product.Type.VARIANT Products
   * grouped together on the Product.Type.PRIMARY Product. Only populated for
   * Product.Type.PRIMARY Products. Note: This field is OUTPUT_ONLY for
   * ProductService.GetProduct. Do not set this field in API requests.
   *
   * @param GoogleCloudRetailV2PriceInfoPriceRange $priceRange
   */
  public function setPriceRange(GoogleCloudRetailV2PriceInfoPriceRange $priceRange)
  {
    $this->priceRange = $priceRange;
  }
  /**
   * @return GoogleCloudRetailV2PriceInfoPriceRange
   */
  public function getPriceRange()
  {
    return $this->priceRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2PriceInfo::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2PriceInfo');
