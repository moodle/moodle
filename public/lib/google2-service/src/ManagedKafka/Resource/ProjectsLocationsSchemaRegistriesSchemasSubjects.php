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
 * The "subjects" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $subjects = $managedkafkaService->projects_locations_schemaRegistries_schemas_subjects;
 *  </code>
 */
class ProjectsLocationsSchemaRegistriesSchemasSubjects extends \Google\Service\Resource
{
  /**
   * List subjects which reference a particular schema id. The response will be an
   * array of subject names.
   * (subjects.listProjectsLocationsSchemaRegistriesSchemasSubjects)
   *
   * @param string $parent Required. The schema resource whose associated subjects
   * are to be listed. Structured like: `projects/{project}/locations/{location}/s
   * chemaRegistries/{schema_registry}/schemas/ids/{schema}` or `projects/{project
   * }/locations/{location}/schemaRegistries/{schema_registry}/contexts/{context}/
   * schemas/ids/{schema}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool deleted Optional. If true, the response will include soft-
   * deleted subjects. The default is false.
   * @opt_param string subject Optional. The subject to filter the subjects by.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSchemaRegistriesSchemasSubjects($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistriesSchemasSubjects::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistriesSchemasSubjects');
