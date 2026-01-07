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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\GoogleCloudApihubV1Definition;

/**
 * The "definitions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $definitions = $apihubService->projects_locations_apis_versions_definitions;
 *  </code>
 */
class ProjectsLocationsApisVersionsDefinitions extends \Google\Service\Resource
{
  /**
   * Get details about a definition in an API version. (definitions.get)
   *
   * @param string $name Required. The name of the definition to retrieve. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}/defini
   * tions/{definition}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Definition
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Definition::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApisVersionsDefinitions::class, 'Google_Service_APIhub_Resource_ProjectsLocationsApisVersionsDefinitions');
