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

namespace Google\Service\NetworkServices\Resource;

use Google\Service\NetworkServices\ListWasmPluginVersionsResponse;
use Google\Service\NetworkServices\Operation;
use Google\Service\NetworkServices\WasmPluginVersion;

/**
 * The "versions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkservicesService = new Google\Service\NetworkServices(...);
 *   $versions = $networkservicesService->projects_locations_wasmPlugins_versions;
 *  </code>
 */
class ProjectsLocationsWasmPluginsVersions extends \Google\Service\Resource
{
  /**
   * Creates a new `WasmPluginVersion` resource in a given project and location.
   * (versions.create)
   *
   * @param string $parent Required. The parent resource of the
   * `WasmPluginVersion` resource. Must be in the format
   * `projects/{project}/locations/global/wasmPlugins/{wasm_plugin}`.
   * @param WasmPluginVersion $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string wasmPluginVersionId Required. User-provided ID of the
   * `WasmPluginVersion` resource to be created.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, WasmPluginVersion $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes the specified `WasmPluginVersion` resource. (versions.delete)
   *
   * @param string $name Required. A name of the `WasmPluginVersion` resource to
   * delete. Must be in the format `projects/{project}/locations/global/wasmPlugin
   * s/{wasm_plugin}/versions/{wasm_plugin_version}`.
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
   * Gets details of the specified `WasmPluginVersion` resource. (versions.get)
   *
   * @param string $name Required. A name of the `WasmPluginVersion` resource to
   * get. Must be in the format `projects/{project}/locations/global/wasmPlugins/{
   * wasm_plugin}/versions/{wasm_plugin_version}`.
   * @param array $optParams Optional parameters.
   * @return WasmPluginVersion
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], WasmPluginVersion::class);
  }
  /**
   * Lists `WasmPluginVersion` resources in a given project and location.
   * (versions.listProjectsLocationsWasmPluginsVersions)
   *
   * @param string $parent Required. The `WasmPlugin` resource whose
   * `WasmPluginVersion`s are listed, specified in the following format:
   * `projects/{project}/locations/global/wasmPlugins/{wasm_plugin}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of `WasmPluginVersion` resources to
   * return per call. If not specified, at most 50 `WasmPluginVersion` resources
   * are returned. The maximum value is 1000; values above 1000 are coerced to
   * 1000.
   * @opt_param string pageToken The value returned by the last
   * `ListWasmPluginVersionsResponse` call. Indicates that this is a continuation
   * of a prior `ListWasmPluginVersions` call, and that the next page of data is
   * to be returned.
   * @return ListWasmPluginVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsWasmPluginsVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWasmPluginVersionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsWasmPluginsVersions::class, 'Google_Service_NetworkServices_Resource_ProjectsLocationsWasmPluginsVersions');
