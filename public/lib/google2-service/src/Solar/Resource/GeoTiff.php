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

use Google\Service\Solar\HttpBody;

/**
 * The "geoTiff" collection of methods.
 * Typical usage is:
 *  <code>
 *   $solarService = new Google\Service\Solar(...);
 *   $geoTiff = $solarService->geoTiff;
 *  </code>
 */
class GeoTiff extends \Google\Service\Resource
{
  /**
   * Returns an image by its ID. (geoTiff.get)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string id Required. The ID of the asset being requested.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function get($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GeoTiff::class, 'Google_Service_Solar_Resource_GeoTiff');
