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

namespace Google\Service\BigLakeService\Resource;

use Google\Service\BigLakeService\ListTablesResponse;
use Google\Service\BigLakeService\RenameTableRequest;
use Google\Service\BigLakeService\Table;

/**
 * The "tables" collection of methods.
 * Typical usage is:
 *  <code>
 *   $biglakeService = new Google\Service\BigLakeService(...);
 *   $tables = $biglakeService->projects_locations_catalogs_databases_tables;
 *  </code>
 */
class ProjectsLocationsCatalogsDatabasesTables extends \Google\Service\Resource
{
  /**
   * Creates a new table. (tables.create)
   *
   * @param string $parent Required. The parent resource where this table will be
   * created. Format: projects/{project_id_or_number}/locations/{location_id}/cata
   * logs/{catalog_id}/databases/{database_id}
   * @param Table $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string tableId Required. The ID to use for the table, which will
   * become the final component of the table's resource name.
   * @return Table
   * @throws \Google\Service\Exception
   */
  public function create($parent, Table $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Table::class);
  }
  /**
   * Deletes an existing table specified by the table ID. (tables.delete)
   *
   * @param string $name Required. The name of the table to delete. Format: projec
   * ts/{project_id_or_number}/locations/{location_id}/catalogs/{catalog_id}/datab
   * ases/{database_id}/tables/{table_id}
   * @param array $optParams Optional parameters.
   * @return Table
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Table::class);
  }
  /**
   * Gets the table specified by the resource name. (tables.get)
   *
   * @param string $name Required. The name of the table to retrieve. Format: proj
   * ects/{project_id_or_number}/locations/{location_id}/catalogs/{catalog_id}/dat
   * abases/{database_id}/tables/{table_id}
   * @param array $optParams Optional parameters.
   * @return Table
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Table::class);
  }
  /**
   * List all tables in a specified database.
   * (tables.listProjectsLocationsCatalogsDatabasesTables)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * tables. Format: projects/{project_id_or_number}/locations/{location_id}/catal
   * ogs/{catalog_id}/databases/{database_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of tables to return. The service
   * may return fewer than this value. If unspecified, at most 50 tables will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListTables` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListTables` must match the call
   * that provided the page token.
   * @opt_param string view The view for the returned tables.
   * @return ListTablesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCatalogsDatabasesTables($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTablesResponse::class);
  }
  /**
   * Updates an existing table specified by the table ID. (tables.patch)
   *
   * @param string $name Output only. The resource name. Format: projects/{project
   * _id_or_number}/locations/{location_id}/catalogs/{catalog_id}/databases/{datab
   * ase_id}/tables/{table_id}
   * @param Table $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields to update. For the
   * `FieldMask` definition, see https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask If not set, defaults to all
   * of the fields that are allowed to update.
   * @return Table
   * @throws \Google\Service\Exception
   */
  public function patch($name, Table $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Table::class);
  }
  /**
   * Renames an existing table specified by the table ID. (tables.rename)
   *
   * @param string $name Required. The table's `name` field is used to identify
   * the table to rename. Format: projects/{project_id_or_number}/locations/{locat
   * ion_id}/catalogs/{catalog_id}/databases/{database_id}/tables/{table_id}
   * @param RenameTableRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Table
   * @throws \Google\Service\Exception
   */
  public function rename($name, RenameTableRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rename', [$params], Table::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCatalogsDatabasesTables::class, 'Google_Service_BigLakeService_Resource_ProjectsLocationsCatalogsDatabasesTables');
