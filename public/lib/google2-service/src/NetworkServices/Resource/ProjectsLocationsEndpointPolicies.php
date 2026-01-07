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

namespace Google\Service\NetworkServices\Resource;

use Google\Service\NetworkServices\EndpointPolicy;
use Google\Service\NetworkServices\ListEndpointPoliciesResponse;
use Google\Service\NetworkServices\Operation;

/**
 * The "endpointPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkservicesService = new Google\Service\NetworkServices(...);
 *   $endpointPolicies = $networkservicesService->projects_locations_endpointPolicies;
 *  </code>
 */
class ProjectsLocationsEndpointPolicies extends \Google\Service\Resource
{
  /**
   * Creates a new EndpointPolicy in a given project and location.
   * (endpointPolicies.create)
   *
   * @param string $parent Required. The parent resource of the EndpointPolicy.
   * Must be in the format `projects/locations`.
   * @param EndpointPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string endpointPolicyId Required. Short name of the EndpointPolicy
   * resource to be created. E.g. "CustomECS".
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, EndpointPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single EndpointPolicy. (endpointPolicies.delete)
   *
   * @param string $name Required. A name of the EndpointPolicy to delete. Must be
   * in the format `projects/locations/endpointPolicies`.
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
   * Gets details of a single EndpointPolicy. (endpointPolicies.get)
   *
   * @param string $name Required. A name of the EndpointPolicy to get. Must be in
   * the format `projects/locations/endpointPolicies`.
   * @param array $optParams Optional parameters.
   * @return EndpointPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], EndpointPolicy::class);
  }
  /**
   * Lists EndpointPolicies in a given project and location.
   * (endpointPolicies.listProjectsLocationsEndpointPolicies)
   *
   * @param string $parent Required. The project and location from which the
   * EndpointPolicies should be listed, specified in the format
   * `projects/locations`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of EndpointPolicies to return per
   * call.
   * @opt_param string pageToken The value returned by the last
   * `ListEndpointPoliciesResponse` Indicates that this is a continuation of a
   * prior `ListEndpointPolicies` call, and that the system should return the next
   * page of data.
   * @opt_param bool returnPartialSuccess Optional. If true, allow partial
   * responses for multi-regional Aggregated List requests. Otherwise if one of
   * the locations is down or unreachable, the Aggregated List request will fail.
   * @return ListEndpointPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEndpointPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListEndpointPoliciesResponse::class);
  }
  /**
   * Updates the parameters of a single EndpointPolicy. (endpointPolicies.patch)
   *
   * @param string $name Identifier. Name of the EndpointPolicy resource. It
   * matches pattern
   * `projects/{project}/locations/endpointPolicies/{endpoint_policy}`.
   * @param EndpointPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the EndpointPolicy resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, EndpointPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEndpointPolicies::class, 'Google_Service_NetworkServices_Resource_ProjectsLocationsEndpointPolicies');
