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

namespace Google\Service\Document\Resource;

use Google\Service\Document\GoogleCloudDocumentaiV1GenerateSchemaVersionRequest;
use Google\Service\Document\GoogleCloudDocumentaiV1GenerateSchemaVersionResponse;
use Google\Service\Document\GoogleCloudDocumentaiV1ListSchemaVersionsResponse;
use Google\Service\Document\GoogleCloudDocumentaiV1SchemaVersion;
use Google\Service\Document\GoogleLongrunningOperation;

/**
 * The "schemaVersions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $documentaiService = new Google\Service\Document(...);
 *   $schemaVersions = $documentaiService->projects_locations_schemas_schemaVersions;
 *  </code>
 */
class ProjectsLocationsSchemasSchemaVersions extends \Google\Service\Resource
{
  /**
   * Creates a schema version. (schemaVersions.create)
   *
   * @param string $parent Required. The parent (project and location) under which
   * to create the SchemaVersion. Format:
   * `projects/{project}/locations/{location}/schemas/{schema}`
   * @param GoogleCloudDocumentaiV1SchemaVersion $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDocumentaiV1SchemaVersion
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDocumentaiV1SchemaVersion $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDocumentaiV1SchemaVersion::class);
  }
  /**
   * Deletes a schema version. (schemaVersions.delete)
   *
   * @param string $name Required. The name of the SchemaVersion to delete.
   * Format: `projects/{project}/locations/{location}/schemas/{schema}/schemaVersi
   * ons/{schema_version}`
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Generates a schema version. (schemaVersions.generate)
   *
   * @param string $parent Required. The parent (project, location and schema)
   * under which to generate the SchemaVersion. Format:
   * `projects/{project}/locations/{location}/schemas/{schema}`
   * @param GoogleCloudDocumentaiV1GenerateSchemaVersionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDocumentaiV1GenerateSchemaVersionResponse
   * @throws \Google\Service\Exception
   */
  public function generate($parent, GoogleCloudDocumentaiV1GenerateSchemaVersionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generate', [$params], GoogleCloudDocumentaiV1GenerateSchemaVersionResponse::class);
  }
  /**
   * Gets a schema version. (schemaVersions.get)
   *
   * @param string $name Required. The name of the SchemaVersion to get. Format: `
   * projects/{project}/locations/{location}/schemas/{schema}/schemaVersions/{sche
   * ma_version}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDocumentaiV1SchemaVersion
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDocumentaiV1SchemaVersion::class);
  }
  /**
   * Lists SchemaVersions.
   * (schemaVersions.listProjectsLocationsSchemasSchemaVersions)
   *
   * @param string $parent Required. Format:
   * `projects/{project}/locations/{location}/schemas/{schema}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of SchemaVersion to
   * return. If unspecified, at most `10` SchemaVersion will be returned. The
   * maximum value is `20`. Values above `20` will be coerced to `20`.
   * @opt_param string pageToken Optional. We will return the SchemaVersion sorted
   * by creation time. The page token will point to the next SchemaVersion.
   * @return GoogleCloudDocumentaiV1ListSchemaVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSchemasSchemaVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDocumentaiV1ListSchemaVersionsResponse::class);
  }
  /**
   * Updates a schema version. Editable fields are: - `display_name` - `labels`
   * (schemaVersions.patch)
   *
   * @param string $name Identifier. The resource name of the SchemaVersion.
   * Format: `projects/{project}/locations/{location}/schemas/{schema}/schemaVersi
   * ons/{schema_version}`
   * @param GoogleCloudDocumentaiV1SchemaVersion $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The update mask to apply to the
   * resource. **Note:** Only the following fields can be updated: - display_name.
   * - labels.
   * @return GoogleCloudDocumentaiV1SchemaVersion
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDocumentaiV1SchemaVersion $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDocumentaiV1SchemaVersion::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemasSchemaVersions::class, 'Google_Service_Document_Resource_ProjectsLocationsSchemasSchemaVersions');
