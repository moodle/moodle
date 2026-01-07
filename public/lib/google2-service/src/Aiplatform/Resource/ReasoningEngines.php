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
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListReasoningEnginesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1QueryReasoningEngineRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1QueryReasoningEngineResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ReasoningEngine;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1StreamQueryReasoningEngineRequest;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "reasoningEngines" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $reasoningEngines = $aiplatformService->reasoningEngines;
 *  </code>
 */
class ReasoningEngines extends \Google\Service\Resource
{
  /**
   * Creates a reasoning engine. (reasoningEngines.create)
   *
   * @param GoogleCloudAiplatformV1ReasoningEngine $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. The resource name of the Location to
   * create the ReasoningEngine in. Format:
   * `projects/{project}/locations/{location}`
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create(GoogleCloudAiplatformV1ReasoningEngine $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a reasoning engine. (reasoningEngines.delete)
   *
   * @param string $name Required. The name of the ReasoningEngine resource to be
   * deleted. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, child resources of this
   * reasoning engine will also be deleted. Otherwise, the request will fail with
   * FAILED_PRECONDITION error when the reasoning engine has undeleted child
   * resources.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a reasoning engine. (reasoningEngines.get)
   *
   * @param string $name Required. The name of the ReasoningEngine resource.
   * Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ReasoningEngine
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1ReasoningEngine::class);
  }
  /**
   * Lists reasoning engines in a location.
   * (reasoningEngines.listReasoningEngines)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The standard list filter. More detail in
   * [AIP-160](https://google.aip.dev/160).
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token.
   * @opt_param string parent Required. The resource name of the Location to list
   * the ReasoningEngines from. Format: `projects/{project}/locations/{location}`
   * @return GoogleCloudAiplatformV1ListReasoningEnginesResponse
   * @throws \Google\Service\Exception
   */
  public function listReasoningEngines($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListReasoningEnginesResponse::class);
  }
  /**
   * Updates a reasoning engine. (reasoningEngines.patch)
   *
   * @param string $name Identifier. The resource name of the ReasoningEngine.
   * Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param GoogleCloudAiplatformV1ReasoningEngine $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Mask specifying which fields to
   * update.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1ReasoningEngine $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Queries using a reasoning engine. (reasoningEngines.query)
   *
   * @param string $name Required. The name of the ReasoningEngine resource to
   * use. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param GoogleCloudAiplatformV1QueryReasoningEngineRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1QueryReasoningEngineResponse
   * @throws \Google\Service\Exception
   */
  public function query($name, GoogleCloudAiplatformV1QueryReasoningEngineRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('query', [$params], GoogleCloudAiplatformV1QueryReasoningEngineResponse::class);
  }
  /**
   * Streams queries using a reasoning engine. (reasoningEngines.streamQuery)
   *
   * @param string $name Required. The name of the ReasoningEngine resource to
   * use. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param GoogleCloudAiplatformV1StreamQueryReasoningEngineRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleApiHttpBody
   * @throws \Google\Service\Exception
   */
  public function streamQuery($name, GoogleCloudAiplatformV1StreamQueryReasoningEngineRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('streamQuery', [$params], GoogleApiHttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReasoningEngines::class, 'Google_Service_Aiplatform_Resource_ReasoningEngines');
