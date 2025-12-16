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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1Dataset;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListDatasetsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "datasets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $datasets = $aiplatformService->datasets;
 *  </code>
 */
class Datasets extends \Google\Service\Resource
{
  /**
   * Creates a Dataset. (datasets.create)
   *
   * @param GoogleCloudAiplatformV1Dataset $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Required. The resource name of the Location to
   * create the Dataset in. Format: `projects/{project}/locations/{location}`
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create(GoogleCloudAiplatformV1Dataset $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Dataset. (datasets.delete)
   *
   * @param string $name Required. The resource name of the Dataset to delete.
   * Format: `projects/{project}/locations/{location}/datasets/{dataset}`
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
   * Gets a Dataset. (datasets.get)
   *
   * @param string $name Required. The name of the Dataset resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1Dataset
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Dataset::class);
  }
  /**
   * Lists Datasets in a Location. (datasets.listDatasets)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression for filtering the results of the
   * request. For field names both snake_case and camelCase are supported. *
   * `display_name`: supports = and != * `metadata_schema_uri`: supports = and !=
   * * `labels` supports general map functions that is: * `labels.key=value` -
   * key:value equality * `labels.key:* or labels:key - key existence * A key
   * including a space must be quoted. `labels."a key"`. Some examples: *
   * `displayName="myDisplayName"` * `labels.myKey="myValue"`
   * @opt_param string orderBy A comma-separated list of fields to order by,
   * sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `display_name` * `create_time` * `update_time`
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token.
   * @opt_param string parent Required. The name of the Dataset's parent resource.
   * Format: `projects/{project}/locations/{location}`
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListDatasetsResponse
   * @throws \Google\Service\Exception
   */
  public function listDatasets($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListDatasetsResponse::class);
  }
  /**
   * Updates a Dataset. (datasets.patch)
   *
   * @param string $name Output only. Identifier. The resource name of the
   * Dataset. Format: `projects/{project}/locations/{location}/datasets/{dataset}`
   * @param GoogleCloudAiplatformV1Dataset $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask applies to the
   * resource. For the `FieldMask` definition, see google.protobuf.FieldMask.
   * Updatable fields: * `display_name` * `description` * `labels`
   * @return GoogleCloudAiplatformV1Dataset
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1Dataset $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1Dataset::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Datasets::class, 'Google_Service_Aiplatform_Resource_Datasets');
