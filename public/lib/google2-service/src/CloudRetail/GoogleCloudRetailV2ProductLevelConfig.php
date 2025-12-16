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

class GoogleCloudRetailV2ProductLevelConfig extends \Google\Model
{
  /**
   * The type of Products allowed to be ingested into the catalog. Acceptable
   * values are: * `primary` (default): You can ingest Products of all types.
   * When ingesting a Product, its type will default to Product.Type.PRIMARY if
   * unset. * `variant` (incompatible with Retail Search): You can only ingest
   * Product.Type.VARIANT Products. This means Product.primary_product_id cannot
   * be empty. If this field is set to an invalid value other than these, an
   * INVALID_ARGUMENT error is returned. If this field is `variant` and
   * merchant_center_product_id_field is `itemGroupId`, an INVALID_ARGUMENT
   * error is returned. See [Product
   * levels](https://cloud.google.com/retail/docs/catalog#product-levels) for
   * more details.
   *
   * @var string
   */
  public $ingestionProductType;
  /**
   * Which field of [Merchant Center Product](/bigquery-transfer/docs/merchant-
   * center-products-schema) should be imported as Product.id. Acceptable values
   * are: * `offerId` (default): Import `offerId` as the product ID. *
   * `itemGroupId`: Import `itemGroupId` as the product ID. Notice that Retail
   * API will choose one item from the ones with the same `itemGroupId`, and use
   * it to represent the item group. If this field is set to an invalid value
   * other than these, an INVALID_ARGUMENT error is returned. If this field is
   * `itemGroupId` and ingestion_product_type is `variant`, an INVALID_ARGUMENT
   * error is returned. See [Product
   * levels](https://cloud.google.com/retail/docs/catalog#product-levels) for
   * more details.
   *
   * @var string
   */
  public $merchantCenterProductIdField;

  /**
   * The type of Products allowed to be ingested into the catalog. Acceptable
   * values are: * `primary` (default): You can ingest Products of all types.
   * When ingesting a Product, its type will default to Product.Type.PRIMARY if
   * unset. * `variant` (incompatible with Retail Search): You can only ingest
   * Product.Type.VARIANT Products. This means Product.primary_product_id cannot
   * be empty. If this field is set to an invalid value other than these, an
   * INVALID_ARGUMENT error is returned. If this field is `variant` and
   * merchant_center_product_id_field is `itemGroupId`, an INVALID_ARGUMENT
   * error is returned. See [Product
   * levels](https://cloud.google.com/retail/docs/catalog#product-levels) for
   * more details.
   *
   * @param string $ingestionProductType
   */
  public function setIngestionProductType($ingestionProductType)
  {
    $this->ingestionProductType = $ingestionProductType;
  }
  /**
   * @return string
   */
  public function getIngestionProductType()
  {
    return $this->ingestionProductType;
  }
  /**
   * Which field of [Merchant Center Product](/bigquery-transfer/docs/merchant-
   * center-products-schema) should be imported as Product.id. Acceptable values
   * are: * `offerId` (default): Import `offerId` as the product ID. *
   * `itemGroupId`: Import `itemGroupId` as the product ID. Notice that Retail
   * API will choose one item from the ones with the same `itemGroupId`, and use
   * it to represent the item group. If this field is set to an invalid value
   * other than these, an INVALID_ARGUMENT error is returned. If this field is
   * `itemGroupId` and ingestion_product_type is `variant`, an INVALID_ARGUMENT
   * error is returned. See [Product
   * levels](https://cloud.google.com/retail/docs/catalog#product-levels) for
   * more details.
   *
   * @param string $merchantCenterProductIdField
   */
  public function setMerchantCenterProductIdField($merchantCenterProductIdField)
  {
    $this->merchantCenterProductIdField = $merchantCenterProductIdField;
  }
  /**
   * @return string
   */
  public function getMerchantCenterProductIdField()
  {
    return $this->merchantCenterProductIdField;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ProductLevelConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ProductLevelConfig');
