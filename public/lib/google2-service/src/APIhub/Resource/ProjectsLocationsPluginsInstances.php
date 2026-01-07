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

use Google\Service\APIhub\GoogleCloudApihubV1DisablePluginInstanceActionRequest;
use Google\Service\APIhub\GoogleCloudApihubV1EnablePluginInstanceActionRequest;
use Google\Service\APIhub\GoogleCloudApihubV1ExecutePluginInstanceActionRequest;
use Google\Service\APIhub\GoogleCloudApihubV1ListPluginInstancesResponse;
use Google\Service\APIhub\GoogleCloudApihubV1ManagePluginInstanceSourceDataRequest;
use Google\Service\APIhub\GoogleCloudApihubV1ManagePluginInstanceSourceDataResponse;
use Google\Service\APIhub\GoogleCloudApihubV1PluginInstance;
use Google\Service\APIhub\GoogleLongrunningOperation;

/**
 * The "instances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $instances = $apihubService->projects_locations_plugins_instances;
 *  </code>
 */
class ProjectsLocationsPluginsInstances extends \Google\Service\Resource
{
  /**
   * Creates a Plugin instance in the API hub. (instances.create)
   *
   * @param string $parent Required. The parent of the plugin instance resource.
   * Format: `projects/{project}/locations/{location}/plugins/{plugin}`
   * @param GoogleCloudApihubV1PluginInstance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pluginInstanceId Optional. The ID to use for the plugin
   * instance, which will become the final component of the plugin instance's
   * resource name. This field is optional. * If provided, the same will be used.
   * The service will throw an error if the specified id is already used by
   * another plugin instance in the plugin resource. * If not provided, a system
   * generated id will be used. This value should be 4-63 characters, and valid
   * characters are /a-z[0-9]-_/.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1PluginInstance $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a plugin instance in the API hub. (instances.delete)
   *
   * @param string $name Required. The name of the plugin instance to delete.
   * Format: `projects/{project}/locations/{location}/plugins/{plugin}/instances/{
   * instance}`.
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Disables a plugin instance in the API hub. (instances.disableAction)
   *
   * @param string $name Required. The name of the plugin instance to disable.
   * Format: `projects/{project}/locations/{location}/plugins/{plugin}/instances/{
   * instance}`
   * @param GoogleCloudApihubV1DisablePluginInstanceActionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function disableAction($name, GoogleCloudApihubV1DisablePluginInstanceActionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('disableAction', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Enables a plugin instance in the API hub. (instances.enableAction)
   *
   * @param string $name Required. The name of the plugin instance to enable.
   * Format: `projects/{project}/locations/{location}/plugins/{plugin}/instances/{
   * instance}`
   * @param GoogleCloudApihubV1EnablePluginInstanceActionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function enableAction($name, GoogleCloudApihubV1EnablePluginInstanceActionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enableAction', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Executes a plugin instance in the API hub. (instances.executeAction)
   *
   * @param string $name Required. The name of the plugin instance to execute.
   * Format: `projects/{project}/locations/{location}/plugins/{plugin}/instances/{
   * instance}`
   * @param GoogleCloudApihubV1ExecutePluginInstanceActionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function executeAction($name, GoogleCloudApihubV1ExecutePluginInstanceActionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('executeAction', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Get an API Hub plugin instance. (instances.get)
   *
   * @param string $name Required. The name of the plugin instance to retrieve.
   * Format: `projects/{project}/locations/{location}/plugins/{plugin}/instances/{
   * instance}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1PluginInstance
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1PluginInstance::class);
  }
  /**
   * List all the plugins in a given project and location. `-` can be used as
   * wildcard value for {plugin_id}
   * (instances.listProjectsLocationsPluginsInstances)
   *
   * @param string $parent Required. The parent resource where this plugin will be
   * created. Format: `projects/{project}/locations/{location}/plugins/{plugin}`.
   * To list plugin instances for multiple plugins, use the - character instead of
   * the plugin ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * plugin instances. A filter expression consists of a field name, a comparison
   * operator, and a value for filtering. The value must be a string. The
   * comparison operator must be one of: `<`, `>` or `=`. Filters are not case
   * sensitive. The following fields in the `PluginInstances` are eligible for
   * filtering: * `state` - The state of the Plugin Instance. Allowed comparison
   * operators: `=`. * `source_project_id` - The source project id of the Plugin
   * Instance. Allowed comparison operators: `=`. A filter function is also
   * supported in the filter string. The filter function is `id(name)`. The
   * `id(name)` function returns the id of the resource name. For example,
   * `id(name) = \"plugin-instance-1\"` is equivalent to `name = \"projects/test-
   * project-id/locations/test-location-id/plugins/plugin-1/instances/plugin-
   * instance-1\"` provided the parent is `projects/test-project-
   * id/locations/test-location-id/plugins/plugin-1`. Expressions are combined
   * with either `AND` logic operator or `OR` logical operator but not both of
   * them together i.e. only one of the `AND` or `OR` operator can be used
   * throughout the filter string and both the operators cannot be used together.
   * No other logical operators are supported. At most three filter fields are
   * allowed in the filter string and if provided more than that then
   * `INVALID_ARGUMENT` error is returned by the API. Here are a few examples: *
   * `state = ENABLED` - The plugin instance is in enabled state.
   * @opt_param int pageSize Optional. The maximum number of hub plugins to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 hub plugins will be returned. The maximum value is 1000; values above 1000
   * will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListPluginInstances` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListPluginInstances` must
   * match the call that provided the page token.
   * @return GoogleCloudApihubV1ListPluginInstancesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPluginsInstances($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListPluginInstancesResponse::class);
  }
  /**
   * Manages data for a given plugin instance. (instances.manageSourceData)
   *
   * @param string $name Required. The name of the plugin instance for which data
   * needs to be managed. Format: `projects/{project}/locations/{location}/plugins
   * /{plugin}/instances/{instance}`
   * @param GoogleCloudApihubV1ManagePluginInstanceSourceDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1ManagePluginInstanceSourceDataResponse
   * @throws \Google\Service\Exception
   */
  public function manageSourceData($name, GoogleCloudApihubV1ManagePluginInstanceSourceDataRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('manageSourceData', [$params], GoogleCloudApihubV1ManagePluginInstanceSourceDataResponse::class);
  }
  /**
   * Updates a plugin instance in the API hub. The following fields in the
   * plugin_instance can be updated currently: * display_name *
   * schedule_cron_expression The update_mask should be used to specify the fields
   * being updated. To update the auth_config and additional_config of the plugin
   * instance, use the ApplyPluginInstanceConfig method. (instances.patch)
   *
   * @param string $name Identifier. The unique name of the plugin instance
   * resource. Format: `projects/{project}/locations/{location}/plugins/{plugin}/i
   * nstances/{instance}`
   * @param GoogleCloudApihubV1PluginInstance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return GoogleCloudApihubV1PluginInstance
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1PluginInstance $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1PluginInstance::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPluginsInstances::class, 'Google_Service_APIhub_Resource_ProjectsLocationsPluginsInstances');
