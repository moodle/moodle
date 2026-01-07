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

namespace Google\Service\Solar\Resource;

use Google\Service\Solar\BuildingInsights as BuildingInsightsModel;

/**
 * The "buildingInsights" collection of methods.
 * Typical usage is:
 *  <code>
 *   $solarService = new Google\Service\Solar(...);
 *   $buildingInsights = $solarService->buildingInsights;
 *  </code>
 */
class BuildingInsights extends \Google\Service\Resource
{
  /**
   * Locates the building whose centroid is closest to a query point. Returns an
   * error with code `NOT_FOUND` if there are no buildings within approximately
   * 50m of the query point. (buildingInsights.findClosest)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool exactQualityRequired Optional. Whether to require exact
   * quality of the imagery. If set to false, the `required_quality` field is
   * interpreted as the minimum required quality, such that HIGH quality imagery
   * may be returned when `required_quality` is set to MEDIUM. If set to true,
   * `required_quality` is interpreted as the exact required quality and only
   * `MEDIUM` quality imagery is returned if `required_quality` is set to
   * `MEDIUM`.
   * @opt_param string experiments Optional. Specifies the pre-GA features to
   * enable.
   * @opt_param double location.latitude The latitude in degrees. It must be in
   * the range [-90.0, +90.0].
   * @opt_param double location.longitude The longitude in degrees. It must be in
   * the range [-180.0, +180.0].
   * @opt_param string requiredQuality Optional. The minimum quality level allowed
   * in the results. No result with lower quality than this will be returned. Not
   * specifying this is equivalent to restricting to HIGH quality only.
   * @return BuildingInsightsModel
   * @throws \Google\Service\Exception
   */
  public function findClosest($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('findClosest', [$params], BuildingInsightsModel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildingInsights::class, 'Google_Service_Solar_Resource_BuildingInsights');
