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

use Google\Service\NetworkServices\ListServiceLbPoliciesResponse;
use Google\Service\NetworkServices\Operation;
use Google\Service\NetworkServices\ServiceLbPolicy;

/**
 * The "serviceLbPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkservicesService = new Google\Service\NetworkServices(...);
 *   $serviceLbPolicies = $networkservicesService->projects_locations_serviceLbPolicies;
 *  </code>
 */
class ProjectsLocationsServiceLbPolicies extends \Google\Service\Resource
{
  /**
   * Creates a new ServiceLbPolicy in a given project and location.
   * (serviceLbPolicies.create)
   *
   * @param string $parent Required. The parent resource of the ServiceLbPolicy.
   * Must be in the format `projects/{project}/locations/{location}`.
   * @param ServiceLbPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string serviceLbPolicyId Required. Short name of the
   * ServiceLbPolicy resource to be created. E.g. for resource name `projects/{pro
   * ject}/locations/{location}/serviceLbPolicies/{service_lb_policy_name}`. the
   * id is value of {service_lb_policy_name}
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, ServiceLbPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single ServiceLbPolicy. (serviceLbPolicies.delete)
   *
   * @param string $name Required. A name of the ServiceLbPolicy to delete. Must
   * be in the format `projects/{project}/locations/{location}/serviceLbPolicies`.
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
   * Gets details of a single ServiceLbPolicy. (serviceLbPolicies.get)
   *
   * @param string $name Required. A name of the ServiceLbPolicy to get. Must be
   * in the format `projects/{project}/locations/{location}/serviceLbPolicies`.
   * @param array $optParams Optional parameters.
   * @return ServiceLbPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ServiceLbPolicy::class);
  }
  /**
   * Lists ServiceLbPolicies in a given project and location.
   * (serviceLbPolicies.listProjectsLocationsServiceLbPolicies)
   *
   * @param string $parent Required. The project and location from which the
   * ServiceLbPolicies should be listed, specified in the format
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of ServiceLbPolicies to return per
   * call.
   * @opt_param string pageToken The value returned by the last
   * `ListServiceLbPoliciesResponse` Indicates that this is a continuation of a
   * prior `ListRouters` call, and that the system should return the next page of
   * data.
   * @return ListServiceLbPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsServiceLbPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListServiceLbPoliciesResponse::class);
  }
  /**
   * Updates the parameters of a single ServiceLbPolicy. (serviceLbPolicies.patch)
   *
   * @param string $name Identifier. Name of the ServiceLbPolicy resource. It
   * matches pattern `projects/{project}/locations/{location}/serviceLbPolicies/{s
   * ervice_lb_policy_name}`.
   * @param ServiceLbPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the ServiceLbPolicy resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, ServiceLbPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsServiceLbPolicies::class, 'Google_Service_NetworkServices_Resource_ProjectsLocationsServiceLbPolicies');
