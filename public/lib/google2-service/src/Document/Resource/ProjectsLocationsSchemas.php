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

use Google\Service\Document\GoogleCloudDocumentaiV1ListSchemasResponse;
use Google\Service\Document\GoogleCloudDocumentaiV1NextSchema;
use Google\Service\Document\GoogleLongrunningOperation;

/**
 * The "schemas" collection of methods.
 * Typical usage is:
 *  <code>
 *   $documentaiService = new Google\Service\Document(...);
 *   $schemas = $documentaiService->projects_locations_schemas;
 *  </code>
 */
class ProjectsLocationsSchemas extends \Google\Service\Resource
{
  /**
   * Creates a schema. (schemas.create)
   *
   * @param string $parent Required. The parent (project and location) under which
   * to create the Schema. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudDocumentaiV1NextSchema $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDocumentaiV1NextSchema
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDocumentaiV1NextSchema $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDocumentaiV1NextSchema::class);
  }
  /**
   * Deletes a schema. (schemas.delete)
   *
   * @param string $name Required. The name of the Schema to be deleted. Format:
   * `projects/{project}/locations/{location}/schemas/{schema}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, any child resources of this
   * Schema will also be deleted. (Otherwise, the request will only work if the
   * Schema has no child resources.)
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
   * Gets a schema. (schemas.get)
   *
   * @param string $name Required. The name of the Schema to get. Format:
   * `projects/{project}/locations/{location}/schemas/{schema}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDocumentaiV1NextSchema
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDocumentaiV1NextSchema::class);
  }
  /**
   * Lists Schemas. (schemas.listProjectsLocationsSchemas)
   *
   * @param string $parent Required. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of schema groups to
   * return. If unspecified, at most `10` Schema will be returned. The maximum
   * value is `20`. Values above `20` will be coerced to `20`.
   * @opt_param string pageToken Optional. We will return the schema groups sorted
   * by creation time. The page token will point to the next Schema.
   * @return GoogleCloudDocumentaiV1ListSchemasResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSchemas($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDocumentaiV1ListSchemasResponse::class);
  }
  /**
   * Updates a schema. Editable fields are: - `display_name` - `labels`
   * (schemas.patch)
   *
   * @param string $name Identifier. The resource name of the Schema. Format:
   * `projects/{project}/locations/{location}/schemas/{schema}`
   * @param GoogleCloudDocumentaiV1NextSchema $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The update mask to apply to the
   * resource. **Note:** Only the following fields can be updated: - display_name.
   * - labels.
   * @return GoogleCloudDocumentaiV1NextSchema
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDocumentaiV1NextSchema $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDocumentaiV1NextSchema::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemas::class, 'Google_Service_Document_Resource_ProjectsLocationsSchemas');
