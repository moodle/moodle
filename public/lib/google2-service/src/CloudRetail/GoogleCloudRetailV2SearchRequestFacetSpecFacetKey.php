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

class GoogleCloudRetailV2SearchRequestFacetSpecFacetKey extends \Google\Collection
{
  protected $collection_key = 'restrictedValues';
  /**
   * True to make facet keys case insensitive when getting faceting values with
   * prefixes or contains; false otherwise.
   *
   * @var bool
   */
  public $caseInsensitive;
  /**
   * Only get facet values that contains the given strings. For example, suppose
   * "categories" has three values "Women > Shoe", "Women > Dress" and "Men >
   * Shoe". If set "contains" to "Shoe", the "categories" facet gives only
   * "Women > Shoe" and "Men > Shoe". Only supported on textual fields. Maximum
   * is 10.
   *
   * @var string[]
   */
  public $contains;
  protected $intervalsType = GoogleCloudRetailV2Interval::class;
  protected $intervalsDataType = 'array';
  /**
   * Required. Supported textual and numerical facet keys in Product object,
   * over which the facet values are computed. Facet key is case-sensitive.
   * Allowed facet keys when FacetKey.query is not specified: * textual_field =
   * * "brands" * "categories" * "genders" * "ageGroups" * "availability" *
   * "colorFamilies" * "colors" * "sizes" * "materials" * "patterns" *
   * "conditions" * "attributes.key" * "pickupInStore" * "shipToStore" *
   * "sameDayDelivery" * "nextDayDelivery" * "customFulfillment1" *
   * "customFulfillment2" * "customFulfillment3" * "customFulfillment4" *
   * "customFulfillment5" * "inventory(place_id,attributes.key)" *
   * numerical_field = * "price" * "discount" * "rating" * "ratingCount" *
   * "attributes.key" * "inventory(place_id,price)" *
   * "inventory(place_id,original_price)" * "inventory(place_id,attributes.key)"
   *
   * @var string
   */
  public $key;
  /**
   * The order in which SearchResponse.Facet.values are returned. Allowed values
   * are: * "count desc", which means order by SearchResponse.Facet.values.count
   * descending. * "value desc", which means order by
   * SearchResponse.Facet.values.value descending. Only applies to textual
   * facets. If not set, textual values are sorted in [natural
   * order](https://en.wikipedia.org/wiki/Natural_sort_order); numerical
   * intervals are sorted in the order given by FacetSpec.FacetKey.intervals;
   * FulfillmentInfo.place_ids are sorted in the order given by
   * FacetSpec.FacetKey.restricted_values.
   *
   * @var string
   */
  public $orderBy;
  /**
   * Only get facet values that start with the given string prefix. For example,
   * suppose "categories" has three values "Women > Shoe", "Women > Dress" and
   * "Men > Shoe". If set "prefixes" to "Women", the "categories" facet gives
   * only "Women > Shoe" and "Women > Dress". Only supported on textual fields.
   * Maximum is 10.
   *
   * @var string[]
   */
  public $prefixes;
  /**
   * The query that is used to compute facet for the given facet key. When
   * provided, it overrides the default behavior of facet computation. The query
   * syntax is the same as a filter expression. See SearchRequest.filter for
   * detail syntax and limitations. Notice that there is no limitation on
   * FacetKey.key when query is specified. In the response,
   * SearchResponse.Facet.values.value is always "1" and
   * SearchResponse.Facet.values.count is the number of results that match the
   * query. For example, you can set a customized facet for "shipToStore", where
   * FacetKey.key is "customizedShipToStore", and FacetKey.query is
   * "availability: ANY(\"IN_STOCK\") AND shipToStore: ANY(\"123\")". Then the
   * facet counts the products that are both in stock and ship to store "123".
   *
   * @var string
   */
  public $query;
  /**
   * Only get facet for the given restricted values. For example, when using
   * "pickupInStore" as key and set restricted values to ["store123",
   * "store456"], only facets for "store123" and "store456" are returned. Only
   * supported on predefined textual fields, custom textual attributes and
   * fulfillments. Maximum is 20. Must be set for the fulfillment facet keys: *
   * pickupInStore * shipToStore * sameDayDelivery * nextDayDelivery *
   * customFulfillment1 * customFulfillment2 * customFulfillment3 *
   * customFulfillment4 * customFulfillment5
   *
   * @var string[]
   */
  public $restrictedValues;
  /**
   * Returns the min and max value for each numerical facet intervals. Ignored
   * for textual facets.
   *
   * @var bool
   */
  public $returnMinMax;

  /**
   * True to make facet keys case insensitive when getting faceting values with
   * prefixes or contains; false otherwise.
   *
   * @param bool $caseInsensitive
   */
  public function setCaseInsensitive($caseInsensitive)
  {
    $this->caseInsensitive = $caseInsensitive;
  }
  /**
   * @return bool
   */
  public function getCaseInsensitive()
  {
    return $this->caseInsensitive;
  }
  /**
   * Only get facet values that contains the given strings. For example, suppose
   * "categories" has three values "Women > Shoe", "Women > Dress" and "Men >
   * Shoe". If set "contains" to "Shoe", the "categories" facet gives only
   * "Women > Shoe" and "Men > Shoe". Only supported on textual fields. Maximum
   * is 10.
   *
   * @param string[] $contains
   */
  public function setContains($contains)
  {
    $this->contains = $contains;
  }
  /**
   * @return string[]
   */
  public function getContains()
  {
    return $this->contains;
  }
  /**
   * Set only if values should be bucketized into intervals. Must be set for
   * facets with numerical values. Must not be set for facet with text values.
   * Maximum number of intervals is 40. For all numerical facet keys that appear
   * in the list of products from the catalog, the percentiles 0, 10, 30, 50,
   * 70, 90, and 100 are computed from their distribution weekly. If the model
   * assigns a high score to a numerical facet key and its intervals are not
   * specified in the search request, these percentiles become the bounds for
   * its intervals and are returned in the response. If the facet key intervals
   * are specified in the request, then the specified intervals are returned
   * instead.
   *
   * @param GoogleCloudRetailV2Interval[] $intervals
   */
  public function setIntervals($intervals)
  {
    $this->intervals = $intervals;
  }
  /**
   * @return GoogleCloudRetailV2Interval[]
   */
  public function getIntervals()
  {
    return $this->intervals;
  }
  /**
   * Required. Supported textual and numerical facet keys in Product object,
   * over which the facet values are computed. Facet key is case-sensitive.
   * Allowed facet keys when FacetKey.query is not specified: * textual_field =
   * * "brands" * "categories" * "genders" * "ageGroups" * "availability" *
   * "colorFamilies" * "colors" * "sizes" * "materials" * "patterns" *
   * "conditions" * "attributes.key" * "pickupInStore" * "shipToStore" *
   * "sameDayDelivery" * "nextDayDelivery" * "customFulfillment1" *
   * "customFulfillment2" * "customFulfillment3" * "customFulfillment4" *
   * "customFulfillment5" * "inventory(place_id,attributes.key)" *
   * numerical_field = * "price" * "discount" * "rating" * "ratingCount" *
   * "attributes.key" * "inventory(place_id,price)" *
   * "inventory(place_id,original_price)" * "inventory(place_id,attributes.key)"
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * The order in which SearchResponse.Facet.values are returned. Allowed values
   * are: * "count desc", which means order by SearchResponse.Facet.values.count
   * descending. * "value desc", which means order by
   * SearchResponse.Facet.values.value descending. Only applies to textual
   * facets. If not set, textual values are sorted in [natural
   * order](https://en.wikipedia.org/wiki/Natural_sort_order); numerical
   * intervals are sorted in the order given by FacetSpec.FacetKey.intervals;
   * FulfillmentInfo.place_ids are sorted in the order given by
   * FacetSpec.FacetKey.restricted_values.
   *
   * @param string $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * Only get facet values that start with the given string prefix. For example,
   * suppose "categories" has three values "Women > Shoe", "Women > Dress" and
   * "Men > Shoe". If set "prefixes" to "Women", the "categories" facet gives
   * only "Women > Shoe" and "Women > Dress". Only supported on textual fields.
   * Maximum is 10.
   *
   * @param string[] $prefixes
   */
  public function setPrefixes($prefixes)
  {
    $this->prefixes = $prefixes;
  }
  /**
   * @return string[]
   */
  public function getPrefixes()
  {
    return $this->prefixes;
  }
  /**
   * The query that is used to compute facet for the given facet key. When
   * provided, it overrides the default behavior of facet computation. The query
   * syntax is the same as a filter expression. See SearchRequest.filter for
   * detail syntax and limitations. Notice that there is no limitation on
   * FacetKey.key when query is specified. In the response,
   * SearchResponse.Facet.values.value is always "1" and
   * SearchResponse.Facet.values.count is the number of results that match the
   * query. For example, you can set a customized facet for "shipToStore", where
   * FacetKey.key is "customizedShipToStore", and FacetKey.query is
   * "availability: ANY(\"IN_STOCK\") AND shipToStore: ANY(\"123\")". Then the
   * facet counts the products that are both in stock and ship to store "123".
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Only get facet for the given restricted values. For example, when using
   * "pickupInStore" as key and set restricted values to ["store123",
   * "store456"], only facets for "store123" and "store456" are returned. Only
   * supported on predefined textual fields, custom textual attributes and
   * fulfillments. Maximum is 20. Must be set for the fulfillment facet keys: *
   * pickupInStore * shipToStore * sameDayDelivery * nextDayDelivery *
   * customFulfillment1 * customFulfillment2 * customFulfillment3 *
   * customFulfillment4 * customFulfillment5
   *
   * @param string[] $restrictedValues
   */
  public function setRestrictedValues($restrictedValues)
  {
    $this->restrictedValues = $restrictedValues;
  }
  /**
   * @return string[]
   */
  public function getRestrictedValues()
  {
    return $this->restrictedValues;
  }
  /**
   * Returns the min and max value for each numerical facet intervals. Ignored
   * for textual facets.
   *
   * @param bool $returnMinMax
   */
  public function setReturnMinMax($returnMinMax)
  {
    $this->returnMinMax = $returnMinMax;
  }
  /**
   * @return bool
   */
  public function getReturnMinMax()
  {
    return $this->returnMinMax;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchRequestFacetSpecFacetKey::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchRequestFacetSpecFacetKey');
