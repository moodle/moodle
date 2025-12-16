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

use Google\Service\WorkflowExecutions\ListStepEntriesResponse;
use Google\Service\WorkflowExecutions\StepEntry;

/**
 * The "stepEntries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workflowexecutionsService = new Google\Service\WorkflowExecutions(...);
 *   $stepEntries = $workflowexecutionsService->projects_locations_workflows_executions_stepEntries;
 *  </code>
 */
class ProjectsLocationsWorkflowsExecutionsStepEntries extends \Google\Service\Resource
{
  /**
   * Gets a step entry. (stepEntries.get)
   *
   * @param string $name Required. The name of the step entry to retrieve. Format:
   * projects/{project}/locations/{location}/workflows/{workflow}/executions/{exec
   * ution}/stepEntries/{step_entry}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Deprecated field.
   * @return StepEntry
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], StepEntry::class);
  }
  /**
   * Lists step entries for the corresponding workflow execution. Returned entries
   * are ordered by their create_time.
   * (stepEntries.listProjectsLocationsWorkflowsExecutionsStepEntries)
   *
   * @param string $parent Required. Name of the workflow execution to list
   * entries for. Format: projects/{project}/locations/{location}/workflows/{workf
   * low}/executions/{execution}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filters applied to the
   * `[StepEntries.ListStepEntries]` results. The following fields are supported
   * for filtering: `entryId`, `createTime`, `updateTime`, `routine`, `step`,
   * `stepType`, `parent`, `state`. For details, see AIP-160. For example, if you
   * are using the Google APIs Explorer: `state="SUCCEEDED"` or
   * `createTime>"2023-08-01" AND state="FAILED"`
   * @opt_param string orderBy Optional. Comma-separated list of fields that
   * specify the ordering applied to the `[StepEntries.ListStepEntries]` results.
   * By default the ordering is based on ascending `entryId`. The following fields
   * are supported for ordering: `entryId`, `createTime`, `updateTime`, `routine`,
   * `step`, `stepType`, `state`. For details, see AIP-132.
   * @opt_param int pageSize Optional. Number of step entries to return per call.
   * The default max is 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListStepEntries` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListStepEntries` must match the
   * call that provided the page token.
   * @opt_param int skip Optional. The number of step entries to skip. It can be
   * used with or without a pageToken. If used with a pageToken, then it indicates
   * the number of step entries to skip starting from the requested page.
   * @opt_param string view Deprecated field.
   * @return ListStepEntriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsWorkflowsExecutionsStepEntries($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListStepEntriesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsWorkflowsExecutionsStepEntries::class, 'Google_Service_WorkflowExecutions_Resource_ProjectsLocationsWorkflowsExecutionsStepEntries');
