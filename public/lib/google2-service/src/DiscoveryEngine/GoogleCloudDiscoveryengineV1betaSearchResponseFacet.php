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

class GoogleCloudDiscoveryengineV1betaSearchResponseFacet extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * @var bool
   */
  public $dynamicFacet;
  /**
   * @var string
   */
  public $key;
  protected $valuesType = GoogleCloudDiscoveryengineV1betaSearchResponseFacetFacetValue::class;
  protected $valuesDataType = 'array';

  /**
   * @param bool
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
   * @param string
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
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseFacetFacetValue[]
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseFacetFacetValue[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchResponseFacet::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchResponseFacet');
