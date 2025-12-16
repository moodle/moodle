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

class GoogleCloudRecommendationengineV1beta1ProductCatalogItem extends \Google\Collection
{
  /**
   * Default item stock status. Should never be used.
   */
  public const STOCK_STATE_STOCK_STATE_UNSPECIFIED = 'STOCK_STATE_UNSPECIFIED';
  /**
   * Item in stock.
   */
  public const STOCK_STATE_IN_STOCK = 'IN_STOCK';
  /**
   * Item out of stock.
   */
  public const STOCK_STATE_OUT_OF_STOCK = 'OUT_OF_STOCK';
  /**
   * Item that is in pre-order state.
   */
  public const STOCK_STATE_PREORDER = 'PREORDER';
  /**
   * Item that is back-ordered (i.e. temporarily out of stock).
   */
  public const STOCK_STATE_BACKORDER = 'BACKORDER';
  protected $collection_key = 'images';
  /**
   * Optional. The available quantity of the item.
   *
   * @var string
   */
  public $availableQuantity;
  /**
   * Optional. Canonical URL directly linking to the item detail page with a
   * length limit of 5 KiB..
   *
   * @var string
   */
  public $canonicalProductUri;
  /**
   * Optional. A map to pass the costs associated with the product. For example:
   * {"manufacturing": 45.5} The profit of selling this item is computed like
   * so: * If 'exactPrice' is provided, profit = displayPrice - sum(costs) * If
   * 'priceRange' is provided, profit = minPrice - sum(costs)
   *
   * @var float[]
   */
  public $costs;
  /**
   * Optional. Only required if the price is set. Currency code for price/costs.
   * Use three-character ISO-4217 code.
   *
   * @var string
   */
  public $currencyCode;
  protected $exactPriceType = GoogleCloudRecommendationengineV1beta1ProductCatalogItemExactPrice::class;
  protected $exactPriceDataType = '';
  protected $imagesType = GoogleCloudRecommendationengineV1beta1Image::class;
  protected $imagesDataType = 'array';
  protected $priceRangeType = GoogleCloudRecommendationengineV1beta1ProductCatalogItemPriceRange::class;
  protected $priceRangeDataType = '';
  /**
   * Optional. Online stock state of the catalog item. Default is `IN_STOCK`.
   *
   * @var string
   */
  public $stockState;

  /**
   * Optional. The available quantity of the item.
   *
   * @param string $availableQuantity
   */
  public function setAvailableQuantity($availableQuantity)
  {
    $this->availableQuantity = $availableQuantity;
  }
  /**
   * @return string
   */
  public function getAvailableQuantity()
  {
    return $this->availableQuantity;
  }
  /**
   * Optional. Canonical URL directly linking to the item detail page with a
   * length limit of 5 KiB..
   *
   * @param string $canonicalProductUri
   */
  public function setCanonicalProductUri($canonicalProductUri)
  {
    $this->canonicalProductUri = $canonicalProductUri;
  }
  /**
   * @return string
   */
  public function getCanonicalProductUri()
  {
    return $this->canonicalProductUri;
  }
  /**
   * Optional. A map to pass the costs associated with the product. For example:
   * {"manufacturing": 45.5} The profit of selling this item is computed like
   * so: * If 'exactPrice' is provided, profit = displayPrice - sum(costs) * If
   * 'priceRange' is provided, profit = minPrice - sum(costs)
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
   * Optional. Only required if the price is set. Currency code for price/costs.
   * Use three-character ISO-4217 code.
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
   * Optional. The exact product price.
   *
   * @param GoogleCloudRecommendationengineV1beta1ProductCatalogItemExactPrice $exactPrice
   */
  public function setExactPrice(GoogleCloudRecommendationengineV1beta1ProductCatalogItemExactPrice $exactPrice)
  {
    $this->exactPrice = $exactPrice;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1ProductCatalogItemExactPrice
   */
  public function getExactPrice()
  {
    return $this->exactPrice;
  }
  /**
   * Optional. Product images for the catalog item.
   *
   * @param GoogleCloudRecommendationengineV1beta1Image[] $images
   */
  public function setImages($images)
  {
    $this->images = $images;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1Image[]
   */
  public function getImages()
  {
    return $this->images;
  }
  /**
   * Optional. The product price range.
   *
   * @param GoogleCloudRecommendationengineV1beta1ProductCatalogItemPriceRange $priceRange
   */
  public function setPriceRange(GoogleCloudRecommendationengineV1beta1ProductCatalogItemPriceRange $priceRange)
  {
    $this->priceRange = $priceRange;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1ProductCatalogItemPriceRange
   */
  public function getPriceRange()
  {
    return $this->priceRange;
  }
  /**
   * Optional. Online stock state of the catalog item. Default is `IN_STOCK`.
   *
   * Accepted values: STOCK_STATE_UNSPECIFIED, IN_STOCK, OUT_OF_STOCK, PREORDER,
   * BACKORDER
   *
   * @param self::STOCK_STATE_* $stockState
   */
  public function setStockState($stockState)
  {
    $this->stockState = $stockState;
  }
  /**
   * @return self::STOCK_STATE_*
   */
  public function getStockState()
  {
    return $this->stockState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1ProductCatalogItem::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1ProductCatalogItem');
