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

namespace Google\Service\Cloudbilling;

class GeoTaxonomy extends \Google\Collection
{
  /**
   * The type is not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The sku is global in nature, e.g. a license sku. Global skus are available
   * in all regions, and so have an empty region list.
   */
  public const TYPE_GLOBAL = 'GLOBAL';
  /**
   * The sku is available in a specific region, e.g. "us-west2".
   */
  public const TYPE_REGIONAL = 'REGIONAL';
  /**
   * The sku is associated with multiple regions, e.g. "us-west2" and "us-
   * east1".
   */
  public const TYPE_MULTI_REGIONAL = 'MULTI_REGIONAL';
  protected $collection_key = 'regions';
  /**
   * The list of regions associated with a sku. Empty for Global skus, which are
   * associated with all Google Cloud regions.
   *
   * @var string[]
   */
  public $regions;
  /**
   * The type of Geo Taxonomy: GLOBAL, REGIONAL, or MULTI_REGIONAL.
   *
   * @var string
   */
  public $type;

  /**
   * The list of regions associated with a sku. Empty for Global skus, which are
   * associated with all Google Cloud regions.
   *
   * @param string[] $regions
   */
  public function setRegions($regions)
  {
    $this->regions = $regions;
  }
  /**
   * @return string[]
   */
  public function getRegions()
  {
    return $this->regions;
  }
  /**
   * The type of Geo Taxonomy: GLOBAL, REGIONAL, or MULTI_REGIONAL.
   *
   * Accepted values: TYPE_UNSPECIFIED, GLOBAL, REGIONAL, MULTI_REGIONAL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GeoTaxonomy::class, 'Google_Service_Cloudbilling_GeoTaxonomy');
