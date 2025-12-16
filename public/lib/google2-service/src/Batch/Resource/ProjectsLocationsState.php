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

namespace Google\Service\Batch\Resource;

use Google\Service\Batch\ReportAgentStateRequest;
use Google\Service\Batch\ReportAgentStateResponse;

/**
 * The "state" collection of methods.
 * Typical usage is:
 *  <code>
 *   $batchService = new Google\Service\Batch(...);
 *   $state = $batchService->projects_locations_state;
 *  </code>
 */
class ProjectsLocationsState extends \Google\Service\Resource
{
  /**
   * Report agent's state, e.g. agent status and tasks information (state.report)
   *
   * @param string $parent Required. Format:
   * projects/{project}/locations/{location} {project} should be a project number.
   * @param ReportAgentStateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ReportAgentStateResponse
   * @throws \Google\Service\Exception
   */
  public function report($parent, ReportAgentStateRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('report', [$params], ReportAgentStateResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsState::class, 'Google_Service_Batch_Resource_ProjectsLocationsState');
