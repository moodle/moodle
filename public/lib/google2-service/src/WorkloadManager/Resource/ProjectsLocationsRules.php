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

namespace Google\Service\WorkloadManager\Resource;

use Google\Service\WorkloadManager\ListRulesResponse;

/**
 * The "rules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workloadmanagerService = new Google\Service\WorkloadManager(...);
 *   $rules = $workloadmanagerService->projects_locations_rules;
 *  </code>
 */
class ProjectsLocationsRules extends \Google\Service\Resource
{
  /**
   * Lists rules in a given project. (rules.listProjectsLocationsRules)
   *
   * @param string $parent Required. The [project] on which to execute the
   * request. The format is: projects/{project_id}/locations/{location} Currently,
   * the pre-defined rules are global available to all projects and all regions
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customRulesBucket The Cloud Storage bucket name for custom
   * rules.
   * @opt_param string evaluationType Optional. The evaluation type of the rules
   * will be applied to. The Cloud Storage bucket name for custom rules.
   * @opt_param string filter Filter based on primary_category, secondary_category
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRulesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRules::class, 'Google_Service_WorkloadManager_Resource_ProjectsLocationsRules');
