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
use Google\Service\Aiplatform\GoogleCloudAiplatformV1InvokeRequest;

/**
 * The "invoke" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $invoke = $aiplatformService->projects_locations_endpoints_deployedModels_invoke;
 *  </code>
 */
class ProjectsLocationsEndpointsDeployedModelsInvoke extends \Google\Service\Resource
{
  /**
   * Forwards arbitrary HTTP requests for both streaming and non-streaming cases.
   * To use this method, invoke_route_prefix must be set to allow the paths that
   * will be specified in the request. (invoke.invoke)
   *
   * @param string $endpoint Required. The name of the Endpoint requested to serve
   * the prediction. Format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`
   * @param string $deployedModelId ID of the DeployedModel that serves the invoke
   * request.
   * @param string $invokeId
   * @param GoogleCloudAiplatformV1InvokeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleApiHttpBody
   * @throws \Google\Service\Exception
   */
  public function invoke($endpoint, $deployedModelId, $invokeId, GoogleCloudAiplatformV1InvokeRequest $postBody, $optParams = [])
  {
    $params = ['endpoint' => $endpoint, 'deployedModelId' => $deployedModelId, 'invokeId' => $invokeId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('invoke', [$params], GoogleApiHttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEndpointsDeployedModelsInvoke::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsEndpointsDeployedModelsInvoke');
