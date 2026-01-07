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

namespace Google\Service\CloudRun\Resource;

use Google\Service\CloudRun\GoogleCloudRunV2SubmitBuildRequest;
use Google\Service\CloudRun\GoogleCloudRunV2SubmitBuildResponse;

/**
 * The "builds" collection of methods.
 * Typical usage is:
 *  <code>
 *   $runService = new Google\Service\CloudRun(...);
 *   $builds = $runService->projects_locations_builds;
 *  </code>
 */
class ProjectsLocationsBuilds extends \Google\Service\Resource
{
  /**
   * Submits a build in a given project. (builds.submit)
   *
   * @param string $parent Required. The project and location to build in.
   * Location must be a region, e.g., 'us-central1' or 'global' if the global
   * builder is to be used. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudRunV2SubmitBuildRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2SubmitBuildResponse
   * @throws \Google\Service\Exception
   */
  public function submit($parent, GoogleCloudRunV2SubmitBuildRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('submit', [$params], GoogleCloudRunV2SubmitBuildResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBuilds::class, 'Google_Service_CloudRun_Resource_ProjectsLocationsBuilds');
