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

class GoogleCloudRetailV2ModelFrequentlyBoughtTogetherFeaturesConfig extends \Google\Model
{
  /**
   * Unspecified default value, should never be explicitly set. Defaults to
   * MULTIPLE_CONTEXT_PRODUCTS.
   */
  public const CONTEXT_PRODUCTS_TYPE_CONTEXT_PRODUCTS_TYPE_UNSPECIFIED = 'CONTEXT_PRODUCTS_TYPE_UNSPECIFIED';
  /**
   * Use only a single product as context for the recommendation. Typically used
   * on pages like add-to-cart or product details.
   */
  public const CONTEXT_PRODUCTS_TYPE_SINGLE_CONTEXT_PRODUCT = 'SINGLE_CONTEXT_PRODUCT';
  /**
   * Use one or multiple products as context for the recommendation. Typically
   * used on shopping cart pages.
   */
  public const CONTEXT_PRODUCTS_TYPE_MULTIPLE_CONTEXT_PRODUCTS = 'MULTIPLE_CONTEXT_PRODUCTS';
  /**
   * Optional. Specifies the context of the model when it is used in predict
   * requests. Can only be set for the `frequently-bought-together` type. If it
   * isn't specified, it defaults to MULTIPLE_CONTEXT_PRODUCTS.
   *
   * @var string
   */
  public $contextProductsType;

  /**
   * Optional. Specifies the context of the model when it is used in predict
   * requests. Can only be set for the `frequently-bought-together` type. If it
   * isn't specified, it defaults to MULTIPLE_CONTEXT_PRODUCTS.
   *
   * Accepted values: CONTEXT_PRODUCTS_TYPE_UNSPECIFIED, SINGLE_CONTEXT_PRODUCT,
   * MULTIPLE_CONTEXT_PRODUCTS
   *
   * @param self::CONTEXT_PRODUCTS_TYPE_* $contextProductsType
   */
  public function setContextProductsType($contextProductsType)
  {
    $this->contextProductsType = $contextProductsType;
  }
  /**
   * @return self::CONTEXT_PRODUCTS_TYPE_*
   */
  public function getContextProductsType()
  {
    return $this->contextProductsType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ModelFrequentlyBoughtTogetherFeaturesConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ModelFrequentlyBoughtTogetherFeaturesConfig');
