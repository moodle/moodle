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

namespace Google\Service\GKEHub\Resource;

use Google\Service\GKEHub\ListMembershipBindingsResponse;
use Google\Service\GKEHub\MembershipBinding;
use Google\Service\GKEHub\Operation;

/**
 * The "bindings" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkehubService = new Google\Service\GKEHub(...);
 *   $bindings = $gkehubService->projects_locations_memberships_bindings;
 *  </code>
 */
class ProjectsLocationsMembershipsBindings extends \Google\Service\Resource
{
  /**
   * Creates a MembershipBinding. (bindings.create)
   *
   * @param string $parent Required. The parent (project and location) where the
   * MembershipBinding will be created. Specified in the format
   * `projects/locations/memberships`.
   * @param MembershipBinding $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string membershipBindingId Required. The ID to use for the
   * MembershipBinding.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, MembershipBinding $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a MembershipBinding. (bindings.delete)
   *
   * @param string $name Required. The MembershipBinding resource name in the
   * format `projects/locations/memberships/bindings`.
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
   * Returns the details of a MembershipBinding. (bindings.get)
   *
   * @param string $name Required. The MembershipBinding resource name in the
   * format `projects/locations/memberships/bindings`.
   * @param array $optParams Optional parameters.
   * @return MembershipBinding
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MembershipBinding::class);
  }
  /**
   * Lists MembershipBindings. (bindings.listProjectsLocationsMembershipsBindings)
   *
   * @param string $parent Required. The parent Membership for which the
   * MembershipBindings will be listed. Specified in the format
   * `projects/locations/memberships`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Lists MembershipBindings that match the
   * filter expression, following the syntax outlined in
   * https://google.aip.dev/160.
   * @opt_param int pageSize Optional. When requesting a 'page' of resources,
   * `page_size` specifies number of resources to return. If unspecified or set to
   * 0, all resources will be returned.
   * @opt_param string pageToken Optional. Token returned by previous call to
   * `ListMembershipBindings` which specifies the position in the list from where
   * to continue listing the resources.
   * @return ListMembershipBindingsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsMembershipsBindings($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMembershipBindingsResponse::class);
  }
  /**
   * Updates a MembershipBinding. (bindings.patch)
   *
   * @param string $name The resource name for the membershipbinding itself `proje
   * cts/{project}/locations/{location}/memberships/{membership}/bindings/{members
   * hipbinding}`
   * @param MembershipBinding $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The fields to be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, MembershipBinding $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsMembershipsBindings::class, 'Google_Service_GKEHub_Resource_ProjectsLocationsMembershipsBindings');
