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

class GoogleCloudRetailV2CatalogAttributeFacetConfig extends \Google\Collection
{
  protected $collection_key = 'mergedFacetValues';
  protected $facetIntervalsType = GoogleCloudRetailV2Interval::class;
  protected $facetIntervalsDataType = 'array';
  protected $ignoredFacetValuesType = GoogleCloudRetailV2CatalogAttributeFacetConfigIgnoredFacetValues::class;
  protected $ignoredFacetValuesDataType = 'array';
  protected $mergedFacetType = GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacet::class;
  protected $mergedFacetDataType = '';
  protected $mergedFacetValuesType = GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacetValue::class;
  protected $mergedFacetValuesDataType = 'array';
  protected $rerankConfigType = GoogleCloudRetailV2CatalogAttributeFacetConfigRerankConfig::class;
  protected $rerankConfigDataType = '';

  /**
   * If you don't set the facet SearchRequest.FacetSpec.FacetKey.intervals in
   * the request to a numerical attribute, then we use the computed intervals
   * with rounded bounds obtained from all its product numerical attribute
   * values. The computed intervals might not be ideal for some attributes.
   * Therefore, we give you the option to overwrite them with the
   * facet_intervals field. The maximum of facet intervals per CatalogAttribute
   * is 40. Each interval must have a lower bound or an upper bound. If both
   * bounds are provided, then the lower bound must be smaller or equal than the
   * upper bound.
   *
   * @param GoogleCloudRetailV2Interval[] $facetIntervals
   */
  public function setFacetIntervals($facetIntervals)
  {
    $this->facetIntervals = $facetIntervals;
  }
  /**
   * @return GoogleCloudRetailV2Interval[]
   */
  public function getFacetIntervals()
  {
    return $this->facetIntervals;
  }
  /**
   * Each instance represents a list of attribute values to ignore as facet
   * values for a specific time range. The maximum number of instances per
   * CatalogAttribute is 25.
   *
   * @param GoogleCloudRetailV2CatalogAttributeFacetConfigIgnoredFacetValues[] $ignoredFacetValues
   */
  public function setIgnoredFacetValues($ignoredFacetValues)
  {
    $this->ignoredFacetValues = $ignoredFacetValues;
  }
  /**
   * @return GoogleCloudRetailV2CatalogAttributeFacetConfigIgnoredFacetValues[]
   */
  public function getIgnoredFacetValues()
  {
    return $this->ignoredFacetValues;
  }
  /**
   * Use this field only if you want to merge a facet key into another facet
   * key.
   *
   * @param GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacet $mergedFacet
   */
  public function setMergedFacet(GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacet $mergedFacet)
  {
    $this->mergedFacet = $mergedFacet;
  }
  /**
   * @return GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacet
   */
  public function getMergedFacet()
  {
    return $this->mergedFacet;
  }
  /**
   * Each instance replaces a list of facet values by a merged facet value. If a
   * facet value is not in any list, then it will stay the same. To avoid
   * conflicts, only paths of length 1 are accepted. In other words, if
   * "dark_blue" merged into "BLUE", then the latter can't merge into "blues"
   * because this would create a path of length 2. The maximum number of
   * instances of MergedFacetValue per CatalogAttribute is 100. This feature is
   * available only for textual custom attributes.
   *
   * @param GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacetValue[] $mergedFacetValues
   */
  public function setMergedFacetValues($mergedFacetValues)
  {
    $this->mergedFacetValues = $mergedFacetValues;
  }
  /**
   * @return GoogleCloudRetailV2CatalogAttributeFacetConfigMergedFacetValue[]
   */
  public function getMergedFacetValues()
  {
    return $this->mergedFacetValues;
  }
  /**
   * Set this field only if you want to rerank based on facet values engaged by
   * the user for the current key. This option is only possible for custom
   * facetable textual keys.
   *
   * @param GoogleCloudRetailV2CatalogAttributeFacetConfigRerankConfig $rerankConfig
   */
  public function setRerankConfig(GoogleCloudRetailV2CatalogAttributeFacetConfigRerankConfig $rerankConfig)
  {
    $this->rerankConfig = $rerankConfig;
  }
  /**
   * @return GoogleCloudRetailV2CatalogAttributeFacetConfigRerankConfig
   */
  public function getRerankConfig()
  {
    return $this->rerankConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2CatalogAttributeFacetConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CatalogAttributeFacetConfig');
