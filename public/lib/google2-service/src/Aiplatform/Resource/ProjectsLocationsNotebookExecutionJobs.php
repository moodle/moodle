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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListNotebookExecutionJobsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1NotebookExecutionJob;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "notebookExecutionJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $notebookExecutionJobs = $aiplatformService->projects_locations_notebookExecutionJobs;
 *  </code>
 */
class ProjectsLocationsNotebookExecutionJobs extends \Google\Service\Resource
{
  /**
   * Creates a NotebookExecutionJob. (notebookExecutionJobs.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the NotebookExecutionJob. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1NotebookExecutionJob $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string notebookExecutionJobId Optional. User specified ID for the
   * NotebookExecutionJob.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1NotebookExecutionJob $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a NotebookExecutionJob. (notebookExecutionJobs.delete)
   *
   * @param string $name Required. The name of the NotebookExecutionJob resource
   * to be deleted.
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
   * Gets a NotebookExecutionJob. (notebookExecutionJobs.get)
   *
   * @param string $name Required. The name of the NotebookExecutionJob resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. The NotebookExecutionJob view. Defaults to
   * BASIC.
   * @return GoogleCloudAiplatformV1NotebookExecutionJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1NotebookExecutionJob::class);
  }
  /**
   * Lists NotebookExecutionJobs in a Location.
   * (notebookExecutionJobs.listProjectsLocationsNotebookExecutionJobs)
   *
   * @param string $parent Required. The resource name of the Location from which
   * to list the NotebookExecutionJobs. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. For field names both snake_case and camelCase are supported. *
   * `notebookExecutionJob` supports = and !=. `notebookExecutionJob` represents
   * the NotebookExecutionJob ID. * `displayName` supports = and != and regex. *
   * `schedule` supports = and != and regex. Some examples: *
   * `notebookExecutionJob="123"` * `notebookExecutionJob="my-execution-job"` *
   * `displayName="myDisplayName"` and `displayName=~"myDisplayNameRegex"`
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `display_name` * `create_time` * `update_time` Example:
   * `display_name, create_time desc`.
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token. Typically
   * obtained via ListNotebookExecutionJobsResponse.next_page_token of the
   * previous NotebookService.ListNotebookExecutionJobs call.
   * @opt_param string view Optional. The NotebookExecutionJob view. Defaults to
   * BASIC.
   * @return GoogleCloudAiplatformV1ListNotebookExecutionJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNotebookExecutionJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListNotebookExecutionJobsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNotebookExecutionJobs::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsNotebookExecutionJobs');
