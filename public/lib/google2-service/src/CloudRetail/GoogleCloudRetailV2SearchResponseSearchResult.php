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

class GoogleCloudRetailV2SearchResponseSearchResult extends \Google\Collection
{
  protected $collection_key = 'personalLabels';
  /**
   * Product.id of the searched Product.
   *
   * @var string
   */
  public $id;
  /**
   * The count of matched variant Products.
   *
   * @var int
   */
  public $matchingVariantCount;
  /**
   * If a variant Product matches the search query, this map indicates which
   * Product fields are matched. The key is the Product.name, the value is a
   * field mask of the matched Product fields. If matched attributes cannot be
   * determined, this map will be empty. For example, a key "sku1" with field
   * mask "products.color_info" indicates there is a match between "sku1"
   * ColorInfo and the query.
   *
   * @var string[]
   */
  public $matchingVariantFields;
  protected $modelScoresType = GoogleCloudRetailV2DoubleList::class;
  protected $modelScoresDataType = 'map';
  /**
   * Specifies previous events related to this product for this user based on
   * UserEvent with same SearchRequest.visitor_id or UserInfo.user_id. This is
   * set only when SearchRequest.PersonalizationSpec.mode is
   * SearchRequest.PersonalizationSpec.Mode.AUTO. Possible values: *
   * `purchased`: Indicates that this product has been purchased before.
   *
   * @var string[]
   */
  public $personalLabels;
  protected $productType = GoogleCloudRetailV2Product::class;
  protected $productDataType = '';
  /**
   * The rollup matching variant Product attributes. The key is one of the
   * SearchRequest.variant_rollup_keys. The values are the merged and de-
   * duplicated Product attributes. Notice that the rollup values are respect
   * filter. For example, when filtering by "colorFamilies:ANY(\"red\")" and
   * rollup "colorFamilies", only "red" is returned. For textual and numerical
   * attributes, the rollup values is a list of string or double values with
   * type google.protobuf.ListValue. For example, if there are two variants with
   * colors "red" and "blue", the rollup values are { key: "colorFamilies" value
   * { list_value { values { string_value: "red" } values { string_value: "blue"
   * } } } } For FulfillmentInfo, the rollup values is a double value with type
   * google.protobuf.Value. For example, `{key: "pickupInStore.store1" value {
   * number_value: 10 }}` means a there are 10 variants in this product are
   * available in the store "store1".
   *
   * @var array[]
   */
  public $variantRollupValues;

  /**
   * Product.id of the searched Product.
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
   * The count of matched variant Products.
   *
   * @param int $matchingVariantCount
   */
  public function setMatchingVariantCount($matchingVariantCount)
  {
    $this->matchingVariantCount = $matchingVariantCount;
  }
  /**
   * @return int
   */
  public function getMatchingVariantCount()
  {
    return $this->matchingVariantCount;
  }
  /**
   * If a variant Product matches the search query, this map indicates which
   * Product fields are matched. The key is the Product.name, the value is a
   * field mask of the matched Product fields. If matched attributes cannot be
   * determined, this map will be empty. For example, a key "sku1" with field
   * mask "products.color_info" indicates there is a match between "sku1"
   * ColorInfo and the query.
   *
   * @param string[] $matchingVariantFields
   */
  public function setMatchingVariantFields($matchingVariantFields)
  {
    $this->matchingVariantFields = $matchingVariantFields;
  }
  /**
   * @return string[]
   */
  public function getMatchingVariantFields()
  {
    return $this->matchingVariantFields;
  }
  /**
   * Google provided available scores.
   *
   * @param GoogleCloudRetailV2DoubleList[] $modelScores
   */
  public function setModelScores($modelScores)
  {
    $this->modelScores = $modelScores;
  }
  /**
   * @return GoogleCloudRetailV2DoubleList[]
   */
  public function getModelScores()
  {
    return $this->modelScores;
  }
  /**
   * Specifies previous events related to this product for this user based on
   * UserEvent with same SearchRequest.visitor_id or UserInfo.user_id. This is
   * set only when SearchRequest.PersonalizationSpec.mode is
   * SearchRequest.PersonalizationSpec.Mode.AUTO. Possible values: *
   * `purchased`: Indicates that this product has been purchased before.
   *
   * @param string[] $personalLabels
   */
  public function setPersonalLabels($personalLabels)
  {
    $this->personalLabels = $personalLabels;
  }
  /**
   * @return string[]
   */
  public function getPersonalLabels()
  {
    return $this->personalLabels;
  }
  /**
   * The product data snippet in the search response. Only Product.name is
   * guaranteed to be populated. Product.variants contains the product variants
   * that match the search query. If there are multiple product variants
   * matching the query, top 5 most relevant product variants are returned and
   * ordered by relevancy. If relevancy can be deternmined, use
   * matching_variant_fields to look up matched product variants fields. If
   * relevancy cannot be determined, e.g. when searching "shoe" all products in
   * a shoe product can be a match, 5 product variants are returned but order is
   * meaningless.
   *
   * @param GoogleCloudRetailV2Product $product
   */
  public function setProduct(GoogleCloudRetailV2Product $product)
  {
    $this->product = $product;
  }
  /**
   * @return GoogleCloudRetailV2Product
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * The rollup matching variant Product attributes. The key is one of the
   * SearchRequest.variant_rollup_keys. The values are the merged and de-
   * duplicated Product attributes. Notice that the rollup values are respect
   * filter. For example, when filtering by "colorFamilies:ANY(\"red\")" and
   * rollup "colorFamilies", only "red" is returned. For textual and numerical
   * attributes, the rollup values is a list of string or double values with
   * type google.protobuf.ListValue. For example, if there are two variants with
   * colors "red" and "blue", the rollup values are { key: "colorFamilies" value
   * { list_value { values { string_value: "red" } values { string_value: "blue"
   * } } } } For FulfillmentInfo, the rollup values is a double value with type
   * google.protobuf.Value. For example, `{key: "pickupInStore.store1" value {
   * number_value: 10 }}` means a there are 10 variants in this product are
   * available in the store "store1".
   *
   * @param array[] $variantRollupValues
   */
  public function setVariantRollupValues($variantRollupValues)
  {
    $this->variantRollupValues = $variantRollupValues;
  }
  /**
   * @return array[]
   */
  public function getVariantRollupValues()
  {
    return $this->variantRollupValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchResponseSearchResult::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchResponseSearchResult');
