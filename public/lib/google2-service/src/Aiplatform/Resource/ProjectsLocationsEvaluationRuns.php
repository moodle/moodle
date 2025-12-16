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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1CancelEvaluationRunRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1EvaluationRun;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListEvaluationRunsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "evaluationRuns" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $evaluationRuns = $aiplatformService->projects_locations_evaluationRuns;
 *  </code>
 */
class ProjectsLocationsEvaluationRuns extends \Google\Service\Resource
{
  /**
   * Cancels an Evaluation Run. Attempts to cancel a running Evaluation Run
   * asynchronously. Status of run can be checked via GetEvaluationRun.
   * (evaluationRuns.cancel)
   *
   * @param string $name Required. The name of the EvaluationRun resource to be
   * cancelled. Format:
   * `projects/{project}/locations/{location}/evaluationRuns/{evaluation_run}`
   * @param GoogleCloudAiplatformV1CancelEvaluationRunRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, GoogleCloudAiplatformV1CancelEvaluationRunRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Creates an Evaluation Run. (evaluationRuns.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the Evaluation Run in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1EvaluationRun $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1EvaluationRun
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1EvaluationRun $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1EvaluationRun::class);
  }
  /**
   * Deletes an Evaluation Run. (evaluationRuns.delete)
   *
   * @param string $name Required. The name of the EvaluationRun resource to be
   * deleted. Format:
   * `projects/{project}/locations/{location}/evaluationRuns/{evaluation_run}`
   * @param array $optParams Optional parameters.
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
   * Gets an Evaluation Run. (evaluationRuns.get)
   *
   * @param string $name Required. The name of the EvaluationRun resource. Format:
   * `projects/{project}/locations/{location}/evaluationRuns/{evaluation_run}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1EvaluationRun
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1EvaluationRun::class);
  }
  /**
   * Lists Evaluation Runs. (evaluationRuns.listProjectsLocationsEvaluationRuns)
   *
   * @param string $parent Required. The resource name of the Location from which
   * to list the Evaluation Runs. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that matches a subset of
   * the EvaluationRuns to show. For field names both snake_case and camelCase are
   * supported. For more information about filter syntax, see
   * [AIP-160](https://google.aip.dev/160).
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order by default. Use `desc` after a field name for
   * descending.
   * @opt_param int pageSize Optional. The maximum number of Evaluation Runs to
   * return.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListEvaluationRuns` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudAiplatformV1ListEvaluationRunsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEvaluationRuns($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListEvaluationRunsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEvaluationRuns::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsEvaluationRuns');
