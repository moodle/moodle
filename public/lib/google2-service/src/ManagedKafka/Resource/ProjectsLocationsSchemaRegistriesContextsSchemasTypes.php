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

/**
 * The "types" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $types = $managedkafkaService->projects_locations_schemaRegistries_contexts_schemas_types;
 *  </code>
 */
class ProjectsLocationsSchemaRegistriesContextsSchemasTypes extends \Google\Service\Resource
{
  /**
   * List the supported schema types. The response will be an array of schema
   * types. (types.listProjectsLocationsSchemaRegistriesContextsSchemasTypes)
   *
   * @param string $parent Required. The parent schema registry whose schema types
   * are to be listed. Structured like:
   * `projects/{project}/locations/{location}/schemaRegistries/{schema_registry}`
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSchemaRegistriesContextsSchemasTypes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistriesContextsSchemasTypes::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistriesContextsSchemasTypes');
