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

namespace Google\Service\Playdeveloperreporting\Resource;

use Google\Service\Playdeveloperreporting\GooglePlayDeveloperReportingV1beta1ReleaseFilterOptions;
use Google\Service\Playdeveloperreporting\GooglePlayDeveloperReportingV1beta1SearchAccessibleAppsResponse;

/**
 * The "apps" collection of methods.
 * Typical usage is:
 *  <code>
 *   $playdeveloperreportingService = new Google\Service\Playdeveloperreporting(...);
 *   $apps = $playdeveloperreportingService->apps;
 *  </code>
 */
class Apps extends \Google\Service\Resource
{
  /**
   * Describes filtering options for releases. (apps.fetchReleaseFilterOptions)
   *
   * @param string $name Required. Name of the resource, i.e. app the filtering
   * options are for. Format: apps/{app}
   * @param array $optParams Optional parameters.
   * @return GooglePlayDeveloperReportingV1beta1ReleaseFilterOptions
   * @throws \Google\Service\Exception
   */
  public function fetchReleaseFilterOptions($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('fetchReleaseFilterOptions', [$params], GooglePlayDeveloperReportingV1beta1ReleaseFilterOptions::class);
  }
  /**
   * Searches for Apps accessible by the user. (apps.search)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of apps to return. The service may
   * return fewer than this value. If unspecified, at most 50 apps will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `SearchAccessibleApps` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `SearchAccessibleApps` must
   * match the call that provided the page token.
   * @return GooglePlayDeveloperReportingV1beta1SearchAccessibleAppsResponse
   * @throws \Google\Service\Exception
   */
  public function search($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GooglePlayDeveloperReportingV1beta1SearchAccessibleAppsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Apps::class, 'Google_Service_Playdeveloperreporting_Resource_Apps');
