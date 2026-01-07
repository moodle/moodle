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

use Google\Service\TagManager\CustomTemplate;
use Google\Service\TagManager\ListTemplatesResponse;
use Google\Service\TagManager\RevertTemplateResponse;

/**
 * The "templates" collection of methods.
 * Typical usage is:
 *  <code>
 *   $tagmanagerService = new Google\Service\TagManager(...);
 *   $templates = $tagmanagerService->accounts_containers_workspaces_templates;
 *  </code>
 */
class AccountsContainersWorkspacesTemplates extends \Google\Service\Resource
{
  /**
   * Creates a GTM Custom Template. (templates.create)
   *
   * @param string $parent GTM Workspace's API relative path.
   * @param CustomTemplate $postBody
   * @param array $optParams Optional parameters.
   * @return CustomTemplate
   * @throws \Google\Service\Exception
   */
  public function create($parent, CustomTemplate $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], CustomTemplate::class);
  }
  /**
   * Deletes a GTM Template. (templates.delete)
   *
   * @param string $path GTM Custom Template's API relative path.
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
   * Gets a GTM Template. (templates.get)
   *
   * @param string $path GTM Custom Template's API relative path.
   * @param array $optParams Optional parameters.
   * @return CustomTemplate
   * @throws \Google\Service\Exception
   */
  public function get($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CustomTemplate::class);
  }
  /**
   * Imports a GTM Custom Template from Gallery. (templates.import_from_gallery)
   *
   * @param string $parent GTM Workspace's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool acknowledgePermissions Must be set to true to allow Gallery
   * template to be imported into the workspace. If this bit is false, the import
   * operation will fail.
   * @opt_param string galleryOwner Owner of the Gallery template to import
   * @opt_param string galleryRepository Repository of the Gallery template to
   * import
   * @opt_param string gallerySha SHA version of the Gallery template to import.
   * Defaulted to the latest SHA version if not provided.
   * @return CustomTemplate
   * @throws \Google\Service\Exception
   */
  public function import_from_gallery($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('import_from_gallery', [$params], CustomTemplate::class);
  }
  /**
   * Lists all GTM Templates of a GTM container workspace.
   * (templates.listAccountsContainersWorkspacesTemplates)
   *
   * @param string $parent GTM Workspace's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken Continuation token for fetching the next page of
   * results.
   * @return ListTemplatesResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsContainersWorkspacesTemplates($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTemplatesResponse::class);
  }
  /**
   * Reverts changes to a GTM Template in a GTM Workspace. (templates.revert)
   *
   * @param string $path GTM Custom Template's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fingerprint When provided, this fingerprint must match the
   * fingerprint of the template in storage.
   * @return RevertTemplateResponse
   * @throws \Google\Service\Exception
   */
  public function revert($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('revert', [$params], RevertTemplateResponse::class);
  }
  /**
   * Updates a GTM Template. (templates.update)
   *
   * @param string $path GTM Custom Template's API relative path.
   * @param CustomTemplate $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fingerprint When provided, this fingerprint must match the
   * fingerprint of the templates in storage.
   * @return CustomTemplate
   * @throws \Google\Service\Exception
   */
  public function update($path, CustomTemplate $postBody, $optParams = [])
  {
    $params = ['path' => $path, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], CustomTemplate::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsContainersWorkspacesTemplates::class, 'Google_Service_TagManager_Resource_AccountsContainersWorkspacesTemplates');
