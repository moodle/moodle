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

use Google\Service\ManagedKafka\SchemaMode;
use Google\Service\ManagedKafka\UpdateSchemaModeRequest;

/**
 * The "mode" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $mode = $managedkafkaService->projects_locations_schemaRegistries_contexts_mode;
 *  </code>
 */
class ProjectsLocationsSchemaRegistriesContextsMode extends \Google\Service\Resource
{
  /**
   * Delete schema mode for a subject. (mode.delete)
   *
   * @param string $name Required. The resource name of subject to delete the mode
   * for. The format is * projects/{project}/locations/{location}/schemaRegistries
   * /{schema_registry}/mode/{subject} * projects/{project}/locations/{location}/s
   * chemaRegistries/{schema_registry}/contexts/{context}/mode/{subject}
   * @param array $optParams Optional parameters.
   * @return SchemaMode
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], SchemaMode::class);
  }
  /**
   * Get mode at global level or for a subject. (mode.get)
   *
   * @param string $name Required. The resource name of the mode. The format is *
   * projects/{project}/locations/{location}/schemaRegistries/{schema_registry}/mo
   * de/{subject}: mode for a schema registry, or * projects/{project}/locations/{
   * location}/schemaRegistries/{schema_registry}/contexts/{context}/mode/{subject
   * }: mode for a specific subject in a specific context
   * @param array $optParams Optional parameters.
   * @return SchemaMode
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SchemaMode::class);
  }
  /**
   * Update mode at global level or for a subject. (mode.update)
   *
   * @param string $name Required. The resource name of the mode. The format is *
   * projects/{project}/locations/{location}/schemaRegistries/{schema_registry}/mo
   * de/{subject}: mode for a schema registry, or * projects/{project}/locations/{
   * location}/schemaRegistries/{schema_registry}/contexts/{context}/mode/{subject
   * }: mode for a specific subject in a specific context
   * @param UpdateSchemaModeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SchemaMode
   * @throws \Google\Service\Exception
   */
  public function update($name, UpdateSchemaModeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], SchemaMode::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistriesContextsMode::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistriesContextsMode');
