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

namespace Google\Service\ManagedKafka\Resource;

use Google\Service\ManagedKafka\CreateSchemaRegistryRequest;
use Google\Service\ManagedKafka\ListSchemaRegistriesResponse;
use Google\Service\ManagedKafka\ManagedkafkaEmpty;
use Google\Service\ManagedKafka\SchemaRegistry;

/**
 * The "schemaRegistries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $schemaRegistries = $managedkafkaService->projects_locations_schemaRegistries;
 *  </code>
 */
class ProjectsLocationsSchemaRegistries extends \Google\Service\Resource
{
  /**
   * Create a schema registry instance. (schemaRegistries.create)
   *
   * @param string $parent Required. The parent whose schema registry instance is
   * to be created. Structured like: `projects/{project}/locations/{location}`
   * @param CreateSchemaRegistryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SchemaRegistry
   * @throws \Google\Service\Exception
   */
  public function create($parent, CreateSchemaRegistryRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], SchemaRegistry::class);
  }
  /**
   * Delete a schema registry instance. (schemaRegistries.delete)
   *
   * @param string $name Required. The name of the schema registry instance to
   * delete. Structured like:
   * `projects/{project}/locations/{location}/schemaRegistries/{schema_registry}`
   * @param array $optParams Optional parameters.
   * @return ManagedkafkaEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ManagedkafkaEmpty::class);
  }
  /**
   * Get the schema registry instance. (schemaRegistries.get)
   *
   * @param string $name Required. The name of the schema registry instance to
   * return. Structured like:
   * `projects/{project}/locations/{location}/schemaRegistries/{schema_registry}`
   * @param array $optParams Optional parameters.
   * @return SchemaRegistry
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SchemaRegistry::class);
  }
  /**
   * List schema registries.
   * (schemaRegistries.listProjectsLocationsSchemaRegistries)
   *
   * @param string $parent Required. The parent whose schema registry instances
   * are to be listed. Structured like: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. Specifies the view to return for the schema
   * registry instances. If not specified, the default view is
   * SCHEMA_REGISTRY_VIEW_BASIC.
   * @return ListSchemaRegistriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSchemaRegistries($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSchemaRegistriesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistries::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistries');
