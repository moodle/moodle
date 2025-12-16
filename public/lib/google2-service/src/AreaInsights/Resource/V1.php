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

namespace Google\Service\AreaInsights\Resource;

use Google\Service\AreaInsights\ComputeInsightsRequest;
use Google\Service\AreaInsights\ComputeInsightsResponse;

/**
 * The "v1" collection of methods.
 * Typical usage is:
 *  <code>
 *   $areainsightsService = new Google\Service\AreaInsights(...);
 *   $v1 = $areainsightsService->v1;
 *  </code>
 */
class V1 extends \Google\Service\Resource
{
  /**
   * This method lets you retrieve insights about areas using a variety of filter
   * such as: area, place type, operating status, price level and ratings.
   * Currently "count" and "places" insights are supported. With "count" insights
   * you can answer questions such as "How many restaurant are located in
   * California that are operational, are inexpensive and have an average rating
   * of at least 4 stars" (see `insight` enum for more details). With "places"
   * insights, you can determine which places match the requested filter. Clients
   * can then use those place resource names to fetch more details about each
   * individual place using the Places API. (v1.computeInsights)
   *
   * @param ComputeInsightsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ComputeInsightsResponse
   * @throws \Google\Service\Exception
   */
  public function computeInsights(ComputeInsightsRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('computeInsights', [$params], ComputeInsightsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V1::class, 'Google_Service_AreaInsights_Resource_V1');
