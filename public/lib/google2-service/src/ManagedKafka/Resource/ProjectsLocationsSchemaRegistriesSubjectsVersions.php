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

use Google\Service\ManagedKafka\CreateVersionRequest;
use Google\Service\ManagedKafka\CreateVersionResponse;
use Google\Service\ManagedKafka\HttpBody;
use Google\Service\ManagedKafka\SchemaVersion;

/**
 * The "versions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $versions = $managedkafkaService->projects_locations_schemaRegistries_subjects_versions;
 *  </code>
 */
class ProjectsLocationsSchemaRegistriesSubjectsVersions extends \Google\Service\Resource
{
  /**
   * Register a new version under a given subject with the given schema.
   * (versions.create)
   *
   * @param string $parent Required. The subject to create the version for.
   * Structured like: `projects/{project}/locations/{location}/schemaRegistries/{s
   * chema_registry}/subjects/{subject}` or `projects/{project}/locations/{locatio
   * n}/schemaRegistries/{schema_registry}/contexts/{context}/subjects/{subject}`
   * @param CreateVersionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CreateVersionResponse
   * @throws \Google\Service\Exception
   */
  public function create($parent, CreateVersionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], CreateVersionResponse::class);
  }
  /**
   * Delete a version of a subject. The response will be the deleted version id.
   * (versions.delete)
   *
   * @param string $name Required. The name of the subject version to delete.
   * Structured like: `projects/{project}/locations/{location}/schemaRegistries/{s
   * chema_registry}/subjects/{subject}/versions/{version}` or `projects/{project}
   * /locations/{location}/schemaRegistries/{schema_registry}/contexts/{context}/s
   * ubjects/{subject}/versions/{version}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool permanent Optional. If true, both the version and the
   * referenced schema ID will be permanently deleted. The default is false. If
   * false, the version will be deleted but the schema ID will be retained. Soft-
   * deleted versions can still be searched in ListVersions API call with
   * deleted=true query parameter. A soft-delete of a version must be performed
   * before a hard-delete.
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
   * Get a versioned schema (schema with subject/version) of a subject.
   * (versions.get)
   *
   * @param string $name Required. The name of the subject to return versions.
   * Structured like: `projects/{project}/locations/{location}/schemaRegistries/{s
   * chema_registry}/subjects/{subject}/versions/{version}` or `projects/{project}
   * /locations/{location}/schemaRegistries/{schema_registry}/contexts/{context}/s
   * ubjects/{subject}/versions/{version}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool deleted Optional. If true, no matter if the subject/version
   * is soft-deleted or not, it returns the version details. If false, it returns
   * NOT_FOUND error if the subject/version is soft-deleted. The default is false.
   * @return SchemaVersion
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SchemaVersion::class);
  }
  /**
   * Get the schema string only for a version of a subject. The response will be
   * the schema string. (versions.getSchema)
   *
   * @param string $name Required. The name of the subject to return versions.
   * Structured like: `projects/{project}/locations/{location}/schemaRegistries/{s
   * chema_registry}/subjects/{subject}/versions/{version}` or `projects/{project}
   * /locations/{location}/schemaRegistries/{schema_registry}/contexts/{context}/s
   * ubjects/{subject}/versions/{version}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool deleted Optional. If true, no matter if the subject/version
   * is soft-deleted or not, it returns the version details. If false, it returns
   * NOT_FOUND error if the subject/version is soft-deleted. The default is false.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function getSchema($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getSchema', [$params], HttpBody::class);
  }
  /**
   * Get all versions of a subject. The response will be an array of versions of
   * the subject. (versions.listProjectsLocationsSchemaRegistriesSubjectsVersions)
   *
   * @param string $parent Required. The subject whose versions are to be listed.
   * Structured like: `projects/{project}/locations/{location}/schemaRegistries/{s
   * chema_registry}/subjects/{subject}` or `projects/{project}/locations/{locatio
   * n}/schemaRegistries/{schema_registry}/contexts/{context}/subjects/{subject}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool deleted Optional. If true, the response will include soft-
   * deleted versions of an active or soft-deleted subject. The default is false.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSchemaRegistriesSubjectsVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistriesSubjectsVersions::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistriesSubjectsVersions');
