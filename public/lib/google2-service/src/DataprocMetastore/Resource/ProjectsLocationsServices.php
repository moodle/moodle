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

namespace Google\Service\DataprocMetastore\Resource;

use Google\Service\DataprocMetastore\GoogleCloudMetastoreV2AlterMetadataResourceLocationRequest;
use Google\Service\DataprocMetastore\GoogleCloudMetastoreV2AlterTablePropertiesRequest;
use Google\Service\DataprocMetastore\GoogleCloudMetastoreV2ExportMetadataRequest;
use Google\Service\DataprocMetastore\GoogleCloudMetastoreV2ImportMetadataRequest;
use Google\Service\DataprocMetastore\GoogleCloudMetastoreV2ListServicesResponse;
use Google\Service\DataprocMetastore\GoogleCloudMetastoreV2MoveTableToDatabaseRequest;
use Google\Service\DataprocMetastore\GoogleCloudMetastoreV2QueryMetadataRequest;
use Google\Service\DataprocMetastore\GoogleCloudMetastoreV2RestoreServiceRequest;
use Google\Service\DataprocMetastore\GoogleCloudMetastoreV2Service;
use Google\Service\DataprocMetastore\GoogleLongrunningOperation;

/**
 * The "services" collection of methods.
 * Typical usage is:
 *  <code>
 *   $metastoreService = new Google\Service\DataprocMetastore(...);
 *   $services = $metastoreService->projects_locations_services;
 *  </code>
 */
class ProjectsLocationsServices extends \Google\Service\Resource
{
  /**
   * Alter metadata resource location. The metadata resource can be a database,
   * table, or partition. This functionality only updates the parent directory for
   * the respective metadata resource and does not transfer any existing data to
   * the new location. (services.alterLocation)
   *
   * @param string $service Required. The relative resource name of the metastore
   * service to mutate metadata, in the following
   * format:projects/{project_id}/locations/{location_id}/services/{service_id}.
   * @param GoogleCloudMetastoreV2AlterMetadataResourceLocationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function alterLocation($service, GoogleCloudMetastoreV2AlterMetadataResourceLocationRequest $postBody, $optParams = [])
  {
    $params = ['service' => $service, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('alterLocation', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Alter metadata table properties. (services.alterTableProperties)
   *
   * @param string $service Required. The relative resource name of the Dataproc
   * Metastore service that's being used to mutate metadata table properties, in
   * the following
   * format:projects/{project_id}/locations/{location_id}/services/{service_id}.
   * @param GoogleCloudMetastoreV2AlterTablePropertiesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function alterTableProperties($service, GoogleCloudMetastoreV2AlterTablePropertiesRequest $postBody, $optParams = [])
  {
    $params = ['service' => $service, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('alterTableProperties', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Creates a metastore service in a project and location. (services.create)
   *
   * @param string $parent Required. The relative resource name of the location in
   * which to create a metastore service, in the following
   * form:projects/{project_number}/locations/{location_id}.
   * @param GoogleCloudMetastoreV2Service $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID. Specify a unique request
   * ID to allow the server to ignore the request if it has completed. The server
   * will ignore subsequent requests that provide a duplicate request ID for at
   * least 60 minutes after the first request.For example, if an initial request
   * times out, followed by another request with the same request ID, the server
   * ignores the second request to prevent the creation of duplicate
   * commitments.The request ID must be a valid UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier#Format) A zero
   * UUID (00000000-0000-0000-0000-000000000000) is not supported.
   * @opt_param string serviceId Required. The ID of the metastore service, which
   * is used as the final component of the metastore service's name.This value
   * must be between 2 and 63 characters long inclusive, begin with a letter, end
   * with a letter or number, and consist of alpha-numeric ASCII characters or
   * hyphens.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudMetastoreV2Service $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a single service. (services.delete)
   *
   * @param string $name Required. The relative resource name of the metastore
   * service to delete, in the following
   * form:projects/{project_number}/locations/{location_id}/services/{service_id}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID. Specify a unique request
   * ID to allow the server to ignore the request if it has completed. The server
   * will ignore subsequent requests that provide a duplicate request ID for at
   * least 60 minutes after the first request.For example, if an initial request
   * times out, followed by another request with the same request ID, the server
   * ignores the second request to prevent the creation of duplicate
   * commitments.The request ID must be a valid UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier#Format) A zero
   * UUID (00000000-0000-0000-0000-000000000000) is not supported.
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
   * Exports metadata from a service. (services.exportMetadata)
   *
   * @param string $service Required. The relative resource name of the metastore
   * service to run export, in the following
   * form:projects/{project_id}/locations/{location_id}/services/{service_id}.
   * @param GoogleCloudMetastoreV2ExportMetadataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function exportMetadata($service, GoogleCloudMetastoreV2ExportMetadataRequest $postBody, $optParams = [])
  {
    $params = ['service' => $service, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportMetadata', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets the details of a single service. (services.get)
   *
   * @param string $name Required. The relative resource name of the metastore
   * service to retrieve, in the following
   * form:projects/{project_number}/locations/{location_id}/services/{service_id}.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudMetastoreV2Service
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudMetastoreV2Service::class);
  }
  /**
   * Imports Metadata into a Dataproc Metastore service. (services.importMetadata)
   *
   * @param string $name Immutable. The relative resource name of the metastore
   * service to run import, in the following
   * form:projects/{project_id}/locations/{location_id}/services/{service_id}.
   * @param GoogleCloudMetastoreV2ImportMetadataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function importMetadata($name, GoogleCloudMetastoreV2ImportMetadataRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('importMetadata', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Lists services in a project and location.
   * (services.listProjectsLocationsServices)
   *
   * @param string $parent Required. The relative resource name of the location of
   * metastore services to list, in the following
   * form:projects/{project_number}/locations/{location_id}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter to apply to list results.
   * @opt_param string orderBy Optional. Specify the ordering of results as
   * described in Sorting Order
   * (https://cloud.google.com/apis/design/design_patterns#sorting_order). If not
   * specified, the results will be sorted in the default order.
   * @opt_param int pageSize Optional. The maximum number of services to return.
   * The response may contain less than the maximum number. If unspecified, no
   * more than 500 services are returned. The maximum value is 1000; values above
   * 1000 are changed to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * DataprocMetastore.ListServices call. Provide this token to retrieve the
   * subsequent page.To retrieve the first page, supply an empty page token.When
   * paginating, other parameters provided to DataprocMetastore.ListServices must
   * match the call that provided the page token.
   * @return GoogleCloudMetastoreV2ListServicesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsServices($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudMetastoreV2ListServicesResponse::class);
  }
  /**
   * Move a table to another database. (services.moveTableToDatabase)
   *
   * @param string $service Required. The relative resource name of the metastore
   * service to mutate metadata, in the following
   * format:projects/{project_id}/locations/{location_id}/services/{service_id}.
   * @param GoogleCloudMetastoreV2MoveTableToDatabaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function moveTableToDatabase($service, GoogleCloudMetastoreV2MoveTableToDatabaseRequest $postBody, $optParams = [])
  {
    $params = ['service' => $service, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('moveTableToDatabase', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Updates the parameters of a single service. (services.patch)
   *
   * @param string $name Immutable. The relative resource name of the metastore
   * service, in the following format:projects/{project_number}/locations/{locatio
   * n_id}/services/{service_id}.
   * @param GoogleCloudMetastoreV2Service $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID. Specify a unique request
   * ID to allow the server to ignore the request if it has completed. The server
   * will ignore subsequent requests that provide a duplicate request ID for at
   * least 60 minutes after the first request.For example, if an initial request
   * times out, followed by another request with the same request ID, the server
   * ignores the second request to prevent the creation of duplicate
   * commitments.The request ID must be a valid UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier#Format) A zero
   * UUID (00000000-0000-0000-0000-000000000000) is not supported.
   * @opt_param string updateMask Required. A field mask used to specify the
   * fields to be overwritten in the metastore service resource by the update.
   * Fields specified in the update_mask are relative to the resource (not to the
   * full request). A field is overwritten if it is in the mask.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudMetastoreV2Service $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Query Dataproc Metastore metadata. (services.queryMetadata)
   *
   * @param string $service Required. The relative resource name of the metastore
   * service to query metadata, in the following
   * format:projects/{project_id}/locations/{location_id}/services/{service_id}.
   * @param GoogleCloudMetastoreV2QueryMetadataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function queryMetadata($service, GoogleCloudMetastoreV2QueryMetadataRequest $postBody, $optParams = [])
  {
    $params = ['service' => $service, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('queryMetadata', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Restores a service from a backup. (services.restore)
   *
   * @param string $service Required. The relative resource name of the metastore
   * service to run restore, in the following
   * form:projects/{project_id}/locations/{location_id}/services/{service_id}.
   * @param GoogleCloudMetastoreV2RestoreServiceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function restore($service, GoogleCloudMetastoreV2RestoreServiceRequest $postBody, $optParams = [])
  {
    $params = ['service' => $service, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsServices::class, 'Google_Service_DataprocMetastore_Resource_ProjectsLocationsServices');
