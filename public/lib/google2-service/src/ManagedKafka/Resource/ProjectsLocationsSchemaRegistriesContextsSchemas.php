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

use Google\Service\ManagedKafka\HttpBody;
use Google\Service\ManagedKafka\Schema;

/**
 * The "schemas" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $schemas = $managedkafkaService->projects_locations_schemaRegistries_contexts_schemas;
 *  </code>
 */
class ProjectsLocationsSchemaRegistriesContextsSchemas extends \Google\Service\Resource
{
  /**
   * Get the schema for the given schema id. (schemas.get)
   *
   * @param string $name Required. The name of the schema to return. Structured
   * like: `projects/{project}/locations/{location}/schemaRegistries/{schema_regis
   * try}/schemas/ids/{schema}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string subject Optional. Used to limit the search for the schema
   * ID to a specific subject, otherwise the schema ID will be searched for in all
   * subjects in the given specified context.
   * @return Schema
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Schema::class);
  }
  /**
   * Get the schema string for the given schema id. The response will be the
   * schema string. (schemas.getSchema)
   *
   * @param string $name Required. The name of the schema to return. Structured
   * like: `projects/{project}/locations/{location}/schemaRegistries/{schema_regis
   * try}/schemas/ids/{schema}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string subject Optional. Used to limit the search for the schema
   * ID to a specific subject, otherwise the schema ID will be searched for in all
   * subjects in the given specified context.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function getSchema($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getSchema', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistriesContextsSchemas::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistriesContextsSchemas');
