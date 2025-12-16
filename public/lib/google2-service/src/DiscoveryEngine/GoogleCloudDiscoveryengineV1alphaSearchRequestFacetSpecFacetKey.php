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

class GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpecFacetKey extends \Google\Collection
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
   * Only get facet values that contain the given strings. For example, suppose
   * "category" has three values "Action > 2022", "Action > 2021" and "Sci-Fi >
   * 2022". If set "contains" to "2022", the "category" facet only contains
   * "Action > 2022" and "Sci-Fi > 2022". Only supported on textual fields.
   * Maximum is 10.
   *
   * @var string[]
   */
  public $contains;
  protected $intervalsType = GoogleCloudDiscoveryengineV1alphaInterval::class;
  protected $intervalsDataType = 'array';
  /**
   * Required. Supported textual and numerical facet keys in Document object,
   * over which the facet values are computed. Facet key is case-sensitive.
   *
   * @var string
   */
  public $key;
  /**
   * The order in which documents are returned. Allowed values are: * "count
   * desc", which means order by SearchResponse.Facet.values.count descending. *
   * "value desc", which means order by SearchResponse.Facet.values.value
   * descending. Only applies to textual facets. If not set, textual values are
   * sorted in [natural
   * order](https://en.wikipedia.org/wiki/Natural_sort_order); numerical
   * intervals are sorted in the order given by FacetSpec.FacetKey.intervals.
   *
   * @var string
   */
  public $orderBy;
  /**
   * Only get facet values that start with the given string prefix. For example,
   * suppose "category" has three values "Action > 2022", "Action > 2021" and
   * "Sci-Fi > 2022". If set "prefixes" to "Action", the "category" facet only
   * contains "Action > 2022" and "Action > 2021". Only supported on textual
   * fields. Maximum is 10.
   *
   * @var string[]
   */
  public $prefixes;
  /**
   * Only get facet for the given restricted values. Only supported on textual
   * fields. For example, suppose "category" has three values "Action > 2022",
   * "Action > 2021" and "Sci-Fi > 2022". If set "restricted_values" to "Action
   * > 2022", the "category" facet only contains "Action > 2022". Only supported
   * on textual fields. Maximum is 10.
   *
   * @var string[]
   */
  public $restrictedValues;

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
   * Only get facet values that contain the given strings. For example, suppose
   * "category" has three values "Action > 2022", "Action > 2021" and "Sci-Fi >
   * 2022". If set "contains" to "2022", the "category" facet only contains
   * "Action > 2022" and "Sci-Fi > 2022". Only supported on textual fields.
   * Maximum is 10.
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
   * Set only if values should be bucketed into intervals. Must be set for
   * facets with numerical values. Must not be set for facet with text values.
   * Maximum number of intervals is 30.
   *
   * @param GoogleCloudDiscoveryengineV1alphaInterval[] $intervals
   */
  public function setIntervals($intervals)
  {
    $this->intervals = $intervals;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaInterval[]
   */
  public function getIntervals()
  {
    return $this->intervals;
  }
  /**
   * Required. Supported textual and numerical facet keys in Document object,
   * over which the facet values are computed. Facet key is case-sensitive.
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
   * The order in which documents are returned. Allowed values are: * "count
   * desc", which means order by SearchResponse.Facet.values.count descending. *
   * "value desc", which means order by SearchResponse.Facet.values.value
   * descending. Only applies to textual facets. If not set, textual values are
   * sorted in [natural
   * order](https://en.wikipedia.org/wiki/Natural_sort_order); numerical
   * intervals are sorted in the order given by FacetSpec.FacetKey.intervals.
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
   * suppose "category" has three values "Action > 2022", "Action > 2021" and
   * "Sci-Fi > 2022". If set "prefixes" to "Action", the "category" facet only
   * contains "Action > 2022" and "Action > 2021". Only supported on textual
   * fields. Maximum is 10.
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
   * Only get facet for the given restricted values. Only supported on textual
   * fields. For example, suppose "category" has three values "Action > 2022",
   * "Action > 2021" and "Sci-Fi > 2022". If set "restricted_values" to "Action
   * > 2022", the "category" facet only contains "Action > 2022". Only supported
   * on textual fields. Maximum is 10.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpecFacetKey::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaSearchRequestFacetSpecFacetKey');
