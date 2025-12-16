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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonLocationGroupInfo extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const RADIUS_UNITS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const RADIUS_UNITS_UNKNOWN = 'UNKNOWN';
  /**
   * Meters
   */
  public const RADIUS_UNITS_METERS = 'METERS';
  /**
   * Miles
   */
  public const RADIUS_UNITS_MILES = 'MILES';
  /**
   * Milli Miles
   */
  public const RADIUS_UNITS_MILLI_MILES = 'MILLI_MILES';
  protected $collection_key = 'geoTargetConstants';
  /**
   * FeedItemSets whose FeedItems are targeted. If multiple IDs are specified,
   * then all items that appear in at least one set are targeted. This field
   * cannot be used with geo_target_constants. This is optional and can only be
   * set in CREATE operations.
   *
   * @var string[]
   */
  public $feedItemSets;
  /**
   * Geo target constant(s) restricting the scope of the geographic area within
   * the feed. Currently only one geo target constant is allowed.
   *
   * @var string[]
   */
  public $geoTargetConstants;
  /**
   * Distance in units specifying the radius around targeted locations. This is
   * required and must be set in CREATE operations.
   *
   * @var string
   */
  public $radius;
  /**
   * Unit of the radius. Miles and meters are supported for geo target
   * constants. Milli miles and meters are supported for feed item sets. This is
   * required and must be set in CREATE operations.
   *
   * @var string
   */
  public $radiusUnits;

  /**
   * FeedItemSets whose FeedItems are targeted. If multiple IDs are specified,
   * then all items that appear in at least one set are targeted. This field
   * cannot be used with geo_target_constants. This is optional and can only be
   * set in CREATE operations.
   *
   * @param string[] $feedItemSets
   */
  public function setFeedItemSets($feedItemSets)
  {
    $this->feedItemSets = $feedItemSets;
  }
  /**
   * @return string[]
   */
  public function getFeedItemSets()
  {
    return $this->feedItemSets;
  }
  /**
   * Geo target constant(s) restricting the scope of the geographic area within
   * the feed. Currently only one geo target constant is allowed.
   *
   * @param string[] $geoTargetConstants
   */
  public function setGeoTargetConstants($geoTargetConstants)
  {
    $this->geoTargetConstants = $geoTargetConstants;
  }
  /**
   * @return string[]
   */
  public function getGeoTargetConstants()
  {
    return $this->geoTargetConstants;
  }
  /**
   * Distance in units specifying the radius around targeted locations. This is
   * required and must be set in CREATE operations.
   *
   * @param string $radius
   */
  public function setRadius($radius)
  {
    $this->radius = $radius;
  }
  /**
   * @return string
   */
  public function getRadius()
  {
    return $this->radius;
  }
  /**
   * Unit of the radius. Miles and meters are supported for geo target
   * constants. Milli miles and meters are supported for feed item sets. This is
   * required and must be set in CREATE operations.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, METERS, MILES, MILLI_MILES
   *
   * @param self::RADIUS_UNITS_* $radiusUnits
   */
  public function setRadiusUnits($radiusUnits)
  {
    $this->radiusUnits = $radiusUnits;
  }
  /**
   * @return self::RADIUS_UNITS_*
   */
  public function getRadiusUnits()
  {
    return $this->radiusUnits;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonLocationGroupInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonLocationGroupInfo');
