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

namespace Google\Service\WorkflowExecutions\Resource;

use Google\Service\WorkflowExecutions\ListCallbacksResponse;

/**
 * The "callbacks" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workflowexecutionsService = new Google\Service\WorkflowExecutions(...);
 *   $callbacks = $workflowexecutionsService->projects_locations_workflows_executions_callbacks;
 *  </code>
 */
class ProjectsLocationsWorkflowsExecutionsCallbacks extends \Google\Service\Resource
{
  /**
   * Returns a list of active callbacks that belong to the execution with the
   * given name. The returned callbacks are ordered by callback ID.
   * (callbacks.listProjectsLocationsWorkflowsExecutionsCallbacks)
   *
   * @param string $parent Required. Name of the execution for which the callbacks
   * should be listed. Format: projects/{project}/locations/{location}/workflows/{
   * workflow}/executions/{execution}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of callbacks to return per call. The
   * default value is 100 and is also the maximum value.
   * @opt_param string pageToken A page token, received from a previous
   * `ListCallbacks` call. Provide this to retrieve the subsequent page. Note that
   * pagination is applied to dynamic data. The list of callbacks returned can
   * change between page requests if callbacks are created or deleted.
   * @return ListCallbacksResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsWorkflowsExecutionsCallbacks($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCallbacksResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsWorkflowsExecutionsCallbacks::class, 'Google_Service_WorkflowExecutions_Resource_ProjectsLocationsWorkflowsExecutionsCallbacks');
