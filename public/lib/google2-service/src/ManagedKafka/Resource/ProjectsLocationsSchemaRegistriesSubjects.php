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
use Google\Service\ManagedKafka\LookupVersionRequest;
use Google\Service\ManagedKafka\SchemaVersion;

/**
 * The "subjects" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $subjects = $managedkafkaService->projects_locations_schemaRegistries_subjects;
 *  </code>
 */
class ProjectsLocationsSchemaRegistriesSubjects extends \Google\Service\Resource
{
  /**
   * Delete a subject. The response will be an array of versions of the deleted
   * subject. (subjects.delete)
   *
   * @param string $name Required. The name of the subject to delete. Structured
   * like: `projects/{project}/locations/{location}/schemaRegistries/{schema_regis
   * try}/subjects/{subject}` or `projects/{project}/locations/{location}/schemaRe
   * gistries/{schema_registry}/contexts/{context}/subjects/{subject}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool permanent Optional. If true, the subject and all associated
   * metadata including the schema ID will be deleted permanently. Otherwise, only
   * the subject is soft-deleted. The default is false. Soft-deleted subjects can
   * still be searched in ListSubjects API call with deleted=true query parameter.
   * A soft-delete of a subject must be performed before a hard-delete.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], HttpBody::class);
  }
  /**
   * List subjects in the schema registry. The response will be an array of
   * subject names. (subjects.listProjectsLocationsSchemaRegistriesSubjects)
   *
   * @param string $parent Required. The parent schema registry/context whose
   * subjects are to be listed. Structured like:
   * `projects/{project}/locations/{location}/schemaRegistries/{schema_registry}`
   * or `projects/{project}/locations/{location}/schemaRegistries/{schema_registry
   * }/contexts/{context}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool deleted Optional. If true, the response will include soft-
   * deleted subjects. The default is false.
   * @opt_param string subjectPrefix Optional. The context to filter the subjects
   * by, in the format of `:.{context}:`. If unset, all subjects in the registry
   * are returned. Set to empty string or add as '?subjectPrefix=' at the end of
   * this request to list subjects in the default context.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSchemaRegistriesSubjects($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], HttpBody::class);
  }
  /**
   * Lookup a schema under the specified subject. (subjects.lookupVersion)
   *
   * @param string $parent Required. The subject to lookup the schema in.
   * Structured like: `projects/{project}/locations/{location}/schemaRegistries/{s
   * chema_registry}/subjects/{subject}` or `projects/{project}/locations/{locatio
   * n}/schemaRegistries/{schema_registry}/contexts/{context}/subjects/{subject}`
   * @param LookupVersionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SchemaVersion
   * @throws \Google\Service\Exception
   */
  public function lookupVersion($parent, LookupVersionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('lookupVersion', [$params], SchemaVersion::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistriesSubjects::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistriesSubjects');
