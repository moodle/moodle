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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleApiHttpBody;

/**
 * The "chat" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $chat = $aiplatformService->projects_locations_endpoints_chat;
 *  </code>
 */
class ProjectsLocationsEndpointsChat extends \Google\Service\Resource
{
  /**
   * Exposes an OpenAI-compatible endpoint for chat completions.
   * (chat.completions)
   *
   * @param string $endpoint Required. The name of the endpoint requested to serve
   * the prediction. Format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`
   * @param GoogleApiHttpBody $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleApiHttpBody
   * @throws \Google\Service\Exception
   */
  public function completions($endpoint, GoogleApiHttpBody $postBody, $optParams = [])
  {
    $params = ['endpoint' => $endpoint, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('completions', [$params], GoogleApiHttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEndpointsChat::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsEndpointsChat');
