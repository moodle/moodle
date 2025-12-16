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

class GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpec extends \Google\Collection
{
  protected $collection_key = 'excludedFilterKeys';
  /**
   * Enables dynamic position for this facet. If set to true, the position of
   * this facet among all facets in the response is determined automatically. If
   * dynamic facets are enabled, it is ordered together. If set to false, the
   * position of this facet in the response is the same as in the request, and
   * it is ranked before the facets with dynamic position enable and all dynamic
   * facets. For example, you may always want to have rating facet returned in
   * the response, but it's not necessarily to always display the rating facet
   * at the top. In that case, you can set enable_dynamic_position to true so
   * that the position of rating facet in response is determined automatically.
   * Another example, assuming you have the following facets in the request: *
   * "rating", enable_dynamic_position = true * "price", enable_dynamic_position
   * = false * "brands", enable_dynamic_position = false And also you have a
   * dynamic facets enabled, which generates a facet `gender`. Then the final
   * order of the facets in the response can be ("price", "brands", "rating",
   * "gender") or ("price", "brands", "gender", "rating") depends on how API
   * orders "gender" and "rating" facets. However, notice that "price" and
   * "brands" are always ranked at first and second position because their
   * enable_dynamic_position is false.
   *
   * @var bool
   */
  public $enableDynamicPosition;
  /**
   * List of keys to exclude when faceting. By default, FacetKey.key is not
   * excluded from the filter unless it is listed in this field. Listing a facet
   * key in this field allows its values to appear as facet results, even when
   * they are filtered out of search results. Using this field does not affect
   * what search results are returned. For example, suppose there are 100
   * documents with the color facet "Red" and 200 documents with the color facet
   * "Blue". A query containing the filter "color:ANY("Red")" and having "color"
   * as FacetKey.key would by default return only "Red" documents in the search
   * results, and also return "Red" with count 100 as the only color facet.
   * Although there are also blue documents available, "Blue" would not be shown
   * as an available facet value. If "color" is listed in "excludedFilterKeys",
   * then the query returns the facet values "Red" with count 100 and "Blue"
   * with count 200, because the "color" key is now excluded from the filter.
   * Because this field doesn't affect search results, the search results are
   * still correctly filtered to return only "Red" documents. A maximum of 100
   * values are allowed. Otherwise, an `INVALID_ARGUMENT` error is returned.
   *
   * @var string[]
   */
  public $excludedFilterKeys;
  protected $facetKeyType = GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpecFacetKey::class;
  protected $facetKeyDataType = '';
  /**
   * Maximum facet values that are returned for this facet. If unspecified,
   * defaults to 20. The maximum allowed value is 300. Values above 300 are
   * coerced to 300. For aggregation in healthcare search, when the
   * [FacetKey.key] is "healthcare_aggregation_key", the limit will be
   * overridden to 10,000 internally, regardless of the value set here. If this
   * field is negative, an `INVALID_ARGUMENT` is returned.
   *
   * @var int
   */
  public $limit;

  /**
   * Enables dynamic position for this facet. If set to true, the position of
   * this facet among all facets in the response is determined automatically. If
   * dynamic facets are enabled, it is ordered together. If set to false, the
   * position of this facet in the response is the same as in the request, and
   * it is ranked before the facets with dynamic position enable and all dynamic
   * facets. For example, you may always want to have rating facet returned in
   * the response, but it's not necessarily to always display the rating facet
   * at the top. In that case, you can set enable_dynamic_position to true so
   * that the position of rating facet in response is determined automatically.
   * Another example, assuming you have the following facets in the request: *
   * "rating", enable_dynamic_position = true * "price", enable_dynamic_position
   * = false * "brands", enable_dynamic_position = false And also you have a
   * dynamic facets enabled, which generates a facet `gender`. Then the final
   * order of the facets in the response can be ("price", "brands", "rating",
   * "gender") or ("price", "brands", "gender", "rating") depends on how API
   * orders "gender" and "rating" facets. However, notice that "price" and
   * "brands" are always ranked at first and second position because their
   * enable_dynamic_position is false.
   *
   * @param bool $enableDynamicPosition
   */
  public function setEnableDynamicPosition($enableDynamicPosition)
  {
    $this->enableDynamicPosition = $enableDynamicPosition;
  }
  /**
   * @return bool
   */
  public function getEnableDynamicPosition()
  {
    return $this->enableDynamicPosition;
  }
  /**
   * List of keys to exclude when faceting. By default, FacetKey.key is not
   * excluded from the filter unless it is listed in this field. Listing a facet
   * key in this field allows its values to appear as facet results, even when
   * they are filtered out of search results. Using this field does not affect
   * what search results are returned. For example, suppose there are 100
   * documents with the color facet "Red" and 200 documents with the color facet
   * "Blue". A query containing the filter "color:ANY("Red")" and having "color"
   * as FacetKey.key would by default return only "Red" documents in the search
   * results, and also return "Red" with count 100 as the only color facet.
   * Although there are also blue documents available, "Blue" would not be shown
   * as an available facet value. If "color" is listed in "excludedFilterKeys",
   * then the query returns the facet values "Red" with count 100 and "Blue"
   * with count 200, because the "color" key is now excluded from the filter.
   * Because this field doesn't affect search results, the search results are
   * still correctly filtered to return only "Red" documents. A maximum of 100
   * values are allowed. Otherwise, an `INVALID_ARGUMENT` error is returned.
   *
   * @param string[] $excludedFilterKeys
   */
  public function setExcludedFilterKeys($excludedFilterKeys)
  {
    $this->excludedFilterKeys = $excludedFilterKeys;
  }
  /**
   * @return string[]
   */
  public function getExcludedFilterKeys()
  {
    return $this->excludedFilterKeys;
  }
  /**
   * Required. The facet key specification.
   *
   * @param GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpecFacetKey $facetKey
   */
  public function setFacetKey(GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpecFacetKey $facetKey)
  {
    $this->facetKey = $facetKey;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpecFacetKey
   */
  public function getFacetKey()
  {
    return $this->facetKey;
  }
  /**
   * Maximum facet values that are returned for this facet. If unspecified,
   * defaults to 20. The maximum allowed value is 300. Values above 300 are
   * coerced to 300. For aggregation in healthcare search, when the
   * [FacetKey.key] is "healthcare_aggregation_key", the limit will be
   * overridden to 10,000 internally, regardless of the value set here. If this
   * field is negative, an `INVALID_ARGUMENT` is returned.
   *
   * @param int $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return int
   */
  public function getLimit()
  {
    return $this->limit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpec');
