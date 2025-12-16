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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\GoogleCloudApihubV1Addon;
use Google\Service\APIhub\GoogleCloudApihubV1ListAddonsResponse;
use Google\Service\APIhub\GoogleCloudApihubV1ManageAddonConfigRequest;
use Google\Service\APIhub\GoogleLongrunningOperation;

/**
 * The "addons" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $addons = $apihubService->projects_locations_addons;
 *  </code>
 */
class ProjectsLocationsAddons extends \Google\Service\Resource
{
  /**
   * Get an addon. (addons.get)
   *
   * @param string $name Required. The name of the addon to get. Format:
   * `projects/{project}/locations/{location}/addons/{addon}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Addon
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Addon::class);
  }
  /**
   * List addons. (addons.listProjectsLocationsAddons)
   *
   * @param string $parent Required. The parent resource where this addon will be
   * created. Format: `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * addons. The only supported filter is `plugin_instance_name`. It can be used
   * to filter addons that are enabled for a given plugin instance. The format of
   * the filter is `plugin_instance_name = "projects/{project}/locations/{location
   * }/plugins/{plugin}/instances/{instance}"`.
   * @opt_param int pageSize Optional. The maximum number of hub addons to return.
   * The service may return fewer than this value. If unspecified, at most 50 hub
   * addons will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListAddons` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters (except page_size) provided to `ListAddons`
   * must match the call that provided the page token.
   * @return GoogleCloudApihubV1ListAddonsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAddons($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListAddonsResponse::class);
  }
  /**
   * Manage addon config. This RPC is used for managing the config of the addon.
   * Calling this RPC moves the addon into an updating state until the long-
   * running operation succeeds. (addons.manageConfig)
   *
   * @param string $name Required. The name of the addon for which the config is
   * to be managed. Format:
   * `projects/{project}/locations/{location}/addons/{addon}`.
   * @param GoogleCloudApihubV1ManageAddonConfigRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function manageConfig($name, GoogleCloudApihubV1ManageAddonConfigRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('manageConfig', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAddons::class, 'Google_Service_APIhub_Resource_ProjectsLocationsAddons');
