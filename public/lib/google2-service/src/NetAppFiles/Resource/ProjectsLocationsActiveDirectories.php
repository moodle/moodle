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

use Google\Service\NetAppFiles\ActiveDirectory;
use Google\Service\NetAppFiles\ListActiveDirectoriesResponse;
use Google\Service\NetAppFiles\Operation;

/**
 * The "activeDirectories" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $activeDirectories = $netappService->projects_locations_activeDirectories;
 *  </code>
 */
class ProjectsLocationsActiveDirectories extends \Google\Service\Resource
{
  /**
   * CreateActiveDirectory Creates the active directory specified in the request.
   * (activeDirectories.create)
   *
   * @param string $parent Required. Value for parent.
   * @param ActiveDirectory $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string activeDirectoryId Required. ID of the active directory to
   * create. Must be unique within the parent resource. Must contain only letters,
   * numbers and hyphen, with the first character a letter , the last a letter or
   * a number, and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, ActiveDirectory $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Delete the active directory specified in the request.
   * (activeDirectories.delete)
   *
   * @param string $name Required. Name of the active directory.
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
   * Describes a specified active directory. (activeDirectories.get)
   *
   * @param string $name Required. Name of the active directory.
   * @param array $optParams Optional parameters.
   * @return ActiveDirectory
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ActiveDirectory::class);
  }
  /**
   * Lists active directories.
   * (activeDirectories.listProjectsLocationsActiveDirectories)
   *
   * @param string $parent Required. Parent value for ListActiveDirectoriesRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filtering results
   * @opt_param string orderBy Hint for how to order the results
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, the server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListActiveDirectoriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsActiveDirectories($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListActiveDirectoriesResponse::class);
  }
  /**
   * Update the parameters of an active directories. (activeDirectories.patch)
   *
   * @param string $name Identifier. The resource name of the active directory.
   * Format: `projects/{project_number}/locations/{location_id}/activeDirectories/
   * {active_directory_id}`.
   * @param ActiveDirectory $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Active Directory resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, ActiveDirectory $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsActiveDirectories::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsActiveDirectories');
