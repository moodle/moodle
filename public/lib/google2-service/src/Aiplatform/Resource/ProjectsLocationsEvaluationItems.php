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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1EvaluationItem;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListEvaluationItemsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "evaluationItems" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $evaluationItems = $aiplatformService->projects_locations_evaluationItems;
 *  </code>
 */
class ProjectsLocationsEvaluationItems extends \Google\Service\Resource
{
  /**
   * Creates an Evaluation Item. (evaluationItems.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the Evaluation Item in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1EvaluationItem $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1EvaluationItem
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1EvaluationItem $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1EvaluationItem::class);
  }
  /**
   * Deletes an Evaluation Item. (evaluationItems.delete)
   *
   * @param string $name Required. The name of the EvaluationItem resource to be
   * deleted. Format:
   * `projects/{project}/locations/{location}/evaluationItems/{evaluation_item}`
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
   * Gets an Evaluation Item. (evaluationItems.get)
   *
   * @param string $name Required. The name of the EvaluationItem resource.
   * Format:
   * `projects/{project}/locations/{location}/evaluationItems/{evaluation_item}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1EvaluationItem
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1EvaluationItem::class);
  }
  /**
   * Lists Evaluation Items.
   * (evaluationItems.listProjectsLocationsEvaluationItems)
   *
   * @param string $parent Required. The resource name of the Location from which
   * to list the Evaluation Items. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that matches a subset of
   * the EvaluationItems to show. For field names both snake_case and camelCase
   * are supported. For more information about filter syntax, see
   * [AIP-160](https://google.aip.dev/160).
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order by default. Use `desc` after a field name for
   * descending.
   * @opt_param int pageSize Optional. The maximum number of Evaluation Items to
   * return.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListEvaluationItems` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudAiplatformV1ListEvaluationItemsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEvaluationItems($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListEvaluationItemsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEvaluationItems::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsEvaluationItems');
