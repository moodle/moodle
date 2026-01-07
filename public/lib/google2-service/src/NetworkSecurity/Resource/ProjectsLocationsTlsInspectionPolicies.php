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

use Google\Service\NetworkSecurity\ListTlsInspectionPoliciesResponse;
use Google\Service\NetworkSecurity\Operation;
use Google\Service\NetworkSecurity\TlsInspectionPolicy;

/**
 * The "tlsInspectionPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $tlsInspectionPolicies = $networksecurityService->projects_locations_tlsInspectionPolicies;
 *  </code>
 */
class ProjectsLocationsTlsInspectionPolicies extends \Google\Service\Resource
{
  /**
   * Creates a new TlsInspectionPolicy in a given project and location.
   * (tlsInspectionPolicies.create)
   *
   * @param string $parent Required. The parent resource of the
   * TlsInspectionPolicy. Must be in the format
   * `projects/{project}/locations/{location}`.
   * @param TlsInspectionPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string tlsInspectionPolicyId Required. Short name of the
   * TlsInspectionPolicy resource to be created. This value should be 1-63
   * characters long, containing only letters, numbers, hyphens, and underscores,
   * and should not start with a number. E.g. "tls_inspection_policy1".
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, TlsInspectionPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single TlsInspectionPolicy. (tlsInspectionPolicies.delete)
   *
   * @param string $name Required. A name of the TlsInspectionPolicy to delete.
   * Must be in the format `projects/{project}/locations/{location}/tlsInspectionP
   * olicies/{tls_inspection_policy}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force If set to true, any rules for this TlsInspectionPolicy
   * will also be deleted. (Otherwise, the request will only work if the
   * TlsInspectionPolicy has no rules.)
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
   * Gets details of a single TlsInspectionPolicy. (tlsInspectionPolicies.get)
   *
   * @param string $name Required. A name of the TlsInspectionPolicy to get. Must
   * be in the format `projects/{project}/locations/{location}/tlsInspectionPolici
   * es/{tls_inspection_policy}`.
   * @param array $optParams Optional parameters.
   * @return TlsInspectionPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], TlsInspectionPolicy::class);
  }
  /**
   * Lists TlsInspectionPolicies in a given project and location.
   * (tlsInspectionPolicies.listProjectsLocationsTlsInspectionPolicies)
   *
   * @param string $parent Required. The project and location from which the
   * TlsInspectionPolicies should be listed, specified in the format
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of TlsInspectionPolicies to return per
   * call.
   * @opt_param string pageToken The value returned by the last
   * 'ListTlsInspectionPoliciesResponse' Indicates that this is a continuation of
   * a prior 'ListTlsInspectionPolicies' call, and that the system should return
   * the next page of data.
   * @return ListTlsInspectionPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsTlsInspectionPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTlsInspectionPoliciesResponse::class);
  }
  /**
   * Updates the parameters of a single TlsInspectionPolicy.
   * (tlsInspectionPolicies.patch)
   *
   * @param string $name Required. Name of the resource. Name is of the form proje
   * cts/{project}/locations/{location}/tlsInspectionPolicies/{tls_inspection_poli
   * cy} tls_inspection_policy should match the
   * pattern:(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
   * @param TlsInspectionPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the TlsInspectionPolicy resource by the update.
   * The fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, TlsInspectionPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTlsInspectionPolicies::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsTlsInspectionPolicies');
