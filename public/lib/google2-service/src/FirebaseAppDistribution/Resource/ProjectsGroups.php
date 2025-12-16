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

namespace Google\Service\FirebaseAppDistribution\Resource;

use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1BatchJoinGroupRequest;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1BatchLeaveGroupRequest;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1Group;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1ListGroupsResponse;
use Google\Service\FirebaseAppDistribution\GoogleProtobufEmpty;

/**
 * The "groups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebaseappdistributionService = new Google\Service\FirebaseAppDistribution(...);
 *   $groups = $firebaseappdistributionService->projects_groups;
 *  </code>
 */
class ProjectsGroups extends \Google\Service\Resource
{
  /**
   * Batch adds members to a group. The testers will gain access to all releases
   * that the groups have access to. (groups.batchJoin)
   *
   * @param string $group Required. The name of the group resource to which
   * testers are added. Format: `projects/{project_number}/groups/{group_alias}`
   * @param GoogleFirebaseAppdistroV1BatchJoinGroupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function batchJoin($group, GoogleFirebaseAppdistroV1BatchJoinGroupRequest $postBody, $optParams = [])
  {
    $params = ['group' => $group, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchJoin', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Batch removed members from a group. The testers will lose access to all
   * releases that the groups have access to. (groups.batchLeave)
   *
   * @param string $group Required. The name of the group resource from which
   * testers are removed. Format: `projects/{project_number}/groups/{group_alias}`
   * @param GoogleFirebaseAppdistroV1BatchLeaveGroupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function batchLeave($group, GoogleFirebaseAppdistroV1BatchLeaveGroupRequest $postBody, $optParams = [])
  {
    $params = ['group' => $group, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchLeave', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Create a group. (groups.create)
   *
   * @param string $parent Required. The name of the project resource, which is
   * the parent of the group resource. Format: `projects/{project_number}`
   * @param GoogleFirebaseAppdistroV1Group $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string groupId Optional. The "alias" to use for the group, which
   * will become the final component of the group's resource name. This value must
   * be unique per project. The field is named `groupId` to comply with AIP
   * guidance for user-specified IDs. This value should be 4-63 characters, and
   * valid characters are `/a-z-/`. If not set, it will be generated based on the
   * display name.
   * @return GoogleFirebaseAppdistroV1Group
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleFirebaseAppdistroV1Group $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleFirebaseAppdistroV1Group::class);
  }
  /**
   * Delete a group. (groups.delete)
   *
   * @param string $name Required. The name of the group resource. Format:
   * `projects/{project_number}/groups/{group_alias}`
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Get a group. (groups.get)
   *
   * @param string $name Required. The name of the group resource to retrieve.
   * Format: `projects/{project_number}/groups/{group_alias}`
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppdistroV1Group
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleFirebaseAppdistroV1Group::class);
  }
  /**
   * List groups. (groups.listProjectsGroups)
   *
   * @param string $parent Required. The name of the project resource, which is
   * the parent of the group resources. Format: `projects/{project_number}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of groups to return. The
   * service may return fewer than this value. The valid range is [1-1000]; If
   * unspecified (0), at most 25 groups are returned. Values above 1000 are
   * coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListGroups` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListGroups` must match the call
   * that provided the page token.
   * @return GoogleFirebaseAppdistroV1ListGroupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsGroups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleFirebaseAppdistroV1ListGroupsResponse::class);
  }
  /**
   * Update a group. (groups.patch)
   *
   * @param string $name The name of the group resource. Format:
   * `projects/{project_number}/groups/{group_alias}`
   * @param GoogleFirebaseAppdistroV1Group $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return GoogleFirebaseAppdistroV1Group
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleFirebaseAppdistroV1Group $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleFirebaseAppdistroV1Group::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsGroups::class, 'Google_Service_FirebaseAppDistribution_Resource_ProjectsGroups');
