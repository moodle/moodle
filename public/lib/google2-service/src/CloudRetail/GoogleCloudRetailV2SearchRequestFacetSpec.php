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

class GoogleCloudRetailV2SearchRequestFacetSpec extends \Google\Collection
{
  protected $collection_key = 'excludedFilterKeys';
  /**
   * Enables dynamic position for this facet. If set to true, the position of
   * this facet among all facets in the response is determined by Google Retail
   * Search. It is ordered together with dynamic facets if dynamic facets is
   * enabled. If set to false, the position of this facet in the response is the
   * same as in the request, and it is ranked before the facets with dynamic
   * position enable and all dynamic facets. For example, you may always want to
   * have rating facet returned in the response, but it's not necessarily to
   * always display the rating facet at the top. In that case, you can set
   * enable_dynamic_position to true so that the position of rating facet in
   * response is determined by Google Retail Search. Another example, assuming
   * you have the following facets in the request: * "rating",
   * enable_dynamic_position = true * "price", enable_dynamic_position = false *
   * "brands", enable_dynamic_position = false And also you have a dynamic
   * facets enable, which generates a facet "gender". Then, the final order of
   * the facets in the response can be ("price", "brands", "rating", "gender")
   * or ("price", "brands", "gender", "rating") depends on how Google Retail
   * Search orders "gender" and "rating" facets. However, notice that "price"
   * and "brands" are always ranked at first and second position because their
   * enable_dynamic_position values are false.
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
   * products with the color facet "Red" and 200 products with the color facet
   * "Blue". A query containing the filter "colorFamilies:ANY("Red")" and having
   * "colorFamilies" as FacetKey.key would by default return only "Red" products
   * in the search results, and also return "Red" with count 100 as the only
   * color facet. Although there are also blue products available, "Blue" would
   * not be shown as an available facet value. If "colorFamilies" is listed in
   * "excludedFilterKeys", then the query returns the facet values "Red" with
   * count 100 and "Blue" with count 200, because the "colorFamilies" key is now
   * excluded from the filter. Because this field doesn't affect search results,
   * the search results are still correctly filtered to return only "Red"
   * products. A maximum of 100 values are allowed. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @var string[]
   */
  public $excludedFilterKeys;
  protected $facetKeyType = GoogleCloudRetailV2SearchRequestFacetSpecFacetKey::class;
  protected $facetKeyDataType = '';
  /**
   * Maximum of facet values that should be returned for this facet. If
   * unspecified, defaults to 50. The maximum allowed value is 300. Values above
   * 300 will be coerced to 300. If this field is negative, an INVALID_ARGUMENT
   * is returned.
   *
   * @var int
   */
  public $limit;

  /**
   * Enables dynamic position for this facet. If set to true, the position of
   * this facet among all facets in the response is determined by Google Retail
   * Search. It is ordered together with dynamic facets if dynamic facets is
   * enabled. If set to false, the position of this facet in the response is the
   * same as in the request, and it is ranked before the facets with dynamic
   * position enable and all dynamic facets. For example, you may always want to
   * have rating facet returned in the response, but it's not necessarily to
   * always display the rating facet at the top. In that case, you can set
   * enable_dynamic_position to true so that the position of rating facet in
   * response is determined by Google Retail Search. Another example, assuming
   * you have the following facets in the request: * "rating",
   * enable_dynamic_position = true * "price", enable_dynamic_position = false *
   * "brands", enable_dynamic_position = false And also you have a dynamic
   * facets enable, which generates a facet "gender". Then, the final order of
   * the facets in the response can be ("price", "brands", "rating", "gender")
   * or ("price", "brands", "gender", "rating") depends on how Google Retail
   * Search orders "gender" and "rating" facets. However, notice that "price"
   * and "brands" are always ranked at first and second position because their
   * enable_dynamic_position values are false.
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
   * products with the color facet "Red" and 200 products with the color facet
   * "Blue". A query containing the filter "colorFamilies:ANY("Red")" and having
   * "colorFamilies" as FacetKey.key would by default return only "Red" products
   * in the search results, and also return "Red" with count 100 as the only
   * color facet. Although there are also blue products available, "Blue" would
   * not be shown as an available facet value. If "colorFamilies" is listed in
   * "excludedFilterKeys", then the query returns the facet values "Red" with
   * count 100 and "Blue" with count 200, because the "colorFamilies" key is now
   * excluded from the filter. Because this field doesn't affect search results,
   * the search results are still correctly filtered to return only "Red"
   * products. A maximum of 100 values are allowed. Otherwise, an
   * INVALID_ARGUMENT error is returned.
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
   * @param GoogleCloudRetailV2SearchRequestFacetSpecFacetKey $facetKey
   */
  public function setFacetKey(GoogleCloudRetailV2SearchRequestFacetSpecFacetKey $facetKey)
  {
    $this->facetKey = $facetKey;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestFacetSpecFacetKey
   */
  public function getFacetKey()
  {
    return $this->facetKey;
  }
  /**
   * Maximum of facet values that should be returned for this facet. If
   * unspecified, defaults to 50. The maximum allowed value is 300. Values above
   * 300 will be coerced to 300. If this field is negative, an INVALID_ARGUMENT
   * is returned.
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
class_alias(GoogleCloudRetailV2SearchRequestFacetSpec::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchRequestFacetSpec');
