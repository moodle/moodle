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

use Google\Service\NetworkSecurity\GatewaySecurityPolicy;
use Google\Service\NetworkSecurity\ListGatewaySecurityPoliciesResponse;
use Google\Service\NetworkSecurity\Operation;

/**
 * The "gatewaySecurityPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $gatewaySecurityPolicies = $networksecurityService->projects_locations_gatewaySecurityPolicies;
 *  </code>
 */
class ProjectsLocationsGatewaySecurityPolicies extends \Google\Service\Resource
{
  /**
   * Creates a new GatewaySecurityPolicy in a given project and location.
   * (gatewaySecurityPolicies.create)
   *
   * @param string $parent Required. The parent resource of the
   * GatewaySecurityPolicy. Must be in the format
   * `projects/{project}/locations/{location}`.
   * @param GatewaySecurityPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string gatewaySecurityPolicyId Required. Short name of the
   * GatewaySecurityPolicy resource to be created. This value should be 1-63
   * characters long, containing only letters, numbers, hyphens, and underscores,
   * and should not start with a number. E.g. "gateway_security_policy1".
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GatewaySecurityPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single GatewaySecurityPolicy. (gatewaySecurityPolicies.delete)
   *
   * @param string $name Required. A name of the GatewaySecurityPolicy to delete.
   * Must be in the format
   * `projects/{project}/locations/{location}/gatewaySecurityPolicies`.
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
   * Gets details of a single GatewaySecurityPolicy. (gatewaySecurityPolicies.get)
   *
   * @param string $name Required. A name of the GatewaySecurityPolicy to get.
   * Must be in the format
   * `projects/{project}/locations/{location}/gatewaySecurityPolicies`.
   * @param array $optParams Optional parameters.
   * @return GatewaySecurityPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GatewaySecurityPolicy::class);
  }
  /**
   * Lists GatewaySecurityPolicies in a given project and location.
   * (gatewaySecurityPolicies.listProjectsLocationsGatewaySecurityPolicies)
   *
   * @param string $parent Required. The project and location from which the
   * GatewaySecurityPolicies should be listed, specified in the format
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of GatewaySecurityPolicies to return
   * per call.
   * @opt_param string pageToken The value returned by the last
   * 'ListGatewaySecurityPoliciesResponse' Indicates that this is a continuation
   * of a prior 'ListGatewaySecurityPolicies' call, and that the system should
   * return the next page of data.
   * @return ListGatewaySecurityPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGatewaySecurityPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListGatewaySecurityPoliciesResponse::class);
  }
  /**
   * Updates the parameters of a single GatewaySecurityPolicy.
   * (gatewaySecurityPolicies.patch)
   *
   * @param string $name Required. Name of the resource. Name is of the form proje
   * cts/{project}/locations/{location}/gatewaySecurityPolicies/{gateway_security_
   * policy} gateway_security_policy should match the
   * pattern:(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
   * @param GatewaySecurityPolicy $postBody
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
  public function patch($name, GatewaySecurityPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsGatewaySecurityPolicies::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsGatewaySecurityPolicies');
