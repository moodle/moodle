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

namespace Google\Service\AirQuality\Resource;

use Google\Service\AirQuality\LookupCurrentConditionsRequest;
use Google\Service\AirQuality\LookupCurrentConditionsResponse;

/**
 * The "currentConditions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $airqualityService = new Google\Service\AirQuality(...);
 *   $currentConditions = $airqualityService->currentConditions;
 *  </code>
 */
class CurrentConditions extends \Google\Service\Resource
{
  /**
   * The Current Conditions endpoint provides hourly air quality information in
   * more than 100 countries, up to a 500 x 500 meters resolution. Includes over
   * 70 local indexes and global air quality index and categories.
   * (currentConditions.lookup)
   *
   * @param LookupCurrentConditionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return LookupCurrentConditionsResponse
   * @throws \Google\Service\Exception
   */
  public function lookup(LookupCurrentConditionsRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('lookup', [$params], LookupCurrentConditionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CurrentConditions::class, 'Google_Service_AirQuality_Resource_CurrentConditions');
