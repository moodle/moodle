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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1Index;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListIndexesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1RemoveDatapointsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1RemoveDatapointsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1UpsertDatapointsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1UpsertDatapointsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "indexes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $indexes = $aiplatformService->projects_locations_indexes;
 *  </code>
 */
class ProjectsLocationsIndexes extends \Google\Service\Resource
{
  /**
   * Creates an Index. (indexes.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the Index in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1Index $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1Index $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes an Index. An Index can only be deleted when all its DeployedIndexes
   * had been undeployed. (indexes.delete)
   *
   * @param string $name Required. The name of the Index resource to be deleted.
   * Format: `projects/{project}/locations/{location}/indexes/{index}`
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
   * Gets an Index. (indexes.get)
   *
   * @param string $name Required. The name of the Index resource. Format:
   * `projects/{project}/locations/{location}/indexes/{index}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Index
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Index::class);
  }
  /**
   * Lists Indexes in a Location. (indexes.listProjectsLocationsIndexes)
   *
   * @param string $parent Required. The resource name of the Location from which
   * to list the Indexes. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The standard list filter.
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token. Typically obtained
   * via ListIndexesResponse.next_page_token of the previous
   * IndexService.ListIndexes call.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListIndexesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsIndexes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListIndexesResponse::class);
  }
  /**
   * Updates an Index. (indexes.patch)
   *
   * @param string $name Output only. The resource name of the Index.
   * @param GoogleCloudAiplatformV1Index $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The update mask applies to the resource. For the
   * `FieldMask` definition, see google.protobuf.FieldMask.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1Index $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Remove Datapoints from an Index. (indexes.removeDatapoints)
   *
   * @param string $index Required. The name of the Index resource to be updated.
   * Format: `projects/{project}/locations/{location}/indexes/{index}`
   * @param GoogleCloudAiplatformV1RemoveDatapointsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1RemoveDatapointsResponse
   * @throws \Google\Service\Exception
   */
  public function removeDatapoints($index, GoogleCloudAiplatformV1RemoveDatapointsRequest $postBody, $optParams = [])
  {
    $params = ['index' => $index, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('removeDatapoints', [$params], GoogleCloudAiplatformV1RemoveDatapointsResponse::class);
  }
  /**
   * Add/update Datapoints into an Index. (indexes.upsertDatapoints)
   *
   * @param string $index Required. The name of the Index resource to be updated.
   * Format: `projects/{project}/locations/{location}/indexes/{index}`
   * @param GoogleCloudAiplatformV1UpsertDatapointsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1UpsertDatapointsResponse
   * @throws \Google\Service\Exception
   */
  public function upsertDatapoints($index, GoogleCloudAiplatformV1UpsertDatapointsRequest $postBody, $optParams = [])
  {
    $params = ['index' => $index, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upsertDatapoints', [$params], GoogleCloudAiplatformV1UpsertDatapointsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsIndexes::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsIndexes');
