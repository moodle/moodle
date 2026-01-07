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

use Google\Service\APIhub\GoogleCloudApihubV1StyleGuideContents;

/**
 * The "styleGuide" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $styleGuide = $apihubService->projects_locations_plugins_styleGuide;
 *  </code>
 */
class ProjectsLocationsPluginsStyleGuide extends \Google\Service\Resource
{
  /**
   * Get the contents of the style guide. (styleGuide.getContents)
   *
   * @param string $name Required. The name of the StyleGuide whose contents need
   * to be retrieved. There is exactly one style guide resource per project per
   * location. The expected format is
   * `projects/{project}/locations/{location}/plugins/{plugin}/styleGuide`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1StyleGuideContents
   * @throws \Google\Service\Exception
   */
  public function getContents($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getContents', [$params], GoogleCloudApihubV1StyleGuideContents::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPluginsStyleGuide::class, 'Google_Service_APIhub_Resource_ProjectsLocationsPluginsStyleGuide');
