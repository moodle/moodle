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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1WidgetConfig;

/**
 * The "widgetConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $widgetConfigs = $discoveryengineService->projects_locations_collections_engines_widgetConfigs;
 *  </code>
 */
class ProjectsLocationsCollectionsEnginesWidgetConfigs extends \Google\Service\Resource
{
  /**
   * Gets a WidgetConfig. (widgetConfigs.get)
   *
   * @param string $name Required. Full WidgetConfig resource name. Format: `proje
   * cts/{project}/locations/{location}/collections/{collection_id}/dataStores/{da
   * ta_store_id}/widgetConfigs/{widget_config_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool acceptCache Optional. Whether it's acceptable to load the
   * widget config from cache. If set to true, recent changes on widget configs
   * may take a few minutes to reflect on the end user's view. It's recommended to
   * set to true for maturely developed widgets, as it improves widget
   * performance. Set to false to see changes reflected in prod right away, if
   * your widget is under development.
   * @opt_param bool getWidgetConfigRequestOption.turnOffCollectionComponents
   * Optional. Whether to turn off collection_components in WidgetConfig to reduce
   * latency and data transmission.
   * @return GoogleCloudDiscoveryengineV1WidgetConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1WidgetConfig::class);
  }
  /**
   * Update a WidgetConfig. (widgetConfigs.patch)
   *
   * @param string $name Immutable. The full resource name of the widget config.
   * Format: `projects/{project}/locations/{location}/collections/{collection_id}/
   * dataStores/{data_store_id}/widgetConfigs/{widget_config_id}`. This field must
   * be a UTF-8 encoded string with a length limit of 1024 characters.
   * @param GoogleCloudDiscoveryengineV1WidgetConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Indicates which fields in the provided
   * WidgetConfig to update. The following are the only supported fields: *
   * WidgetConfig.enable_autocomplete If not set, all supported fields are
   * updated.
   * @return GoogleCloudDiscoveryengineV1WidgetConfig
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1WidgetConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1WidgetConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsEnginesWidgetConfigs::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsEnginesWidgetConfigs');
