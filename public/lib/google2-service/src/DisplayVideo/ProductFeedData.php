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

namespace Google\Service\DisplayVideo;

class ProductFeedData extends \Google\Collection
{
  /**
   * Not specified or unknown.
   */
  public const PRODUCT_MATCH_TYPE_PRODUCT_MATCH_TYPE_UNSPECIFIED = 'PRODUCT_MATCH_TYPE_UNSPECIFIED';
  /**
   * All the products are matched.
   */
  public const PRODUCT_MATCH_TYPE_PRODUCT_MATCH_TYPE_ALL_PRODUCTS = 'PRODUCT_MATCH_TYPE_ALL_PRODUCTS';
  /**
   * Specific products are selected.
   */
  public const PRODUCT_MATCH_TYPE_PRODUCT_MATCH_TYPE_SPECIFIC_PRODUCTS = 'PRODUCT_MATCH_TYPE_SPECIFIC_PRODUCTS';
  /**
   * Match products by their custom labels.
   */
  public const PRODUCT_MATCH_TYPE_PRODUCT_MATCH_TYPE_CUSTOM_LABEL = 'PRODUCT_MATCH_TYPE_CUSTOM_LABEL';
  protected $collection_key = 'productMatchDimensions';
  /**
   * Whether the product feed has opted-out of showing products.
   *
   * @var bool
   */
  public $isFeedDisabled;
  protected $productMatchDimensionsType = ProductMatchDimension::class;
  protected $productMatchDimensionsDataType = 'array';
  /**
   * How products are selected by the product feed.
   *
   * @var string
   */
  public $productMatchType;

  /**
   * Whether the product feed has opted-out of showing products.
   *
   * @param bool $isFeedDisabled
   */
  public function setIsFeedDisabled($isFeedDisabled)
  {
    $this->isFeedDisabled = $isFeedDisabled;
  }
  /**
   * @return bool
   */
  public function getIsFeedDisabled()
  {
    return $this->isFeedDisabled;
  }
  /**
   * A list of dimensions used to match products.
   *
   * @param ProductMatchDimension[] $productMatchDimensions
   */
  public function setProductMatchDimensions($productMatchDimensions)
  {
    $this->productMatchDimensions = $productMatchDimensions;
  }
  /**
   * @return ProductMatchDimension[]
   */
  public function getProductMatchDimensions()
  {
    return $this->productMatchDimensions;
  }
  /**
   * How products are selected by the product feed.
   *
   * Accepted values: PRODUCT_MATCH_TYPE_UNSPECIFIED,
   * PRODUCT_MATCH_TYPE_ALL_PRODUCTS, PRODUCT_MATCH_TYPE_SPECIFIC_PRODUCTS,
   * PRODUCT_MATCH_TYPE_CUSTOM_LABEL
   *
   * @param self::PRODUCT_MATCH_TYPE_* $productMatchType
   */
  public function setProductMatchType($productMatchType)
  {
    $this->productMatchType = $productMatchType;
  }
  /**
   * @return self::PRODUCT_MATCH_TYPE_*
   */
  public function getProductMatchType()
  {
    return $this->productMatchType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductFeedData::class, 'Google_Service_DisplayVideo_ProductFeedData');
