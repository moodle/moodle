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

use Google\Service\NetAppFiles\ListStoragePoolsResponse;
use Google\Service\NetAppFiles\Operation;
use Google\Service\NetAppFiles\StoragePool;
use Google\Service\NetAppFiles\SwitchActiveReplicaZoneRequest;
use Google\Service\NetAppFiles\ValidateDirectoryServiceRequest;

/**
 * The "storagePools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $storagePools = $netappService->projects_locations_storagePools;
 *  </code>
 */
class ProjectsLocationsStoragePools extends \Google\Service\Resource
{
  /**
   * Creates a new storage pool. (storagePools.create)
   *
   * @param string $parent Required. Value for parent.
   * @param StoragePool $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string storagePoolId Required. Id of the requesting storage pool.
   * Must be unique within the parent resource. Must contain only letters, numbers
   * and hyphen, with the first character a letter, the last a letter or a number,
   * and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, StoragePool $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Warning! This operation will permanently delete the storage pool.
   * (storagePools.delete)
   *
   * @param string $name Required. Name of the storage pool
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
   * Returns the description of the specified storage pool by poolId.
   * (storagePools.get)
   *
   * @param string $name Required. Name of the storage pool
   * @param array $optParams Optional parameters.
   * @return StoragePool
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], StoragePool::class);
  }
  /**
   * Returns descriptions of all storage pools owned by the caller.
   * (storagePools.listProjectsLocationsStoragePools)
   *
   * @param string $parent Required. Parent value
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. List filter.
   * @opt_param string orderBy Optional. Sort results. Supported values are
   * "name", "name desc" or "" (unsorted).
   * @opt_param int pageSize Optional. The maximum number of items to return.
   * @opt_param string pageToken Optional. The next_page_token value to use if
   * there are additional results to retrieve for this list request.
   * @return ListStoragePoolsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsStoragePools($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListStoragePoolsResponse::class);
  }
  /**
   * Updates the storage pool properties with the full spec (storagePools.patch)
   *
   * @param string $name Identifier. Name of the storage pool
   * @param StoragePool $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the StoragePool resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, StoragePool $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * This operation will switch the active/replica zone for a regional
   * storagePool. (storagePools.switchProjectsLocationsStoragePools)
   *
   * @param string $name Required. Name of the storage pool
   * @param SwitchActiveReplicaZoneRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function switchProjectsLocationsStoragePools($name, SwitchActiveReplicaZoneRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('switch', [$params], Operation::class);
  }
  /**
   * ValidateDirectoryService does a connectivity check for a directory service
   * policy attached to the storage pool. (storagePools.validateDirectoryService)
   *
   * @param string $name Required. Name of the storage pool
   * @param ValidateDirectoryServiceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function validateDirectoryService($name, ValidateDirectoryServiceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('validateDirectoryService', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsStoragePools::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsStoragePools');
