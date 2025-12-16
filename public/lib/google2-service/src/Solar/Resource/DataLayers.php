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

use Google\Service\Solar\DataLayers as DataLayersModel;

/**
 * The "dataLayers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $solarService = new Google\Service\Solar(...);
 *   $dataLayers = $solarService->dataLayers;
 *  </code>
 */
class DataLayers extends \Google\Service\Resource
{
  /**
   * Gets solar information for a region surrounding a location. Returns an error
   * with code `NOT_FOUND` if the location is outside the coverage area.
   * (dataLayers.get)
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
   * @opt_param string experiments Optional. Specifies the pre-GA experiments to
   * enable.
   * @opt_param double location.latitude The latitude in degrees. It must be in
   * the range [-90.0, +90.0].
   * @opt_param double location.longitude The longitude in degrees. It must be in
   * the range [-180.0, +180.0].
   * @opt_param float pixelSizeMeters Optional. The minimum scale, in meters per
   * pixel, of the data to return. Values of 0.1 (the default, if this field is
   * not set explicitly), 0.25, 0.5, and 1.0 are supported. Imagery components
   * whose normal resolution is less than `pixel_size_meters` will be returned at
   * the resolution specified by `pixel_size_meters`; imagery components whose
   * normal resolution is equal to or greater than `pixel_size_meters` will be
   * returned at that normal resolution.
   * @opt_param float radiusMeters Required. The radius, in meters, defining the
   * region surrounding that centre point for which data should be returned. The
   * limitations on this value are: * Any value up to 100m can always be
   * specified. * Values over 100m can be specified, as long as `radius_meters` <=
   * `pixel_size_meters * 1000`. * However, for values over 175m, the
   * `DataLayerView` in the request must not include monthly flux or hourly shade.
   * @opt_param string requiredQuality Optional. The minimum quality level allowed
   * in the results. No result with lower quality than this will be returned. Not
   * specifying this is equivalent to restricting to HIGH quality only.
   * @opt_param string view Optional. The desired subset of the data to return.
   * @return DataLayersModel
   * @throws \Google\Service\Exception
   */
  public function get($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DataLayersModel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataLayers::class, 'Google_Service_Solar_Resource_DataLayers');
