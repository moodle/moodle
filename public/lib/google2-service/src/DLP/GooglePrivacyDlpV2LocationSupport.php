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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2LocationSupport extends \Google\Collection
{
  /**
   * Invalid.
   */
  public const REGIONALIZATION_SCOPE_REGIONALIZATION_SCOPE_UNSPECIFIED = 'REGIONALIZATION_SCOPE_UNSPECIFIED';
  /**
   * Feature may be used with one or more regions. See locations for details.
   */
  public const REGIONALIZATION_SCOPE_REGIONAL = 'REGIONAL';
  /**
   * Feature may be used anywhere. Default value.
   */
  public const REGIONALIZATION_SCOPE_ANY_LOCATION = 'ANY_LOCATION';
  protected $collection_key = 'locations';
  /**
   * Specific locations where the feature may be used. Examples: us-central1,
   * us, asia, global If scope is ANY_LOCATION, no regions will be listed.
   *
   * @var string[]
   */
  public $locations;
  /**
   * The current scope for location on this feature. This may expand over time.
   *
   * @var string
   */
  public $regionalizationScope;

  /**
   * Specific locations where the feature may be used. Examples: us-central1,
   * us, asia, global If scope is ANY_LOCATION, no regions will be listed.
   *
   * @param string[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return string[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * The current scope for location on this feature. This may expand over time.
   *
   * Accepted values: REGIONALIZATION_SCOPE_UNSPECIFIED, REGIONAL, ANY_LOCATION
   *
   * @param self::REGIONALIZATION_SCOPE_* $regionalizationScope
   */
  public function setRegionalizationScope($regionalizationScope)
  {
    $this->regionalizationScope = $regionalizationScope;
  }
  /**
   * @return self::REGIONALIZATION_SCOPE_*
   */
  public function getRegionalizationScope()
  {
    return $this->regionalizationScope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2LocationSupport::class, 'Google_Service_DLP_GooglePrivacyDlpV2LocationSupport');
