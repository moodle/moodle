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

namespace Google\Service\SecureSourceManager\Resource;

use Google\Service\SecureSourceManager\Hook;
use Google\Service\SecureSourceManager\ListHooksResponse;
use Google\Service\SecureSourceManager\Operation;

/**
 * The "hooks" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securesourcemanagerService = new Google\Service\SecureSourceManager(...);
 *   $hooks = $securesourcemanagerService->projects_locations_repositories_hooks;
 *  </code>
 */
class ProjectsLocationsRepositoriesHooks extends \Google\Service\Resource
{
  /**
   * Creates a new hook in a given repository. (hooks.create)
   *
   * @param string $parent Required. The repository in which to create the hook.
   * Values are of the form `projects/{project_number}/locations/{location_id}/rep
   * ositories/{repository_id}`
   * @param Hook $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string hookId Required. The ID to use for the hook, which will
   * become the final component of the hook's resource name. This value restricts
   * to lower-case letters, numbers, and hyphen, with the first character a
   * letter, the last a letter or a number, and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Hook $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a Hook. (hooks.delete)
   *
   * @param string $name Required. Name of the hook to delete. The format is `proj
   * ects/{project_number}/locations/{location_id}/repositories/{repository_id}/ho
   * oks/{hook_id}`.
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
   * Gets metadata of a hook. (hooks.get)
   *
   * @param string $name Required. Name of the hook to retrieve. The format is `pr
   * ojects/{project_number}/locations/{location_id}/repositories/{repository_id}/
   * hooks/{hook_id}`.
   * @param array $optParams Optional parameters.
   * @return Hook
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Hook::class);
  }
  /**
   * Lists hooks in a given repository.
   * (hooks.listProjectsLocationsRepositoriesHooks)
   *
   * @param string $parent Required. Parent value for ListHooksRequest.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListHooksResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesHooks($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListHooksResponse::class);
  }
  /**
   * Updates the metadata of a hook. (hooks.patch)
   *
   * @param string $name Identifier. A unique identifier for a Hook. The name
   * should be of the format: `projects/{project}/locations/{location_id}/reposito
   * ries/{repository_id}/hooks/{hook_id}`
   * @param Hook $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the hook resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. The special value
   * "*" means full replacement.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Hook $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesHooks::class, 'Google_Service_SecureSourceManager_Resource_ProjectsLocationsRepositoriesHooks');
