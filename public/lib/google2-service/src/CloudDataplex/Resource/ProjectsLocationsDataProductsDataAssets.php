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

namespace Google\Service\CloudDataplex\Resource;

use Google\Service\CloudDataplex\GoogleCloudDataplexV1DataAsset;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1ListDataAssetsResponse;
use Google\Service\CloudDataplex\GoogleLongrunningOperation;

/**
 * The "dataAssets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $dataAssets = $dataplexService->projects_locations_dataProducts_dataAssets;
 *  </code>
 */
class ProjectsLocationsDataProductsDataAssets extends \Google\Service\Resource
{
  /**
   * Creates a Data Asset. (dataAssets.create)
   *
   * @param string $parent Required. The parent resource where this Data Asset
   * will be created. Format: projects/{project_id_or_number}/locations/{location_
   * id}/dataProducts/{data_product_id}
   * @param GoogleCloudDataplexV1DataAsset $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dataAssetId Optional. The ID of the Data Asset to
   * create.The ID must conform to RFC-1034 and contain only lower-case letters
   * (a-z), numbers (0-9), or hyphens, with the first character a letter, the last
   * a letter or a number, and a 63 character maximum. Characters outside of ASCII
   * are not permitted. Valid format regex: (^a-z?$) If not provided, a system
   * generated ID will be used.
   * @opt_param bool validateOnly Optional. Validates the request without actually
   * creating the Data Asset. Defaults to false.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1DataAsset $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Data Asset. (dataAssets.delete)
   *
   * @param string $name Required. The name of the Data Asset to delete. Format: p
   * rojects/{project_id_or_number}/locations/{location_id}/dataProducts/{data_pro
   * duct_id}/dataAssets/{data_asset_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The etag of the Data Asset. If this is
   * provided, it must match the server's etag. If the etag is provided and does
   * not match the server-computed etag, the request must fail with a ABORTED
   * error code.
   * @opt_param bool validateOnly Optional. Validates the request without actually
   * deleting the Data Asset. Defaults to false.
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
   * Gets a Data Asset. (dataAssets.get)
   *
   * @param string $name Required. The name of the Data Asset to retrieve. Format:
   * projects/{project_id_or_number}/locations/{location_id}/dataProducts/{data_pr
   * oduct_id}/dataAssets/{data_asset_id}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1DataAsset
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1DataAsset::class);
  }
  /**
   * Lists Data Assets for a given Data Product.
   * (dataAssets.listProjectsLocationsDataProductsDataAssets)
   *
   * @param string $parent Required. The parent, which has this collection of Data
   * Assets. Format: projects/{project_id_or_number}/locations/{location_id}/dataP
   * roducts/{data_product_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that filters DataAssets
   * listed in the response.
   * @opt_param string orderBy Optional. Order by expression that orders
   * DataAssets listed in the response.Supported Order by fields are: name or
   * create_time.If not specified, the ordering is undefined.
   * @opt_param int pageSize Optional. The maximum number of Data Assets to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 Data Assets will be returned. The maximum value is 1000; values above 1000
   * will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * ListDataAssets call. Provide this to retrieve the subsequent page.When
   * paginating, all other parameters provided to ListDataAssets must match the
   * call that provided the page token.
   * @return GoogleCloudDataplexV1ListDataAssetsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDataProductsDataAssets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDataplexV1ListDataAssetsResponse::class);
  }
  /**
   * Updates a Data Asset. (dataAssets.patch)
   *
   * @param string $name Identifier. Resource name of the Data Asset. Format: proj
   * ects/{project_id_or_number}/locations/{location_id}/dataProducts/{data_produc
   * t_id}/dataAssets/{data_asset_id}
   * @param GoogleCloudDataplexV1DataAsset $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update. If this
   * is empty or not set, then all fields that are populated (have a non-empty
   * value) in data_asset above will be updated.
   * @opt_param bool validateOnly Optional. Validates the request without actually
   * updating the Data Asset. Defaults to false.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDataplexV1DataAsset $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDataProductsDataAssets::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocationsDataProductsDataAssets');
