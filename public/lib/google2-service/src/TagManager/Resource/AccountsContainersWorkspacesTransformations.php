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

use Google\Service\TagManager\ListTransformationsResponse;
use Google\Service\TagManager\RevertTransformationResponse;
use Google\Service\TagManager\Transformation;

/**
 * The "transformations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $tagmanagerService = new Google\Service\TagManager(...);
 *   $transformations = $tagmanagerService->accounts_containers_workspaces_transformations;
 *  </code>
 */
class AccountsContainersWorkspacesTransformations extends \Google\Service\Resource
{
  /**
   * Creates a GTM Transformation. (transformations.create)
   *
   * @param string $parent GTM Workspace's API relative path.
   * @param Transformation $postBody
   * @param array $optParams Optional parameters.
   * @return Transformation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Transformation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Transformation::class);
  }
  /**
   * Deletes a GTM Transformation. (transformations.delete)
   *
   * @param string $path GTM Transformation's API relative path.
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
   * Gets a GTM Transformation. (transformations.get)
   *
   * @param string $path GTM Transformation's API relative path.
   * @param array $optParams Optional parameters.
   * @return Transformation
   * @throws \Google\Service\Exception
   */
  public function get($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Transformation::class);
  }
  /**
   * Lists all GTM Transformations of a GTM container workspace.
   * (transformations.listAccountsContainersWorkspacesTransformations)
   *
   * @param string $parent GTM Workspace's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken Continuation token for fetching the next page of
   * results.
   * @return ListTransformationsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsContainersWorkspacesTransformations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTransformationsResponse::class);
  }
  /**
   * Reverts changes to a GTM Transformation in a GTM Workspace.
   * (transformations.revert)
   *
   * @param string $path GTM Transformation's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fingerprint When provided, this fingerprint must match the
   * fingerprint of the transformation in storage.
   * @return RevertTransformationResponse
   * @throws \Google\Service\Exception
   */
  public function revert($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('revert', [$params], RevertTransformationResponse::class);
  }
  /**
   * Updates a GTM Transformation. (transformations.update)
   *
   * @param string $path GTM Transformation's API relative path.
   * @param Transformation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fingerprint When provided, this fingerprint must match the
   * fingerprint of the transformation in storage.
   * @return Transformation
   * @throws \Google\Service\Exception
   */
  public function update($path, Transformation $postBody, $optParams = [])
  {
    $params = ['path' => $path, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Transformation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsContainersWorkspacesTransformations::class, 'Google_Service_TagManager_Resource_AccountsContainersWorkspacesTransformations');
