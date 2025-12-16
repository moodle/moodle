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

namespace Google\Service\Integrations\Resource;

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaCreateCloudFunctionRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaCreateCloudFunctionResponse;

/**
 * The "cloudFunctions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $cloudFunctions = $integrationsService->projects_locations_cloudFunctions;
 *  </code>
 */
class ProjectsLocationsCloudFunctions extends \Google\Service\Resource
{
  /**
   * Creates a cloud function project. (cloudFunctions.create)
   *
   * @param string $parent Required. The project that the executed integration
   * belongs to.
   * @param GoogleCloudIntegrationsV1alphaCreateCloudFunctionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaCreateCloudFunctionResponse
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudIntegrationsV1alphaCreateCloudFunctionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudIntegrationsV1alphaCreateCloudFunctionResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCloudFunctions::class, 'Google_Service_Integrations_Resource_ProjectsLocationsCloudFunctions');
