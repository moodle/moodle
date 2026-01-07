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

class GoogleCloudDiscoveryengineV1SearchResponseFacet extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * Whether the facet is dynamically generated.
   *
   * @var bool
   */
  public $dynamicFacet;
  /**
   * The key for this facet. For example, `"colors"` or `"price"`. It matches
   * SearchRequest.FacetSpec.FacetKey.key.
   *
   * @var string
   */
  public $key;
  protected $valuesType = GoogleCloudDiscoveryengineV1SearchResponseFacetFacetValue::class;
  protected $valuesDataType = 'array';

  /**
   * Whether the facet is dynamically generated.
   *
   * @param bool $dynamicFacet
   */
  public function setDynamicFacet($dynamicFacet)
  {
    $this->dynamicFacet = $dynamicFacet;
  }
  /**
   * @return bool
   */
  public function getDynamicFacet()
  {
    return $this->dynamicFacet;
  }
  /**
   * The key for this facet. For example, `"colors"` or `"price"`. It matches
   * SearchRequest.FacetSpec.FacetKey.key.
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
   * The facet values for this field.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseFacetFacetValue[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseFacetFacetValue[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchResponseFacet::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchResponseFacet');
