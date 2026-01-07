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

use Google\Service\GKEHub\ListMembershipFeaturesResponse;
use Google\Service\GKEHub\MembershipFeature;
use Google\Service\GKEHub\Operation;

/**
 * The "features" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkehubService = new Google\Service\GKEHub(...);
 *   $features = $gkehubService->projects_locations_memberships_features;
 *  </code>
 */
class ProjectsLocationsMembershipsFeatures extends \Google\Service\Resource
{
  /**
   * Creates membershipFeature under a given parent. (features.create)
   *
   * @param string $parent Required. The name of parent where the
   * MembershipFeature will be created. Specified in the format
   * `projects/locations/memberships`.
   * @param MembershipFeature $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string featureId Required. The ID of the membership_feature to
   * create.
   * @opt_param string requestId Idempotent request UUID.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, MembershipFeature $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Removes a membershipFeature. (features.delete)
   *
   * @param string $name Required. The name of the membershipFeature to be
   * deleted. Specified in the format `projects/locations/memberships/features`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Idempotent request UUID.
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
   * ========= MembershipFeature Services ========= Gets details of a
   * membershipFeature. (features.get)
   *
   * @param string $name Required. The MembershipFeature resource name in the
   * format `projects/locations/memberships/features`.
   * @param array $optParams Optional parameters.
   * @return MembershipFeature
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MembershipFeature::class);
  }
  /**
   * Lists MembershipFeatures in a given project and location.
   * (features.listProjectsLocationsMembershipsFeatures)
   *
   * @param string $parent Required. The parent where the MembershipFeature will
   * be listed. In the format: `projects/locations/memberships`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists MembershipFeatures that match the filter
   * expression, following the syntax outlined in https://google.aip.dev/160.
   * Examples: - Feature with the name "helloworld" in project "foo-proj" and
   * membership "member-bar": name = "projects/foo-
   * proj/locations/global/memberships/member-bar/features/helloworld" - Features
   * that have a label called `foo`: labels.foo:* - Features that have a label
   * called `foo` whose value is `bar`: labels.foo = bar
   * @opt_param string orderBy One or more fields to compare and use to sort the
   * output. See https://google.aip.dev/132#ordering.
   * @opt_param int pageSize When requesting a 'page' of resources, `page_size`
   * specifies number of resources to return. If unspecified or set to 0, all
   * resources will be returned.
   * @opt_param string pageToken Token returned by previous call to `ListFeatures`
   * which specifies the position in the list from where to continue listing the
   * resources.
   * @return ListMembershipFeaturesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsMembershipsFeatures($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMembershipFeaturesResponse::class);
  }
  /**
   * Updates an existing MembershipFeature. (features.patch)
   *
   * @param string $name Output only. The resource name of the membershipFeature,
   * in the format: `projects/{project}/locations/{location}/memberships/{membersh
   * ip}/features/{feature}`. Note that `membershipFeatures` is shortened to
   * `features` in the resource name. (see http://go/aip/122#collection-
   * identifiers)
   * @param MembershipFeature $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the
   * MembershipFeature is not found, a new MembershipFeature will be created. In
   * this situation, `update_mask` is ignored.
   * @opt_param string requestId Idempotent request UUID.
   * @opt_param string updateMask Required. Mask of fields to update.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, MembershipFeature $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsMembershipsFeatures::class, 'Google_Service_GKEHub_Resource_ProjectsLocationsMembershipsFeatures');
