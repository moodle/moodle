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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1RankRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1RankResponse;

/**
 * The "rankingConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $rankingConfigs = $discoveryengineService->projects_locations_rankingConfigs;
 *  </code>
 */
class ProjectsLocationsRankingConfigs extends \Google\Service\Resource
{
  /**
   * Ranks a list of text records based on the given input query.
   * (rankingConfigs.rank)
   *
   * @param string $rankingConfig Required. The resource name of the rank service
   * config, such as `projects/{project_num}/locations/{location}/rankingConfigs/d
   * efault_ranking_config`.
   * @param GoogleCloudDiscoveryengineV1RankRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1RankResponse
   * @throws \Google\Service\Exception
   */
  public function rank($rankingConfig, GoogleCloudDiscoveryengineV1RankRequest $postBody, $optParams = [])
  {
    $params = ['rankingConfig' => $rankingConfig, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rank', [$params], GoogleCloudDiscoveryengineV1RankResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRankingConfigs::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsRankingConfigs');
