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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1IdentityMappingStore;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ImportIdentityMappingsRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListIdentityMappingStoresResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListIdentityMappingsResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequest;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "identityMappingStores" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $identityMappingStores = $discoveryengineService->projects_locations_identityMappingStores;
 *  </code>
 */
class ProjectsLocationsIdentityMappingStores extends \Google\Service\Resource
{
  /**
   * Creates a new Identity Mapping Store. (identityMappingStores.create)
   *
   * @param string $parent Required. The parent collection resource name, such as
   * `projects/{project}/locations/{location}`.
   * @param GoogleCloudDiscoveryengineV1IdentityMappingStore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string cmekConfigName Resource name of the CmekConfig to use for
   * protecting this Identity Mapping Store.
   * @opt_param bool disableCmek Identity Mapping Store without CMEK protections.
   * If a default CmekConfig is set for the project, setting this field will
   * override the default CmekConfig as well.
   * @opt_param string identityMappingStoreId Required. The ID of the Identity
   * Mapping Store to create. The ID must contain only letters (a-z, A-Z), numbers
   * (0-9), underscores (_), and hyphens (-). The maximum length is 63 characters.
   * @return GoogleCloudDiscoveryengineV1IdentityMappingStore
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1IdentityMappingStore $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDiscoveryengineV1IdentityMappingStore::class);
  }
  /**
   * Deletes the Identity Mapping Store. (identityMappingStores.delete)
   *
   * @param string $name Required. The name of the Identity Mapping Store to
   * delete. Format: `projects/{project}/locations/{location}/identityMappingStore
   * s/{identityMappingStore}`
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
   * Gets the Identity Mapping Store. (identityMappingStores.get)
   *
   * @param string $name Required. The name of the Identity Mapping Store to get.
   * Format: `projects/{project}/locations/{location}/identityMappingStores/{ident
   * ityMappingStore}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1IdentityMappingStore
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1IdentityMappingStore::class);
  }
  /**
   * Imports a list of Identity Mapping Entries to an Identity Mapping Store.
   * (identityMappingStores.importIdentityMappings)
   *
   * @param string $identityMappingStore Required. The name of the Identity
   * Mapping Store to import Identity Mapping Entries to. Format: `projects/{proje
   * ct}/locations/{location}/identityMappingStores/{identityMappingStore}`
   * @param GoogleCloudDiscoveryengineV1ImportIdentityMappingsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function importIdentityMappings($identityMappingStore, GoogleCloudDiscoveryengineV1ImportIdentityMappingsRequest $postBody, $optParams = [])
  {
    $params = ['identityMappingStore' => $identityMappingStore, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('importIdentityMappings', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Lists all Identity Mapping Stores.
   * (identityMappingStores.listProjectsLocationsIdentityMappingStores)
   *
   * @param string $parent Required. The parent of the Identity Mapping Stores to
   * list. Format: `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of IdentityMappingStores to return. If
   * unspecified, defaults to 100. The maximum allowed value is 1000. Values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListIdentityMappingStores` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListIdentityMappingStores` must match the call that provided the page token.
   * @return GoogleCloudDiscoveryengineV1ListIdentityMappingStoresResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsIdentityMappingStores($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListIdentityMappingStoresResponse::class);
  }
  /**
   * Lists Identity Mappings in an Identity Mapping Store.
   * (identityMappingStores.listIdentityMappings)
   *
   * @param string $identityMappingStore Required. The name of the Identity
   * Mapping Store to list Identity Mapping Entries in. Format: `projects/{project
   * }/locations/{location}/identityMappingStores/{identityMappingStore}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of IdentityMappings to return. If
   * unspecified, defaults to 2000. The maximum allowed value is 10000. Values
   * above 10000 will be coerced to 10000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListIdentityMappings` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListIdentityMappings` must
   * match the call that provided the page token.
   * @return GoogleCloudDiscoveryengineV1ListIdentityMappingsResponse
   * @throws \Google\Service\Exception
   */
  public function listIdentityMappings($identityMappingStore, $optParams = [])
  {
    $params = ['identityMappingStore' => $identityMappingStore];
    $params = array_merge($params, $optParams);
    return $this->call('listIdentityMappings', [$params], GoogleCloudDiscoveryengineV1ListIdentityMappingsResponse::class);
  }
  /**
   * Purges specified or all Identity Mapping Entries from an Identity Mapping
   * Store. (identityMappingStores.purgeIdentityMappings)
   *
   * @param string $identityMappingStore Required. The name of the Identity
   * Mapping Store to purge Identity Mapping Entries from. Format: `projects/{proj
   * ect}/locations/{location}/identityMappingStores/{identityMappingStore}`
   * @param GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function purgeIdentityMappings($identityMappingStore, GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequest $postBody, $optParams = [])
  {
    $params = ['identityMappingStore' => $identityMappingStore, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('purgeIdentityMappings', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsIdentityMappingStores::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsIdentityMappingStores');
