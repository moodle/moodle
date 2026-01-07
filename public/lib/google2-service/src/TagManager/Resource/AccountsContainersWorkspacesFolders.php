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

namespace Google\Service\TagManager\Resource;

use Google\Service\TagManager\Folder;
use Google\Service\TagManager\FolderEntities;
use Google\Service\TagManager\ListFoldersResponse;
use Google\Service\TagManager\RevertFolderResponse;

/**
 * The "folders" collection of methods.
 * Typical usage is:
 *  <code>
 *   $tagmanagerService = new Google\Service\TagManager(...);
 *   $folders = $tagmanagerService->accounts_containers_workspaces_folders;
 *  </code>
 */
class AccountsContainersWorkspacesFolders extends \Google\Service\Resource
{
  /**
   * Creates a GTM Folder. (folders.create)
   *
   * @param string $parent GTM Workspace's API relative path.
   * @param Folder $postBody
   * @param array $optParams Optional parameters.
   * @return Folder
   * @throws \Google\Service\Exception
   */
  public function create($parent, Folder $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Folder::class);
  }
  /**
   * Deletes a GTM Folder. (folders.delete)
   *
   * @param string $path GTM Folder's API relative path.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * List all entities in a GTM Folder. (folders.entities)
   *
   * @param string $path GTM Folder's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken Continuation token for fetching the next page of
   * results.
   * @return FolderEntities
   * @throws \Google\Service\Exception
   */
  public function entities($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('entities', [$params], FolderEntities::class);
  }
  /**
   * Gets a GTM Folder. (folders.get)
   *
   * @param string $path GTM Folder's API relative path.
   * @param array $optParams Optional parameters.
   * @return Folder
   * @throws \Google\Service\Exception
   */
  public function get($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Folder::class);
  }
  /**
   * Lists all GTM Folders of a Container.
   * (folders.listAccountsContainersWorkspacesFolders)
   *
   * @param string $parent GTM Workspace's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken Continuation token for fetching the next page of
   * results.
   * @return ListFoldersResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsContainersWorkspacesFolders($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListFoldersResponse::class);
  }
  /**
   * Moves entities to a GTM Folder. If {folder_id} in the request path equals 0,
   * this will instead move entities out of the folder they currently belong to.
   * (folders.move_entities_to_folder)
   *
   * @param string $path GTM Folder's API relative path.
   * @param Folder $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string tagId The tags to be moved to the folder.
   * @opt_param string triggerId The triggers to be moved to the folder.
   * @opt_param string variableId The variables to be moved to the folder.
   * @throws \Google\Service\Exception
   */
  public function move_entities_to_folder($path, Folder $postBody, $optParams = [])
  {
    $params = ['path' => $path, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('move_entities_to_folder', [$params]);
  }
  /**
   * Reverts changes to a GTM Folder in a GTM Workspace. (folders.revert)
   *
   * @param string $path GTM Folder's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fingerprint When provided, this fingerprint must match the
   * fingerprint of the tag in storage.
   * @return RevertFolderResponse
   * @throws \Google\Service\Exception
   */
  public function revert($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('revert', [$params], RevertFolderResponse::class);
  }
  /**
   * Updates a GTM Folder. (folders.update)
   *
   * @param string $path GTM Folder's API relative path.
   * @param Folder $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fingerprint When provided, this fingerprint must match the
   * fingerprint of the folder in storage.
   * @return Folder
   * @throws \Google\Service\Exception
   */
  public function update($path, Folder $postBody, $optParams = [])
  {
    $params = ['path' => $path, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Folder::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsContainersWorkspacesFolders::class, 'Google_Service_TagManager_Resource_AccountsContainersWorkspacesFolders');
