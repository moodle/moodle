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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1FeatureViewSync;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListFeatureViewSyncsResponse;

/**
 * The "featureViewSyncs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $featureViewSyncs = $aiplatformService->projects_locations_featureOnlineStores_featureViews_featureViewSyncs;
 *  </code>
 */
class ProjectsLocationsFeatureOnlineStoresFeatureViewsFeatureViewSyncs extends \Google\Service\Resource
{
  /**
   * Gets details of a single FeatureViewSync. (featureViewSyncs.get)
   *
   * @param string $name Required. The name of the FeatureViewSync resource.
   * Format: `projects/{project}/locations/{location}/featureOnlineStores/{feature
   * _online_store}/featureViews/{feature_view}/featureViewSyncs/{feature_view_syn
   * c}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1FeatureViewSync
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1FeatureViewSync::class);
  }
  /**
   * Lists FeatureViewSyncs in a given FeatureView. (featureViewSyncs.listProjects
   * LocationsFeatureOnlineStoresFeatureViewsFeatureViewSyncs)
   *
   * @param string $parent Required. The resource name of the FeatureView to list
   * FeatureViewSyncs. Format: `projects/{project}/locations/{location}/featureOnl
   * ineStores/{feature_online_store}/featureViews/{feature_view}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the FeatureViewSyncs that match the filter
   * expression. The following filters are supported: * `create_time`: Supports
   * `=`, `!=`, `<`, `>`, `>=`, and `<=` comparisons. Values must be in RFC 3339
   * format. Examples: * `create_time > \"2020-01-31T15:30:00.000000Z\"` -->
   * FeatureViewSyncs created after 2020-01-31T15:30:00.000000Z.
   * @opt_param string orderBy A comma-separated list of fields to order by,
   * sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `create_time`
   * @opt_param int pageSize The maximum number of FeatureViewSyncs to return. The
   * service may return fewer than this value. If unspecified, at most 1000
   * FeatureViewSyncs will be returned. The maximum value is 1000; any value
   * greater than 1000 will be coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * FeatureOnlineStoreAdminService.ListFeatureViewSyncs call. Provide this to
   * retrieve the subsequent page. When paginating, all other parameters provided
   * to FeatureOnlineStoreAdminService.ListFeatureViewSyncs must match the call
   * that provided the page token.
   * @return GoogleCloudAiplatformV1ListFeatureViewSyncsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsFeatureOnlineStoresFeatureViewsFeatureViewSyncs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListFeatureViewSyncsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsFeatureOnlineStoresFeatureViewsFeatureViewSyncs::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsFeatureOnlineStoresFeatureViewsFeatureViewSyncs');
