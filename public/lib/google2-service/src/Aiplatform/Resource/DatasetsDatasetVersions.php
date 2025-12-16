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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1DatasetVersion;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListDatasetVersionsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "datasetVersions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $datasetVersions = $aiplatformService->datasets_datasetVersions;
 *  </code>
 */
class DatasetsDatasetVersions extends \Google\Service\Resource
{
  /**
   * Create a version from a Dataset. (datasetVersions.create)
   *
   * @param string $parent Required. The name of the Dataset resource. Format:
   * `projects/{project}/locations/{location}/datasets/{dataset}`
   * @param GoogleCloudAiplatformV1DatasetVersion $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1DatasetVersion $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Dataset version. (datasetVersions.delete)
   *
   * @param string $name Required. The resource name of the Dataset version to
   * delete. Format: `projects/{project}/locations/{location}/datasets/{dataset}/d
   * atasetVersions/{dataset_version}`
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
   * Gets a Dataset version. (datasetVersions.get)
   *
   * @param string $name Required. The resource name of the Dataset version to
   * delete. Format: `projects/{project}/locations/{location}/datasets/{dataset}/d
   * atasetVersions/{dataset_version}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1DatasetVersion
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1DatasetVersion::class);
  }
  /**
   * Lists DatasetVersions in a Dataset.
   * (datasetVersions.listDatasetsDatasetVersions)
   *
   * @param string $parent Required. The resource name of the Dataset to list
   * DatasetVersions from. Format:
   * `projects/{project}/locations/{location}/datasets/{dataset}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The standard list filter.
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order. Use "desc" after a field name for descending.
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token.
   * @opt_param string readMask Optional. Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListDatasetVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listDatasetsDatasetVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListDatasetVersionsResponse::class);
  }
  /**
   * Updates a DatasetVersion. (datasetVersions.patch)
   *
   * @param string $name Output only. Identifier. The resource name of the
   * DatasetVersion. Format: `projects/{project}/locations/{location}/datasets/{da
   * taset}/datasetVersions/{dataset_version}`
   * @param GoogleCloudAiplatformV1DatasetVersion $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask applies to the
   * resource. For the `FieldMask` definition, see google.protobuf.FieldMask.
   * Updatable fields: * `display_name`
   * @return GoogleCloudAiplatformV1DatasetVersion
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1DatasetVersion $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1DatasetVersion::class);
  }
  /**
   * Restores a dataset version. (datasetVersions.restore)
   *
   * @param string $name Required. The name of the DatasetVersion resource.
   * Format: `projects/{project}/locations/{location}/datasets/{dataset}/datasetVe
   * rsions/{dataset_version}`
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function restore($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatasetsDatasetVersions::class, 'Google_Service_Aiplatform_Resource_DatasetsDatasetVersions');
