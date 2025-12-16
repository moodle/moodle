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

namespace Google\Service\Dataform\Resource;

use Google\Service\Dataform\DataformEmpty;
use Google\Service\Dataform\ListReleaseConfigsResponse;
use Google\Service\Dataform\ReleaseConfig;

/**
 * The "releaseConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataformService = new Google\Service\Dataform(...);
 *   $releaseConfigs = $dataformService->projects_locations_repositories_releaseConfigs;
 *  </code>
 */
class ProjectsLocationsRepositoriesReleaseConfigs extends \Google\Service\Resource
{
  /**
   * Creates a new ReleaseConfig in a given Repository. (releaseConfigs.create)
   *
   * @param string $parent Required. The repository in which to create the release
   * config. Must be in the format `projects/locations/repositories`.
   * @param ReleaseConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string releaseConfigId Required. The ID to use for the release
   * config, which will become the final component of the release config's
   * resource name.
   * @return ReleaseConfig
   * @throws \Google\Service\Exception
   */
  public function create($parent, ReleaseConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], ReleaseConfig::class);
  }
  /**
   * Deletes a single ReleaseConfig. (releaseConfigs.delete)
   *
   * @param string $name Required. The release config's name.
   * @param array $optParams Optional parameters.
   * @return DataformEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DataformEmpty::class);
  }
  /**
   * Fetches a single ReleaseConfig. (releaseConfigs.get)
   *
   * @param string $name Required. The release config's name.
   * @param array $optParams Optional parameters.
   * @return ReleaseConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ReleaseConfig::class);
  }
  /**
   * Lists ReleaseConfigs in a given Repository.
   * (releaseConfigs.listProjectsLocationsRepositoriesReleaseConfigs)
   *
   * @param string $parent Required. The repository in which to list release
   * configs. Must be in the format `projects/locations/repositories`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of release configs to
   * return. The server may return fewer items than requested. If unspecified, the
   * server will pick an appropriate default.
   * @opt_param string pageToken Optional. Page token received from a previous
   * `ListReleaseConfigs` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListReleaseConfigs`, with the
   * exception of `page_size`, must match the call that provided the page token.
   * @return ListReleaseConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesReleaseConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListReleaseConfigsResponse::class);
  }
  /**
   * Updates a single ReleaseConfig. **Note:** *This method does not fully
   * implement [AIP/134](https://google.aip.dev/134). The wildcard entry () is
   * treated as a bad request, and when the `field_mask` is omitted, the request
   * is treated as a full update on all modifiable fields.* (releaseConfigs.patch)
   *
   * @param string $name Identifier. The release config's name.
   * @param ReleaseConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Specifies the fields to be updated in
   * the release config. If left unset, all fields will be updated.
   * @return ReleaseConfig
   * @throws \Google\Service\Exception
   */
  public function patch($name, ReleaseConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], ReleaseConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesReleaseConfigs::class, 'Google_Service_Dataform_Resource_ProjectsLocationsRepositoriesReleaseConfigs');
