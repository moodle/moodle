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

namespace Google\Service\DataCatalog\Resource;

use Google\Service\DataCatalog\GoogleCloudDatacatalogV1MigrationConfig;
use Google\Service\DataCatalog\GoogleCloudDatacatalogV1OrganizationConfig;
use Google\Service\DataCatalog\GoogleCloudDatacatalogV1SetConfigRequest;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datacatalogService = new Google\Service\DataCatalog(...);
 *   $locations = $datacatalogService->organizations_locations;
 *  </code>
 */
class OrganizationsLocations extends \Google\Service\Resource
{
  /**
   * Retrieves the configuration related to the migration from Data Catalog to
   * Dataplex Universal Catalog for a specific organization, including all the
   * projects under it which have a separate configuration set.
   * (locations.retrieveConfig)
   *
   * @param string $name Required. The organization whose config is being
   * retrieved.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDatacatalogV1OrganizationConfig
   * @throws \Google\Service\Exception
   */
  public function retrieveConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveConfig', [$params], GoogleCloudDatacatalogV1OrganizationConfig::class);
  }
  /**
   * Retrieves the effective configuration related to the migration from Data
   * Catalog to Dataplex Universal Catalog for a specific organization or project.
   * If there is no specific configuration set for the resource, the setting is
   * checked hierarchicahlly through the ancestors of the resource, starting from
   * the resource itself. (locations.retrieveEffectiveConfig)
   *
   * @param string $name Required. The resource whose effective config is being
   * retrieved.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDatacatalogV1MigrationConfig
   * @throws \Google\Service\Exception
   */
  public function retrieveEffectiveConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveEffectiveConfig', [$params], GoogleCloudDatacatalogV1MigrationConfig::class);
  }
  /**
   * Sets the configuration related to the migration to Dataplex Universal Catalog
   * for an organization or project. (locations.setConfig)
   *
   * @param string $name Required. The organization or project whose config is
   * being specified.
   * @param GoogleCloudDatacatalogV1SetConfigRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDatacatalogV1MigrationConfig
   * @throws \Google\Service\Exception
   */
  public function setConfig($name, GoogleCloudDatacatalogV1SetConfigRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setConfig', [$params], GoogleCloudDatacatalogV1MigrationConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocations::class, 'Google_Service_DataCatalog_Resource_OrganizationsLocations');
