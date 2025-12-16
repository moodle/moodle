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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1EvaluationSet;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListEvaluationSetsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "evaluationSets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $evaluationSets = $aiplatformService->projects_locations_evaluationSets;
 *  </code>
 */
class ProjectsLocationsEvaluationSets extends \Google\Service\Resource
{
  /**
   * Creates an Evaluation Set. (evaluationSets.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the Evaluation Set in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1EvaluationSet $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1EvaluationSet
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1EvaluationSet $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1EvaluationSet::class);
  }
  /**
   * Deletes an Evaluation Set. (evaluationSets.delete)
   *
   * @param string $name Required. The name of the EvaluationSet resource to be
   * deleted. Format:
   * `projects/{project}/locations/{location}/evaluationSets/{evaluation_set}`
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
   * Gets an Evaluation Set. (evaluationSets.get)
   *
   * @param string $name Required. The name of the EvaluationSet resource. Format:
   * `projects/{project}/locations/{location}/evaluationSets/{evaluation_set}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1EvaluationSet
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1EvaluationSet::class);
  }
  /**
   * Lists Evaluation Sets. (evaluationSets.listProjectsLocationsEvaluationSets)
   *
   * @param string $parent Required. The resource name of the Location from which
   * to list the Evaluation Sets. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that matches a subset of
   * the EvaluationSets to show. For field names both snake_case and camelCase are
   * supported. For more information about filter syntax, see
   * [AIP-160](https://google.aip.dev/160).
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order by default. Use `desc` after a field name for
   * descending.
   * @opt_param int pageSize Optional. The maximum number of Evaluation Sets to
   * return.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListEvaluationSets` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudAiplatformV1ListEvaluationSetsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEvaluationSets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListEvaluationSetsResponse::class);
  }
  /**
   * Updates an Evaluation Set. (evaluationSets.patch)
   *
   * @param string $name Identifier. The resource name of the EvaluationSet.
   * Format:
   * `projects/{project}/locations/{location}/evaluationSets/{evaluation_set}`
   * @param GoogleCloudAiplatformV1EvaluationSet $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The update mask applies to the
   * resource. For the `FieldMask` definition, see google.protobuf.FieldMask.
   * @return GoogleCloudAiplatformV1EvaluationSet
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1EvaluationSet $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1EvaluationSet::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEvaluationSets::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsEvaluationSets');
