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

use Google\Service\NetworkServices\ListWasmPluginsResponse;
use Google\Service\NetworkServices\Operation;
use Google\Service\NetworkServices\WasmPlugin;

/**
 * The "wasmPlugins" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkservicesService = new Google\Service\NetworkServices(...);
 *   $wasmPlugins = $networkservicesService->projects_locations_wasmPlugins;
 *  </code>
 */
class ProjectsLocationsWasmPlugins extends \Google\Service\Resource
{
  /**
   * Creates a new `WasmPlugin` resource in a given project and location.
   * (wasmPlugins.create)
   *
   * @param string $parent Required. The parent resource of the `WasmPlugin`
   * resource. Must be in the format `projects/{project}/locations/global`.
   * @param WasmPlugin $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string wasmPluginId Required. User-provided ID of the `WasmPlugin`
   * resource to be created.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, WasmPlugin $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes the specified `WasmPlugin` resource. (wasmPlugins.delete)
   *
   * @param string $name Required. A name of the `WasmPlugin` resource to delete.
   * Must be in the format
   * `projects/{project}/locations/global/wasmPlugins/{wasm_plugin}`.
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
   * Gets details of the specified `WasmPlugin` resource. (wasmPlugins.get)
   *
   * @param string $name Required. A name of the `WasmPlugin` resource to get.
   * Must be in the format
   * `projects/{project}/locations/global/wasmPlugins/{wasm_plugin}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Determines how much data must be returned in the
   * response. See [AIP-157](https://google.aip.dev/157).
   * @return WasmPlugin
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], WasmPlugin::class);
  }
  /**
   * Lists `WasmPlugin` resources in a given project and location.
   * (wasmPlugins.listProjectsLocationsWasmPlugins)
   *
   * @param string $parent Required. The project and location from which the
   * `WasmPlugin` resources are listed, specified in the following format:
   * `projects/{project}/locations/global`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of `WasmPlugin` resources to return
   * per call. If not specified, at most 50 `WasmPlugin` resources are returned.
   * The maximum value is 1000; values above 1000 are coerced to 1000.
   * @opt_param string pageToken The value returned by the last
   * `ListWasmPluginsResponse` call. Indicates that this is a continuation of a
   * prior `ListWasmPlugins` call, and that the next page of data is to be
   * returned.
   * @return ListWasmPluginsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsWasmPlugins($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWasmPluginsResponse::class);
  }
  /**
   * Updates the parameters of the specified `WasmPlugin` resource.
   * (wasmPlugins.patch)
   *
   * @param string $name Identifier. Name of the `WasmPlugin` resource in the
   * following format:
   * `projects/{project}/locations/{location}/wasmPlugins/{wasm_plugin}`.
   * @param WasmPlugin $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Used to specify the fields to be
   * overwritten in the `WasmPlugin` resource by the update. The fields specified
   * in the `update_mask` field are relative to the resource, not the full
   * request. An omitted `update_mask` field is treated as an implied
   * `update_mask` field equivalent to all fields that are populated (that have a
   * non-empty value). The `update_mask` field supports a special value `*`, which
   * means that each field in the given `WasmPlugin` resource (including the empty
   * ones) replaces the current value.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, WasmPlugin $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsWasmPlugins::class, 'Google_Service_NetworkServices_Resource_ProjectsLocationsWasmPlugins');
