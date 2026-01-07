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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1EvaluateDatasetRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1EvaluateInstancesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1EvaluateInstancesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1GenerateInstanceRubricsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1GenerateInstanceRubricsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "v1" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $v1 = $aiplatformService->v1;
 *  </code>
 */
class V1 extends \Google\Service\Resource
{
  /**
   * Evaluates a dataset based on a set of given metrics. (v1.evaluateDataset)
   *
   * @param GoogleCloudAiplatformV1EvaluateDatasetRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function evaluateDataset(GoogleCloudAiplatformV1EvaluateDatasetRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('evaluateDataset', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Evaluates instances based on a given metric. (v1.evaluateInstances)
   *
   * @param GoogleCloudAiplatformV1EvaluateInstancesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1EvaluateInstancesResponse
   * @throws \Google\Service\Exception
   */
  public function evaluateInstances(GoogleCloudAiplatformV1EvaluateInstancesRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('evaluateInstances', [$params], GoogleCloudAiplatformV1EvaluateInstancesResponse::class);
  }
  /**
   * Generates rubrics for a given prompt. A rubric represents a single testable
   * criterion for evaluation. One input prompt could have multiple rubrics This
   * RPC allows users to get suggested rubrics based on provided prompt, which can
   * then be reviewed and used for subsequent evaluations.
   * (v1.generateInstanceRubrics)
   *
   * @param GoogleCloudAiplatformV1GenerateInstanceRubricsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1GenerateInstanceRubricsResponse
   * @throws \Google\Service\Exception
   */
  public function generateInstanceRubrics(GoogleCloudAiplatformV1GenerateInstanceRubricsRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateInstanceRubrics', [$params], GoogleCloudAiplatformV1GenerateInstanceRubricsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V1::class, 'Google_Service_Aiplatform_Resource_V1');
