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

namespace Google\Service\Pollen\Resource;

use Google\Service\Pollen\LookupForecastResponse;

/**
 * The "forecast" collection of methods.
 * Typical usage is:
 *  <code>
 *   $pollenService = new Google\Service\Pollen(...);
 *   $forecast = $pollenService->forecast;
 *  </code>
 */
class Forecast extends \Google\Service\Resource
{
  /**
   * Returns up to 5 days of daily pollen information in more than 65 countries,
   * up to 1km resolution. (forecast.lookup)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int days Required. A number that indicates how many forecast days
   * to request (minimum value 1, maximum value is 5).
   * @opt_param string languageCode Optional. Allows the client to choose the
   * language for the response. If data cannot be provided for that language, the
   * API uses the closest match. Allowed values rely on the IETF BCP-47 standard.
   * The default value is "en".
   * @opt_param double location.latitude The latitude in degrees. It must be in
   * the range [-90.0, +90.0].
   * @opt_param double location.longitude The longitude in degrees. It must be in
   * the range [-180.0, +180.0].
   * @opt_param int pageSize Optional. The maximum number of daily info records to
   * return per page. The default and max value is 5, indicating 5 days of data.
   * @opt_param string pageToken Optional. A page token received from a previous
   * daily call. It is used to retrieve the subsequent page. Note that when
   * providing a value for the page token, all other request parameters provided
   * must match the previous call that provided the page token.
   * @opt_param bool plantsDescription Optional. Contains general information
   * about plants, including details on their seasonality, special shapes and
   * colors, information about allergic cross-reactions, and plant photos. The
   * default value is "true".
   * @return LookupForecastResponse
   * @throws \Google\Service\Exception
   */
  public function lookup($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('lookup', [$params], LookupForecastResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Forecast::class, 'Google_Service_Pollen_Resource_Forecast');
