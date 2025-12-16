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

use Google\Service\BackupforGKE\BackupChannel;
use Google\Service\BackupforGKE\GoogleLongrunningOperation;
use Google\Service\BackupforGKE\ListBackupChannelsResponse;

/**
 * The "backupChannels" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkebackupService = new Google\Service\BackupforGKE(...);
 *   $backupChannels = $gkebackupService->projects_locations_backupChannels;
 *  </code>
 */
class ProjectsLocationsBackupChannels extends \Google\Service\Resource
{
  /**
   * Creates a new BackupChannel in a given location. (backupChannels.create)
   *
   * @param string $parent Required. The location within which to create the
   * BackupChannel. Format: `projects/locations`
   * @param BackupChannel $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backupChannelId Optional. The client-provided short name
   * for the BackupChannel resource. This name must: - be between 1 and 63
   * characters long (inclusive) - consist of only lower-case ASCII letters,
   * numbers, and dashes - start with a lower-case letter - end with a lower-case
   * letter or number - be unique within the set of BackupChannels in this
   * location If the user does not provide a name, a uuid will be used as the
   * name.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, BackupChannel $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes an existing BackupChannel. (backupChannels.delete)
   *
   * @param string $name Required. Fully qualified BackupChannel name. Format:
   * `projects/locations/backupChannels`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. If provided, this value must match the
   * current value of the target BackupChannel's etag field or the request is
   * rejected.
   * @opt_param bool force Optional. If set to true, any BackupPlanAssociations
   * below this BackupChannel will also be deleted. Otherwise, the request will
   * only succeed if the BackupChannel has no BackupPlanAssociations.
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
   * Retrieve the details of a single BackupChannel. (backupChannels.get)
   *
   * @param string $name Required. Fully qualified BackupChannel name. Format:
   * `projects/locations/backupChannels`
   * @param array $optParams Optional parameters.
   * @return BackupChannel
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackupChannel::class);
  }
  /**
   * Lists BackupChannels in a given location.
   * (backupChannels.listProjectsLocationsBackupChannels)
   *
   * @param string $parent Required. The location that contains the BackupChannels
   * to list. Format: `projects/locations`
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
   * from a previous `ListBackupChannels` call. Provide this to retrieve the
   * subsequent page in a multi-page list of results. When paginating, all other
   * parameters provided to `ListBackupChannels` must match the call that provided
   * the page token.
   * @return ListBackupChannelsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupChannels($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupChannelsResponse::class);
  }
  /**
   * Update a BackupChannel. (backupChannels.patch)
   *
   * @param string $name Identifier. The fully qualified name of the
   * BackupChannel. `projects/locations/backupChannels`
   * @param BackupChannel $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. This is used to specify the fields to
   * be overwritten in the BackupChannel targeted for update. The values for each
   * of these updated fields will be taken from the `backup_channel` provided with
   * this request. Field names are relative to the root of the resource (e.g.,
   * `description`, `labels`, etc.) If no `update_mask` is provided, all fields in
   * `backup_channel` will be written to the target BackupChannel resource. Note
   * that OUTPUT_ONLY and IMMUTABLE fields in `backup_channel` are ignored and are
   * not used to update the target BackupChannel.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, BackupChannel $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupChannels::class, 'Google_Service_BackupforGKE_Resource_ProjectsLocationsBackupChannels');
