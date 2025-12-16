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

namespace Google\Service\NetAppFiles\Resource;

use Google\Service\NetAppFiles\ListQuotaRulesResponse;
use Google\Service\NetAppFiles\Operation;
use Google\Service\NetAppFiles\QuotaRule;

/**
 * The "quotaRules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $quotaRules = $netappService->projects_locations_volumes_quotaRules;
 *  </code>
 */
class ProjectsLocationsVolumesQuotaRules extends \Google\Service\Resource
{
  /**
   * Creates a new quota rule. (quotaRules.create)
   *
   * @param string $parent Required. Parent value for CreateQuotaRuleRequest
   * @param QuotaRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string quotaRuleId Required. ID of the quota rule to create. Must
   * be unique within the parent resource. Must contain only letters, numbers,
   * underscore and hyphen, with the first character a letter or underscore, the
   * last a letter or underscore or a number, and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, QuotaRule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a quota rule. (quotaRules.delete)
   *
   * @param string $name Required. Name of the quota rule.
   * @param array $optParams Optional parameters.
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
   * Returns details of the specified quota rule. (quotaRules.get)
   *
   * @param string $name Required. Name of the quota rule
   * @param array $optParams Optional parameters.
   * @return QuotaRule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], QuotaRule::class);
  }
  /**
   * Returns list of all quota rules in a location.
   * (quotaRules.listProjectsLocationsVolumesQuotaRules)
   *
   * @param string $parent Required. Parent value for ListQuotaRulesRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, the server will pick an
   * appropriate default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListQuotaRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsVolumesQuotaRules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListQuotaRulesResponse::class);
  }
  /**
   * Updates a quota rule. (quotaRules.patch)
   *
   * @param string $name Identifier. The resource name of the quota rule. Format:
   * `projects/{project_number}/locations/{location_id}/volumes/volumes/{volume_id
   * }/quotaRules/{quota_rule_id}`.
   * @param QuotaRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the Quota Rule resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, QuotaRule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsVolumesQuotaRules::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsVolumesQuotaRules');
