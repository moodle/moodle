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

use Google\Service\ManagedKafka\SchemaConfig;
use Google\Service\ManagedKafka\UpdateSchemaConfigRequest;

/**
 * The "config" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $config = $managedkafkaService->projects_locations_schemaRegistries_contexts_config;
 *  </code>
 */
class ProjectsLocationsSchemaRegistriesContextsConfig extends \Google\Service\Resource
{
  /**
   * Delete schema config for a subject. (config.delete)
   *
   * @param string $name Required. The resource name of subject to delete the
   * config for. The format is * projects/{project}/locations/{location}/schemaReg
   * istries/{schema_registry}/config/{subject}
   * @param array $optParams Optional parameters.
   * @return SchemaConfig
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], SchemaConfig::class);
  }
  /**
   * Get schema config at global level or for a subject. (config.get)
   *
   * @param string $name Required. The resource name to get the config for. It can
   * be either of following: * projects/{project}/locations/{location}/schemaRegis
   * tries/{schema_registry}/config: Get config at global level. * projects/{proje
   * ct}/locations/{location}/schemaRegistries/{schema_registry}/config/{subject}:
   * Get config for a specific subject.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool defaultToGlobal Optional. If true, the config will fall back
   * to the config at the global level if no subject level config is found.
   * @return SchemaConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SchemaConfig::class);
  }
  /**
   * Update config at global level or for a subject. Creates a SchemaSubject-level
   * SchemaConfig if it does not exist. (config.update)
   *
   * @param string $name Required. The resource name to update the config for. It
   * can be either of following: * projects/{project}/locations/{location}/schemaR
   * egistries/{schema_registry}/config: Update config at global level. * projects
   * /{project}/locations/{location}/schemaRegistries/{schema_registry}/config/{su
   * bject}: Update config for a specific subject.
   * @param UpdateSchemaConfigRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SchemaConfig
   * @throws \Google\Service\Exception
   */
  public function update($name, UpdateSchemaConfigRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], SchemaConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistriesContextsConfig::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistriesContextsConfig');
