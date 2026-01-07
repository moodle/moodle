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

namespace Google\Service\CloudResourceManager\Resource;

use Google\Service\CloudResourceManager\Capability;
use Google\Service\CloudResourceManager\Operation;

/**
 * The "capabilities" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudresourcemanagerService = new Google\Service\CloudResourceManager(...);
 *   $capabilities = $cloudresourcemanagerService->folders_capabilities;
 *  </code>
 */
class FoldersCapabilities extends \Google\Service\Resource
{
  /**
   * Retrieves the Capability identified by the supplied resource name.
   * (capabilities.get)
   *
   * @param string $name Required. The name of the capability to get. For example,
   * `folders/123/capabilities/app-management`
   * @param array $optParams Optional parameters.
   * @return Capability
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Capability::class);
  }
  /**
   * Updates the Capability. (capabilities.patch)
   *
   * @param string $name Immutable. Identifier. The resource name of the
   * capability. Must be in the following form: *
   * `folders/{folder_id}/capabilities/{capability_name}` For example,
   * `folders/123/capabilities/app-management` Following are the allowed
   * {capability_name} values: * `app-management`
   * @param Capability $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update. Only
   * [Capability.value] can be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Capability $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FoldersCapabilities::class, 'Google_Service_CloudResourceManager_Resource_FoldersCapabilities');
