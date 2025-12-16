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

namespace Google\Service\Storage\Resource;

use Google\Service\Storage\ManagedFolder;
use Google\Service\Storage\ManagedFolders as ManagedFoldersModel;
use Google\Service\Storage\Policy;
use Google\Service\Storage\TestIamPermissionsResponse;

/**
 * The "managedFolders" collection of methods.
 * Typical usage is:
 *  <code>
 *   $storageService = new Google\Service\Storage(...);
 *   $managedFolders = $storageService->managedFolders;
 *  </code>
 */
class ManagedFolders extends \Google\Service\Resource
{
  /**
   * Permanently deletes a managed folder. (managedFolders.delete)
   *
   * @param string $bucket Name of the bucket containing the managed folder.
   * @param string $managedFolder The managed folder name/path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowNonEmpty Allows the deletion of a managed folder even if
   * it is not empty. A managed folder is empty if there are no objects or managed
   * folders that it applies to. Callers must have
   * storage.managedFolders.setIamPolicy permission.
   * @opt_param string ifMetagenerationMatch If set, only deletes the managed
   * folder if its metageneration matches this value.
   * @opt_param string ifMetagenerationNotMatch If set, only deletes the managed
   * folder if its metageneration does not match this value.
   * @throws \Google\Service\Exception
   */
  public function delete($bucket, $managedFolder, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'managedFolder' => $managedFolder];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Returns metadata of the specified managed folder. (managedFolders.get)
   *
   * @param string $bucket Name of the bucket containing the managed folder.
   * @param string $managedFolder The managed folder name/path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string ifMetagenerationMatch Makes the return of the managed
   * folder metadata conditional on whether the managed folder's current
   * metageneration matches the given value.
   * @opt_param string ifMetagenerationNotMatch Makes the return of the managed
   * folder metadata conditional on whether the managed folder's current
   * metageneration does not match the given value.
   * @return ManagedFolder
   * @throws \Google\Service\Exception
   */
  public function get($bucket, $managedFolder, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'managedFolder' => $managedFolder];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ManagedFolder::class);
  }
  /**
   * Returns an IAM policy for the specified managed folder.
   * (managedFolders.getIamPolicy)
   *
   * @param string $bucket Name of the bucket containing the managed folder.
   * @param string $managedFolder The managed folder name/path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int optionsRequestedPolicyVersion The IAM policy format version to
   * be returned. If the optionsRequestedPolicyVersion is for an older version
   * that doesn't support part of the requested IAM policy, the request fails.
   * @opt_param string userProject The project to be billed for this request.
   * Required for Requester Pays buckets.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($bucket, $managedFolder, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'managedFolder' => $managedFolder];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Creates a new managed folder. (managedFolders.insert)
   *
   * @param string $bucket Name of the bucket containing the managed folder.
   * @param ManagedFolder $postBody
   * @param array $optParams Optional parameters.
   * @return ManagedFolder
   * @throws \Google\Service\Exception
   */
  public function insert($bucket, ManagedFolder $postBody, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], ManagedFolder::class);
  }
  /**
   * Lists managed folders in the given bucket.
   * (managedFolders.listManagedFolders)
   *
   * @param string $bucket Name of the bucket containing the managed folder.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of items to return in a single page of
   * responses.
   * @opt_param string pageToken A previously-returned page token representing
   * part of the larger set of results to view.
   * @opt_param string prefix The managed folder name/path prefix to filter the
   * output list of results.
   * @return ManagedFoldersModel
   * @throws \Google\Service\Exception
   */
  public function listManagedFolders($bucket, $optParams = [])
  {
    $params = ['bucket' => $bucket];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ManagedFoldersModel::class);
  }
  /**
   * Updates an IAM policy for the specified managed folder.
   * (managedFolders.setIamPolicy)
   *
   * @param string $bucket Name of the bucket containing the managed folder.
   * @param string $managedFolder The managed folder name/path.
   * @param Policy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string userProject The project to be billed for this request.
   * Required for Requester Pays buckets.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($bucket, $managedFolder, Policy $postBody, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'managedFolder' => $managedFolder, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Tests a set of permissions on the given managed folder to see which, if any,
   * are held by the caller. (managedFolders.testIamPermissions)
   *
   * @param string $bucket Name of the bucket containing the managed folder.
   * @param string $managedFolder The managed folder name/path.
   * @param string|array $permissions Permissions to test.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string userProject The project to be billed for this request.
   * Required for Requester Pays buckets.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($bucket, $managedFolder, $permissions, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'managedFolder' => $managedFolder, 'permissions' => $permissions];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedFolders::class, 'Google_Service_Storage_Resource_ManagedFolders');
