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

class GoogleCloudRetailV2CatalogAttributeFacetConfigRerankConfig extends \Google\Collection
{
  protected $collection_key = 'facetValues';
  /**
   * If empty, rerank on all facet values for the current key. Otherwise, will
   * rerank on the facet values from this list only.
   *
   * @var string[]
   */
  public $facetValues;
  /**
   * If set to true, then we also rerank the dynamic facets based on the facet
   * values engaged by the user for the current attribute key during serving.
   *
   * @var bool
   */
  public $rerankFacet;

  /**
   * If empty, rerank on all facet values for the current key. Otherwise, will
   * rerank on the facet values from this list only.
   *
   * @param string[] $facetValues
   */
  public function setFacetValues($facetValues)
  {
    $this->facetValues = $facetValues;
  }
  /**
   * @return string[]
   */
  public function getFacetValues()
  {
    return $this->facetValues;
  }
  /**
   * If set to true, then we also rerank the dynamic facets based on the facet
   * values engaged by the user for the current attribute key during serving.
   *
   * @param bool $rerankFacet
   */
  public function setRerankFacet($rerankFacet)
  {
    $this->rerankFacet = $rerankFacet;
  }
  /**
   * @return bool
   */
  public function getRerankFacet()
  {
    return $this->rerankFacet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2CatalogAttributeFacetConfigRerankConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CatalogAttributeFacetConfigRerankConfig');
