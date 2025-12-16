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

namespace Google\Service\Backupdr\Resource;

use Google\Service\Backupdr\DataSourceReference;
use Google\Service\Backupdr\FetchDataSourceReferencesForResourceTypeResponse;
use Google\Service\Backupdr\ListDataSourceReferencesResponse;

/**
 * The "dataSourceReferences" collection of methods.
 * Typical usage is:
 *  <code>
 *   $backupdrService = new Google\Service\Backupdr(...);
 *   $dataSourceReferences = $backupdrService->projects_locations_dataSourceReferences;
 *  </code>
 */
class ProjectsLocationsDataSourceReferences extends \Google\Service\Resource
{
  /**
   * Fetch DataSourceReferences for a given project, location and resource type.
   * (dataSourceReferences.fetchForResourceType)
   *
   * @param string $parent Required. The parent resource name. Format:
   * projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter expression that filters the
   * results fetched in the response. The expression must specify the field name,
   * a comparison operator, and the value that you want to use for filtering.
   * Supported fields: * data_source *
   * data_source_gcp_resource_info.gcp_resourcename *
   * data_source_backup_config_state * data_source_backup_count *
   * data_source_backup_config_info.last_backup_state *
   * data_source_gcp_resource_info.gcp_resourcename *
   * data_source_gcp_resource_info.type * data_source_gcp_resource_info.location *
   * data_source_gcp_resource_info.cloud_sql_instance_properties.instance_create_t
   * ime
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * name
   * @opt_param int pageSize Optional. The maximum number of DataSourceReferences
   * to return. The service may return fewer than this value. If unspecified, at
   * most 50 DataSourceReferences will be returned. The maximum value is 100;
   * values above 100 will be coerced to 100.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * call of `FetchDataSourceReferencesForResourceType`. Provide this to retrieve
   * the subsequent page. When paginating, all other parameters provided to
   * `FetchDataSourceReferencesForResourceType` must match the call that provided
   * the page token.
   * @opt_param string resourceType Required. The type of the GCP resource. Ex:
   * sql.googleapis.com/Instance
   * @return FetchDataSourceReferencesForResourceTypeResponse
   * @throws \Google\Service\Exception
   */
  public function fetchForResourceType($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('fetchForResourceType', [$params], FetchDataSourceReferencesForResourceTypeResponse::class);
  }
  /**
   * Gets details of a single DataSourceReference. (dataSourceReferences.get)
   *
   * @param string $name Required. The name of the DataSourceReference to
   * retrieve. Format: projects/{project}/locations/{location}/dataSourceReference
   * s/{data_source_reference}
   * @param array $optParams Optional parameters.
   * @return DataSourceReference
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DataSourceReference::class);
  }
  /**
   * Lists DataSourceReferences for a given project and location.
   * (dataSourceReferences.listProjectsLocationsDataSourceReferences)
   *
   * @param string $parent Required. The parent resource name. Format:
   * projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter expression that filters the
   * results listed in the response. The expression must specify the field name, a
   * comparison operator, and the value that you want to use for filtering. The
   * following field and operator combinations are supported: *
   * data_source_gcp_resource_info.gcp_resourcename with `=`, `!=` *
   * data_source_gcp_resource_info.type with `=`, `!=`
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * data_source *
   * data_source_gcp_resource_info.gcp_resourcename
   * @opt_param int pageSize Optional. The maximum number of DataSourceReferences
   * to return. The service may return fewer than this value. If unspecified, at
   * most 50 DataSourceReferences will be returned. The maximum value is 100;
   * values above 100 will be coerced to 100.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListDataSourceReferences` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListDataSourceReferences` must match the call that provided the page token.
   * @return ListDataSourceReferencesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDataSourceReferences($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDataSourceReferencesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDataSourceReferences::class, 'Google_Service_Backupdr_Resource_ProjectsLocationsDataSourceReferences');
