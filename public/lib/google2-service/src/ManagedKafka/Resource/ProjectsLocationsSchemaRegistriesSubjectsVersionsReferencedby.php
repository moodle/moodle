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
 * The "referencedby" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $referencedby = $managedkafkaService->projects_locations_schemaRegistries_subjects_versions_referencedby;
 *  </code>
 */
class ProjectsLocationsSchemaRegistriesSubjectsVersionsReferencedby extends \Google\Service\Resource
{
  /**
   * Get a list of IDs of schemas that reference the schema with the given subject
   * and version. (referencedby.listProjectsLocationsSchemaRegistriesSubjectsVersi
   * onsReferencedby)
   *
   * @param string $parent Required. The version to list referenced by. Structured
   * like: `projects/{project}/locations/{location}/schemaRegistries/{schema_regis
   * try}/subjects/{subject}/versions/{version}` or `projects/{project}/locations/
   * {location}/schemaRegistries/{schema_registry}/contexts/{context}/subjects/{su
   * bject}/versions/{version}`
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSchemaRegistriesSubjectsVersionsReferencedby($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistriesSubjectsVersionsReferencedby::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistriesSubjectsVersionsReferencedby');
