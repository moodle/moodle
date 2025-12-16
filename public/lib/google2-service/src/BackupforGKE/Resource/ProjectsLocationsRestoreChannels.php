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

namespace Google\Service\BackupforGKE\Resource;

use Google\Service\BackupforGKE\GoogleLongrunningOperation;
use Google\Service\BackupforGKE\ListRestoreChannelsResponse;
use Google\Service\BackupforGKE\RestoreChannel;

/**
 * The "restoreChannels" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkebackupService = new Google\Service\BackupforGKE(...);
 *   $restoreChannels = $gkebackupService->projects_locations_restoreChannels;
 *  </code>
 */
class ProjectsLocationsRestoreChannels extends \Google\Service\Resource
{
  /**
   * Creates a new RestoreChannel in a given location. (restoreChannels.create)
   *
   * @param string $parent Required. The location within which to create the
   * RestoreChannel. Format: `projects/locations`
   * @param RestoreChannel $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string restoreChannelId Optional. The client-provided short name
   * for the RestoreChannel resource. This name must: - be between 1 and 63
   * characters long (inclusive) - consist of only lower-case ASCII letters,
   * numbers, and dashes - start with a lower-case letter - end with a lower-case
   * letter or number - be unique within the set of RestoreChannels in this
   * location If the user does not provide a name, a uuid will be used as the
   * name.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, RestoreChannel $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes an existing RestoreChannel. (restoreChannels.delete)
   *
   * @param string $name Required. Fully qualified RestoreChannel name. Format:
   * `projects/locations/restoreChannels`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. If provided, this value must match the
   * current value of the target RestoreChannel's etag field or the request is
   * rejected.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Retrieve the details of a single RestoreChannel. (restoreChannels.get)
   *
   * @param string $name Required. Fully qualified RestoreChannel name. Format:
   * `projects/locations/restoreChannels`
   * @param array $optParams Optional parameters.
   * @return RestoreChannel
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], RestoreChannel::class);
  }
  /**
   * Lists RestoreChannels in a given location.
   * (restoreChannels.listProjectsLocationsRestoreChannels)
   *
   * @param string $parent Required. The location that contains the
   * RestoreChannels to list. Format: `projects/locations`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Field match expression used to filter the
   * results.
   * @opt_param string orderBy Optional. Field by which to sort the results.
   * @opt_param int pageSize Optional. The target number of results to return in a
   * single response. If not specified, a default value will be chosen by the
   * service. Note that the response may include a partial list and a caller
   * should only rely on the response's next_page_token to determine if there are
   * more instances left to be queried.
   * @opt_param string pageToken Optional. The value of next_page_token received
   * from a previous `ListRestoreChannels` call. Provide this to retrieve the
   * subsequent page in a multi-page list of results. When paginating, all other
   * parameters provided to `ListRestoreChannels` must match the call that
   * provided the page token.
   * @return ListRestoreChannelsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRestoreChannels($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRestoreChannelsResponse::class);
  }
  /**
   * Update a RestoreChannel. (restoreChannels.patch)
   *
   * @param string $name Identifier. The fully qualified name of the
   * RestoreChannel. `projects/locations/restoreChannels`
   * @param RestoreChannel $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. This is used to specify the fields to
   * be overwritten in the RestoreChannel targeted for update. The values for each
   * of these updated fields will be taken from the `restore_channel` provided
   * with this request. Field names are relative to the root of the resource
   * (e.g., `description`, `destination_project_id`, etc.) If no `update_mask` is
   * provided, all fields in `restore_channel` will be written to the target
   * RestoreChannel resource. Note that OUTPUT_ONLY and IMMUTABLE fields in
   * `restore_channel` are ignored and are not used to update the target
   * RestoreChannel.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, RestoreChannel $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRestoreChannels::class, 'Google_Service_BackupforGKE_Resource_ProjectsLocationsRestoreChannels');
