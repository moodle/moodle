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

use Google\Service\APIhub\GoogleCloudApihubV1DisablePluginRequest;
use Google\Service\APIhub\GoogleCloudApihubV1EnablePluginRequest;
use Google\Service\APIhub\GoogleCloudApihubV1ListPluginsResponse;
use Google\Service\APIhub\GoogleCloudApihubV1Plugin;
use Google\Service\APIhub\GoogleCloudApihubV1StyleGuide;
use Google\Service\APIhub\GoogleLongrunningOperation;

/**
 * The "plugins" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $plugins = $apihubService->projects_locations_plugins;
 *  </code>
 */
class ProjectsLocationsPlugins extends \Google\Service\Resource
{
  /**
   * Create an API Hub plugin resource in the API hub. Once a plugin is created,
   * it can be used to create plugin instances. (plugins.create)
   *
   * @param string $parent Required. The parent resource where this plugin will be
   * created. Format: `projects/{project}/locations/{location}`.
   * @param GoogleCloudApihubV1Plugin $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pluginId Optional. The ID to use for the Plugin resource,
   * which will become the final component of the Plugin's resource name. This
   * field is optional. * If provided, the same will be used. The service will
   * throw an error if the specified id is already used by another Plugin resource
   * in the API hub instance. * If not provided, a system generated id will be
   * used. This value should be 4-63 characters, overall resource name which will
   * be of format `projects/{project}/locations/{location}/plugins/{plugin}`, its
   * length is limited to 1000 characters and valid characters are /a-z[0-9]-_/.
   * @return GoogleCloudApihubV1Plugin
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1Plugin $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1Plugin::class);
  }
  /**
   * Delete a Plugin in API hub. Note, only user owned plugins can be deleted via
   * this method. (plugins.delete)
   *
   * @param string $name Required. The name of the Plugin resource to delete.
   * Format: `projects/{project}/locations/{location}/plugins/{plugin}`
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
   * Disables a plugin. The `state` of the plugin after disabling is `DISABLED`
   * (plugins.disable)
   *
   * @param string $name Required. The name of the plugin to disable. Format:
   * `projects/{project}/locations/{location}/plugins/{plugin}`.
   * @param GoogleCloudApihubV1DisablePluginRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Plugin
   * @throws \Google\Service\Exception
   */
  public function disable($name, GoogleCloudApihubV1DisablePluginRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('disable', [$params], GoogleCloudApihubV1Plugin::class);
  }
  /**
   * Enables a plugin. The `state` of the plugin after enabling is `ENABLED`
   * (plugins.enable)
   *
   * @param string $name Required. The name of the plugin to enable. Format:
   * `projects/{project}/locations/{location}/plugins/{plugin}`.
   * @param GoogleCloudApihubV1EnablePluginRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Plugin
   * @throws \Google\Service\Exception
   */
  public function enable($name, GoogleCloudApihubV1EnablePluginRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enable', [$params], GoogleCloudApihubV1Plugin::class);
  }
  /**
   * Get an API Hub plugin. (plugins.get)
   *
   * @param string $name Required. The name of the plugin to retrieve. Format:
   * `projects/{project}/locations/{location}/plugins/{plugin}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Plugin
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Plugin::class);
  }
  /**
   * Get the style guide being used for linting. (plugins.getStyleGuide)
   *
   * @param string $name Required. The name of the spec to retrieve. Format:
   * `projects/{project}/locations/{location}/plugins/{plugin}/styleGuide`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1StyleGuide
   * @throws \Google\Service\Exception
   */
  public function getStyleGuide($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getStyleGuide', [$params], GoogleCloudApihubV1StyleGuide::class);
  }
  /**
   * List all the plugins in a given project and location.
   * (plugins.listProjectsLocationsPlugins)
   *
   * @param string $parent Required. The parent resource where this plugin will be
   * created. Format: `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * plugins. A filter expression consists of a field name, a comparison operator,
   * and a value for filtering. The value must be a string. The comparison
   * operator must be one of: `<`, `>` or `=`. Filters are not case sensitive. The
   * following fields in the `Plugins` are eligible for filtering: *
   * `plugin_category` - The category of the Plugin. Allowed comparison operators:
   * `=`. Expressions are combined with either `AND` logic operator or `OR`
   * logical operator but not both of them together i.e. only one of the `AND` or
   * `OR` operator can be used throughout the filter string and both the operators
   * cannot be used together. No other logical operators are supported. At most
   * three filter fields are allowed in the filter string and if provided more
   * than that then `INVALID_ARGUMENT` error is returned by the API. Here are a
   * few examples: * `plugin_category = ON_RAMP` - The plugin is of category on
   * ramp.
   * @opt_param int pageSize Optional. The maximum number of hub plugins to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 hub plugins will be returned. The maximum value is 1000; values above 1000
   * will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListPlugins` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters (except page_size) provided to `ListPlugins`
   * must match the call that provided the page token.
   * @return GoogleCloudApihubV1ListPluginsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPlugins($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListPluginsResponse::class);
  }
  /**
   * Update the styleGuide to be used for liniting in by API hub.
   * (plugins.updateStyleGuide)
   *
   * @param string $name Identifier. The name of the style guide. Format:
   * `projects/{project}/locations/{location}/plugins/{plugin}/styleGuide`
   * @param GoogleCloudApihubV1StyleGuide $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return GoogleCloudApihubV1StyleGuide
   * @throws \Google\Service\Exception
   */
  public function updateStyleGuide($name, GoogleCloudApihubV1StyleGuide $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateStyleGuide', [$params], GoogleCloudApihubV1StyleGuide::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPlugins::class, 'Google_Service_APIhub_Resource_ProjectsLocationsPlugins');
