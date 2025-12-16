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

namespace Google\Service\NetworkSecurity\Resource;

use Google\Service\NetworkSecurity\GatewaySecurityPolicyRule;
use Google\Service\NetworkSecurity\ListGatewaySecurityPolicyRulesResponse;
use Google\Service\NetworkSecurity\Operation;

/**
 * The "rules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $rules = $networksecurityService->projects_locations_gatewaySecurityPolicies_rules;
 *  </code>
 */
class ProjectsLocationsGatewaySecurityPoliciesRules extends \Google\Service\Resource
{
  /**
   * Creates a new GatewaySecurityPolicy in a given project and location.
   * (rules.create)
   *
   * @param string $parent Required. The parent where this rule will be created.
   * Format : projects/{project}/location/{location}/gatewaySecurityPolicies
   * @param GatewaySecurityPolicyRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string gatewaySecurityPolicyRuleId The ID to use for the rule,
   * which will become the final component of the rule's resource name. This value
   * should be 4-63 characters, and valid characters are /a-z-/.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GatewaySecurityPolicyRule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single GatewaySecurityPolicyRule. (rules.delete)
   *
   * @param string $name Required. A name of the GatewaySecurityPolicyRule to
   * delete. Must be in the format `projects/{project}/locations/{location}/gatewa
   * ySecurityPolicies/{gatewaySecurityPolicy}/rules`.
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
   * Gets details of a single GatewaySecurityPolicyRule. (rules.get)
   *
   * @param string $name Required. The name of the GatewaySecurityPolicyRule to
   * retrieve. Format:
   * projects/{project}/location/{location}/gatewaySecurityPolicies/rules
   * @param array $optParams Optional parameters.
   * @return GatewaySecurityPolicyRule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GatewaySecurityPolicyRule::class);
  }
  /**
   * Lists GatewaySecurityPolicyRules in a given project and location.
   * (rules.listProjectsLocationsGatewaySecurityPoliciesRules)
   *
   * @param string $parent Required. The project, location and
   * GatewaySecurityPolicy from which the GatewaySecurityPolicyRules should be
   * listed, specified in the format `projects/{project}/locations/{location}/gate
   * waySecurityPolicies/{gatewaySecurityPolicy}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of GatewaySecurityPolicyRules to
   * return per call.
   * @opt_param string pageToken The value returned by the last
   * 'ListGatewaySecurityPolicyRulesResponse' Indicates that this is a
   * continuation of a prior 'ListGatewaySecurityPolicyRules' call, and that the
   * system should return the next page of data.
   * @return ListGatewaySecurityPolicyRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGatewaySecurityPoliciesRules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListGatewaySecurityPolicyRulesResponse::class);
  }
  /**
   * Updates the parameters of a single GatewaySecurityPolicyRule. (rules.patch)
   *
   * @param string $name Required. Immutable. Name of the resource. ame is the
   * full resource name so projects/{project}/locations/{location}/gatewaySecurity
   * Policies/{gateway_security_policy}/rules/{rule} rule should match the
   * pattern: (^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
   * @param GatewaySecurityPolicyRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the GatewaySecurityPolicy resource by the update.
   * The fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GatewaySecurityPolicyRule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsGatewaySecurityPoliciesRules::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsGatewaySecurityPoliciesRules');
