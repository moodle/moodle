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

namespace Google\Service\Dataform\Resource;

use Google\Service\Dataform\CancelWorkflowInvocationRequest;
use Google\Service\Dataform\CancelWorkflowInvocationResponse;
use Google\Service\Dataform\DataformEmpty;
use Google\Service\Dataform\ListWorkflowInvocationsResponse;
use Google\Service\Dataform\QueryWorkflowInvocationActionsResponse;
use Google\Service\Dataform\WorkflowInvocation;

/**
 * The "workflowInvocations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataformService = new Google\Service\Dataform(...);
 *   $workflowInvocations = $dataformService->projects_locations_repositories_workflowInvocations;
 *  </code>
 */
class ProjectsLocationsRepositoriesWorkflowInvocations extends \Google\Service\Resource
{
  /**
   * Requests cancellation of a running WorkflowInvocation.
   * (workflowInvocations.cancel)
   *
   * @param string $name Required. The workflow invocation resource's name.
   * @param CancelWorkflowInvocationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CancelWorkflowInvocationResponse
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelWorkflowInvocationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], CancelWorkflowInvocationResponse::class);
  }
  /**
   * Creates a new WorkflowInvocation in a given Repository.
   * (workflowInvocations.create)
   *
   * @param string $parent Required. The repository in which to create the
   * workflow invocation. Must be in the format `projects/locations/repositories`.
   * @param WorkflowInvocation $postBody
   * @param array $optParams Optional parameters.
   * @return WorkflowInvocation
   * @throws \Google\Service\Exception
   */
  public function create($parent, WorkflowInvocation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], WorkflowInvocation::class);
  }
  /**
   * Deletes a single WorkflowInvocation. (workflowInvocations.delete)
   *
   * @param string $name Required. The workflow invocation resource's name.
   * @param array $optParams Optional parameters.
   * @return DataformEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DataformEmpty::class);
  }
  /**
   * Fetches a single WorkflowInvocation. (workflowInvocations.get)
   *
   * @param string $name Required. The workflow invocation resource's name.
   * @param array $optParams Optional parameters.
   * @return WorkflowInvocation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], WorkflowInvocation::class);
  }
  /**
   * Lists WorkflowInvocations in a given Repository.
   * (workflowInvocations.listProjectsLocationsRepositoriesWorkflowInvocations)
   *
   * @param string $parent Required. The parent resource of the WorkflowInvocation
   * type. Must be in the format `projects/locations/repositories`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter for the returned list.
   * @opt_param string orderBy Optional. This field only supports ordering by
   * `name`. If unspecified, the server will choose the ordering. If specified,
   * the default order is ascending for the `name` field.
   * @opt_param int pageSize Optional. Maximum number of workflow invocations to
   * return. The server may return fewer items than requested. If unspecified, the
   * server will pick an appropriate default.
   * @opt_param string pageToken Optional. Page token received from a previous
   * `ListWorkflowInvocations` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListWorkflowInvocations`,
   * with the exception of `page_size`, must match the call that provided the page
   * token.
   * @return ListWorkflowInvocationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesWorkflowInvocations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWorkflowInvocationsResponse::class);
  }
  /**
   * Returns WorkflowInvocationActions in a given WorkflowInvocation.
   * (workflowInvocations.query)
   *
   * @param string $name Required. The workflow invocation's name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of workflow invocations to
   * return. The server may return fewer items than requested. If unspecified, the
   * server will pick an appropriate default.
   * @opt_param string pageToken Optional. Page token received from a previous
   * `QueryWorkflowInvocationActions` call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `QueryWorkflowInvocationActions`, with the exception of `page_size`, must
   * match the call that provided the page token.
   * @return QueryWorkflowInvocationActionsResponse
   * @throws \Google\Service\Exception
   */
  public function query($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('query', [$params], QueryWorkflowInvocationActionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesWorkflowInvocations::class, 'Google_Service_Dataform_Resource_ProjectsLocationsRepositoriesWorkflowInvocations');
