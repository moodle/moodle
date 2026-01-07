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

namespace Google\Service\CloudKMS\Resource;

use Google\Service\CloudKMS\KeyHandle;
use Google\Service\CloudKMS\ListKeyHandlesResponse;
use Google\Service\CloudKMS\Operation;

/**
 * The "keyHandles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudkmsService = new Google\Service\CloudKMS(...);
 *   $keyHandles = $cloudkmsService->projects_locations_keyHandles;
 *  </code>
 */
class ProjectsLocationsKeyHandles extends \Google\Service\Resource
{
  /**
   * Creates a new KeyHandle, triggering the provisioning of a new CryptoKey for
   * CMEK use with the given resource type in the configured key project and the
   * same location. GetOperation should be used to resolve the resulting long-
   * running operation and get the resulting KeyHandle and CryptoKey.
   * (keyHandles.create)
   *
   * @param string $parent Required. Name of the resource project and location to
   * create the KeyHandle in, e.g. `projects/{PROJECT_ID}/locations/{LOCATION}`.
   * @param KeyHandle $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string keyHandleId Optional. Id of the KeyHandle. Must be unique
   * to the resource project and location. If not provided by the caller, a new
   * UUID is used.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, KeyHandle $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Returns the KeyHandle. (keyHandles.get)
   *
   * @param string $name Required. Name of the KeyHandle resource, e.g.
   * `projects/{PROJECT_ID}/locations/{LOCATION}/keyHandles/{KEY_HANDLE_ID}`.
   * @param array $optParams Optional parameters.
   * @return KeyHandle
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], KeyHandle::class);
  }
  /**
   * Lists KeyHandles. (keyHandles.listProjectsLocationsKeyHandles)
   *
   * @param string $parent Required. Name of the resource project and location
   * from which to list KeyHandles, e.g.
   * `projects/{PROJECT_ID}/locations/{LOCATION}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter to apply when listing KeyHandles,
   * e.g. `resource_type_selector="{SERVICE}.googleapis.com/{TYPE}"`.
   * @opt_param int pageSize Optional. Optional limit on the number of KeyHandles
   * to include in the response. The service may return fewer than this value.
   * Further KeyHandles can subsequently be obtained by including the
   * ListKeyHandlesResponse.next_page_token in a subsequent request. If
   * unspecified, at most 100 KeyHandles will be returned.
   * @opt_param string pageToken Optional. Optional pagination token, returned
   * earlier via ListKeyHandlesResponse.next_page_token.
   * @return ListKeyHandlesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsKeyHandles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListKeyHandlesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsKeyHandles::class, 'Google_Service_CloudKMS_Resource_ProjectsLocationsKeyHandles');
