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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1AclConfig;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1CmekConfig;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1DataConnector;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1SetUpDataConnectorRequest;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $locations = $discoveryengineService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Gets the AclConfig. (locations.getAclConfig)
   *
   * @param string $name Required. Resource name of AclConfig, such as
   * `projects/locations/aclConfig`. If the caller does not have permission to
   * access the AclConfig, regardless of whether or not it exists, a
   * PERMISSION_DENIED error is returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1AclConfig
   * @throws \Google\Service\Exception
   */
  public function getAclConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getAclConfig', [$params], GoogleCloudDiscoveryengineV1AclConfig::class);
  }
  /**
   * Gets the CmekConfig. (locations.getCmekConfig)
   *
   * @param string $name Required. Resource name of CmekConfig, such as
   * `projects/locations/cmekConfig` or `projects/locations/cmekConfigs`. If the
   * caller does not have permission to access the CmekConfig, regardless of
   * whether or not it exists, a PERMISSION_DENIED error is returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1CmekConfig
   * @throws \Google\Service\Exception
   */
  public function getCmekConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getCmekConfig', [$params], GoogleCloudDiscoveryengineV1CmekConfig::class);
  }
  /**
   * Creates a Collection and sets up the DataConnector for it. To stop a
   * DataConnector after setup, use the CollectionService.DeleteCollection method.
   * (locations.setUpDataConnector)
   *
   * @param string $parent Required. The parent of Collection, in the format of
   * `projects/{project}/locations/{location}`.
   * @param GoogleCloudDiscoveryengineV1SetUpDataConnectorRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function setUpDataConnector($parent, GoogleCloudDiscoveryengineV1SetUpDataConnectorRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setUpDataConnector', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Creates a Collection and sets up the DataConnector for it. To stop a
   * DataConnector after setup, use the CollectionService.DeleteCollection method.
   * (locations.setUpDataConnectorV2)
   *
   * @param string $parent Required. The parent of Collection, in the format of
   * `projects/{project}/locations/{location}`.
   * @param GoogleCloudDiscoveryengineV1DataConnector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string collectionDisplayName Required. The display name of the
   * Collection. Should be human readable, used to display collections in the
   * Console Dashboard. UTF-8 encoded string with limit of 1024 characters.
   * @opt_param string collectionId Required. The ID to use for the Collection,
   * which will become the final component of the Collection's resource name. A
   * new Collection is created as part of the DataConnector setup. DataConnector
   * is a singleton resource under Collection, managing all DataStores of the
   * Collection. This field must conform to
   * [RFC-1034](https://tools.ietf.org/html/rfc1034) standard with a length limit
   * of 63 characters. Otherwise, an INVALID_ARGUMENT error is returned.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function setUpDataConnectorV2($parent, GoogleCloudDiscoveryengineV1DataConnector $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setUpDataConnectorV2', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Default ACL configuration for use in a location of a customer's project.
   * Updates will only reflect to new data stores. Existing data stores will still
   * use the old value. (locations.updateAclConfig)
   *
   * @param string $name Immutable. The full resource name of the acl
   * configuration. Format: `projects/{project}/locations/{location}/aclConfig`.
   * This field must be a UTF-8 encoded string with a length limit of 1024
   * characters.
   * @param GoogleCloudDiscoveryengineV1AclConfig $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1AclConfig
   * @throws \Google\Service\Exception
   */
  public function updateAclConfig($name, GoogleCloudDiscoveryengineV1AclConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateAclConfig', [$params], GoogleCloudDiscoveryengineV1AclConfig::class);
  }
  /**
   * Provisions a CMEK key for use in a location of a customer's project. This
   * method will also conduct location validation on the provided cmekConfig to
   * make sure the key is valid and can be used in the selected location.
   * (locations.updateCmekConfig)
   *
   * @param string $name Required. The name of the CmekConfig of the form
   * `projects/{project}/locations/{location}/cmekConfig` or
   * `projects/{project}/locations/{location}/cmekConfigs/{cmek_config}`.
   * @param GoogleCloudDiscoveryengineV1CmekConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool setDefault Set the following CmekConfig as the default to be
   * used for child resources if one is not specified.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function updateCmekConfig($name, GoogleCloudDiscoveryengineV1CmekConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateCmekConfig', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocations');
