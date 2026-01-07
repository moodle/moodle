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

namespace Google\Service\Drive\Resource;

use Google\Service\Drive\Drive;
use Google\Service\Drive\DriveList;

/**
 * The "drives" collection of methods.
 * Typical usage is:
 *  <code>
 *   $driveService = new Google\Service\Drive(...);
 *   $drives = $driveService->drives;
 *  </code>
 */
class Drives extends \Google\Service\Resource
{
  /**
   * Creates a shared drive. For more information, see [Manage shared
   * drives](https://developers.google.com/workspace/drive/api/guides/manage-
   * shareddrives). (drives.create)
   *
   * @param string $requestId Required. An ID, such as a random UUID, which
   * uniquely identifies this user's request for idempotent creation of a shared
   * drive. A repeated request by the same user and with the same request ID will
   * avoid creating duplicates by attempting to create the same shared drive. If
   * the shared drive already exists a 409 error will be returned.
   * @param Drive $postBody
   * @param array $optParams Optional parameters.
   * @return Drive
   * @throws \Google\Service\Exception
   */
  public function create($requestId, Drive $postBody, $optParams = [])
  {
    $params = ['requestId' => $requestId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Drive::class);
  }
  /**
   * Permanently deletes a shared drive for which the user is an `organizer`. The
   * shared drive cannot contain any untrashed items. For more information, see
   * [Manage shared
   * drives](https://developers.google.com/workspace/drive/api/guides/manage-
   * shareddrives). (drives.delete)
   *
   * @param string $driveId The ID of the shared drive.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowItemDeletion Whether any items inside the shared drive
   * should also be deleted. This option is only supported when
   * `useDomainAdminAccess` is also set to `true`.
   * @opt_param bool useDomainAdminAccess Issue the request as a domain
   * administrator; if set to true, then the requester will be granted access if
   * they are an administrator of the domain to which the shared drive belongs.
   * @throws \Google\Service\Exception
   */
  public function delete($driveId, $optParams = [])
  {
    $params = ['driveId' => $driveId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Gets a shared drive's metadata by ID. For more information, see [Manage
   * shared
   * drives](https://developers.google.com/workspace/drive/api/guides/manage-
   * shareddrives). (drives.get)
   *
   * @param string $driveId The ID of the shared drive.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool useDomainAdminAccess Issue the request as a domain
   * administrator; if set to true, then the requester will be granted access if
   * they are an administrator of the domain to which the shared drive belongs.
   * @return Drive
   * @throws \Google\Service\Exception
   */
  public function get($driveId, $optParams = [])
  {
    $params = ['driveId' => $driveId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Drive::class);
  }
  /**
   * Hides a shared drive from the default view. For more information, see [Manage
   * shared
   * drives](https://developers.google.com/workspace/drive/api/guides/manage-
   * shareddrives). (drives.hide)
   *
   * @param string $driveId The ID of the shared drive.
   * @param array $optParams Optional parameters.
   * @return Drive
   * @throws \Google\Service\Exception
   */
  public function hide($driveId, $optParams = [])
  {
    $params = ['driveId' => $driveId];
    $params = array_merge($params, $optParams);
    return $this->call('hide', [$params], Drive::class);
  }
  /**
   * Lists the user's shared drives. This method accepts the `q` parameter, which
   * is a search query combining one or more search terms. For more information,
   * see the [Search for shared drives](/workspace/drive/api/guides/search-
   * shareddrives) guide. (drives.listDrives)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of shared drives to return per page.
   * @opt_param string pageToken Page token for shared drives.
   * @opt_param string q Query string for searching shared drives.
   * @opt_param bool useDomainAdminAccess Issue the request as a domain
   * administrator; if set to true, then all shared drives of the domain in which
   * the requester is an administrator are returned.
   * @return DriveList
   * @throws \Google\Service\Exception
   */
  public function listDrives($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], DriveList::class);
  }
  /**
   * Restores a shared drive to the default view. For more information, see
   * [Manage shared
   * drives](https://developers.google.com/workspace/drive/api/guides/manage-
   * shareddrives). (drives.unhide)
   *
   * @param string $driveId The ID of the shared drive.
   * @param array $optParams Optional parameters.
   * @return Drive
   * @throws \Google\Service\Exception
   */
  public function unhide($driveId, $optParams = [])
  {
    $params = ['driveId' => $driveId];
    $params = array_merge($params, $optParams);
    return $this->call('unhide', [$params], Drive::class);
  }
  /**
   * Updates the metadata for a shared drive. For more information, see [Manage
   * shared
   * drives](https://developers.google.com/workspace/drive/api/guides/manage-
   * shareddrives). (drives.update)
   *
   * @param string $driveId The ID of the shared drive.
   * @param Drive $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool useDomainAdminAccess Issue the request as a domain
   * administrator; if set to true, then the requester will be granted access if
   * they are an administrator of the domain to which the shared drive belongs.
   * @return Drive
   * @throws \Google\Service\Exception
   */
  public function update($driveId, Drive $postBody, $optParams = [])
  {
    $params = ['driveId' => $driveId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Drive::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Drives::class, 'Google_Service_Drive_Resource_Drives');
