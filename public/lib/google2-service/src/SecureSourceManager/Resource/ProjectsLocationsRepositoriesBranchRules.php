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

namespace Google\Service\SecureSourceManager\Resource;

use Google\Service\SecureSourceManager\BranchRule;
use Google\Service\SecureSourceManager\ListBranchRulesResponse;
use Google\Service\SecureSourceManager\Operation;

/**
 * The "branchRules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securesourcemanagerService = new Google\Service\SecureSourceManager(...);
 *   $branchRules = $securesourcemanagerService->projects_locations_repositories_branchRules;
 *  </code>
 */
class ProjectsLocationsRepositoriesBranchRules extends \Google\Service\Resource
{
  /**
   * CreateBranchRule creates a branch rule in a given repository.
   * (branchRules.create)
   *
   * @param string $parent
   * @param BranchRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string branchRuleId
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, BranchRule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * DeleteBranchRule deletes a branch rule. (branchRules.delete)
   *
   * @param string $name
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the branch rule is
   * not found, the request will succeed but no action will be taken on the
   * server.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * GetBranchRule gets a branch rule. (branchRules.get)
   *
   * @param string $name Required. Name of the repository to retrieve. The format
   * is `projects/{project}/locations/{location}/repositories/{repository}/branchR
   * ules/{branch_rule}`.
   * @param array $optParams Optional parameters.
   * @return BranchRule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BranchRule::class);
  }
  /**
   * ListBranchRules lists branch rules in a given repository.
   * (branchRules.listProjectsLocationsRepositoriesBranchRules)
   *
   * @param string $parent
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize
   * @opt_param string pageToken
   * @return ListBranchRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesBranchRules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBranchRulesResponse::class);
  }
  /**
   * UpdateBranchRule updates a branch rule. (branchRules.patch)
   *
   * @param string $name Optional. A unique identifier for a BranchRule. The name
   * should be of the format: `projects/{project}/locations/{location}/repositorie
   * s/{repository}/branchRules/{branch_rule}`
   * @param BranchRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the branchRule resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. The special value
   * "*" means full replacement.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the review, but do not actually post it. (https://google.aip.dev/163,
   * for declarative friendly)
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, BranchRule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesBranchRules::class, 'Google_Service_SecureSourceManager_Resource_ProjectsLocationsRepositoriesBranchRules');
