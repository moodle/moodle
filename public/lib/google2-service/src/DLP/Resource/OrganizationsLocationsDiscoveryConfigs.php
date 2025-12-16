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

namespace Google\Service\DLP\Resource;

use Google\Service\DLP\GooglePrivacyDlpV2CreateDiscoveryConfigRequest;
use Google\Service\DLP\GooglePrivacyDlpV2DiscoveryConfig;
use Google\Service\DLP\GooglePrivacyDlpV2ListDiscoveryConfigsResponse;
use Google\Service\DLP\GooglePrivacyDlpV2UpdateDiscoveryConfigRequest;
use Google\Service\DLP\GoogleProtobufEmpty;

/**
 * The "discoveryConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dlpService = new Google\Service\DLP(...);
 *   $discoveryConfigs = $dlpService->organizations_locations_discoveryConfigs;
 *  </code>
 */
class OrganizationsLocationsDiscoveryConfigs extends \Google\Service\Resource
{
  /**
   * Creates a config for discovery to scan and profile storage.
   * (discoveryConfigs.create)
   *
   * @param string $parent Required. Parent resource name. The format of this
   * value varies depending on the scope of the request (project or organization):
   * + Projects scope: `projects/{project_id}/locations/{location_id}` +
   * Organizations scope: `organizations/{org_id}/locations/{location_id}` The
   * following example `parent` string specifies a parent project with the
   * identifier `example-project`, and specifies the `europe-west3` location for
   * processing data: parent=projects/example-project/locations/europe-west3
   * @param GooglePrivacyDlpV2CreateDiscoveryConfigRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2DiscoveryConfig
   * @throws \Google\Service\Exception
   */
  public function create($parent, GooglePrivacyDlpV2CreateDiscoveryConfigRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GooglePrivacyDlpV2DiscoveryConfig::class);
  }
  /**
   * Deletes a discovery configuration. (discoveryConfigs.delete)
   *
   * @param string $name Required. Resource name of the project and the config,
   * for example `projects/dlp-test-project/discoveryConfigs/53234423`.
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Gets a discovery configuration. (discoveryConfigs.get)
   *
   * @param string $name Required. Resource name of the project and the
   * configuration, for example `projects/dlp-test-
   * project/discoveryConfigs/53234423`.
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2DiscoveryConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GooglePrivacyDlpV2DiscoveryConfig::class);
  }
  /**
   * Lists discovery configurations.
   * (discoveryConfigs.listOrganizationsLocationsDiscoveryConfigs)
   *
   * @param string $parent Required. Parent resource name. The format of this
   * value is as follows: `projects/{project_id}/locations/{location_id}` The
   * following example `parent` string specifies a parent project with the
   * identifier `example-project`, and specifies the `europe-west3` location for
   * processing data: parent=projects/example-project/locations/europe-west3
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Comma-separated list of config fields to order by,
   * followed by `asc` or `desc` postfix. This list is case insensitive. The
   * default sorting order is ascending. Redundant space characters are
   * insignificant. Example: `name asc,update_time, create_time desc` Supported
   * fields are: - `last_run_time`: corresponds to the last time the
   * DiscoveryConfig ran. - `name`: corresponds to the DiscoveryConfig's name. -
   * `status`: corresponds to DiscoveryConfig's status.
   * @opt_param int pageSize Size of the page. This value can be limited by a
   * server.
   * @opt_param string pageToken Page token to continue retrieval. Comes from the
   * previous call to ListDiscoveryConfigs. `order_by` field must not change for
   * subsequent calls.
   * @return GooglePrivacyDlpV2ListDiscoveryConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsDiscoveryConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GooglePrivacyDlpV2ListDiscoveryConfigsResponse::class);
  }
  /**
   * Updates a discovery configuration. (discoveryConfigs.patch)
   *
   * @param string $name Required. Resource name of the project and the
   * configuration, for example `projects/dlp-test-
   * project/discoveryConfigs/53234423`.
   * @param GooglePrivacyDlpV2UpdateDiscoveryConfigRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2DiscoveryConfig
   * @throws \Google\Service\Exception
   */
  public function patch($name, GooglePrivacyDlpV2UpdateDiscoveryConfigRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GooglePrivacyDlpV2DiscoveryConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsDiscoveryConfigs::class, 'Google_Service_DLP_Resource_OrganizationsLocationsDiscoveryConfigs');
