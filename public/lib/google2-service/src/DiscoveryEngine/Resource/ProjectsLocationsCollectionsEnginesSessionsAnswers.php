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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1Answer;

/**
 * The "answers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $answers = $discoveryengineService->projects_locations_collections_engines_sessions_answers;
 *  </code>
 */
class ProjectsLocationsCollectionsEnginesSessionsAnswers extends \Google\Service\Resource
{
  /**
   * Gets a Answer. (answers.get)
   *
   * @param string $name Required. The resource name of the Answer to get. Format:
   * `projects/{project}/locations/{location}/collections/{collection}/engines/{en
   * gine_id}/sessions/{session_id}/answers/{answer_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1Answer
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1Answer::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsEnginesSessionsAnswers::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsEnginesSessionsAnswers');
