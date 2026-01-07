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

use Google\Service\BigLakeService\Database;
use Google\Service\BigLakeService\ListDatabasesResponse;

/**
 * The "databases" collection of methods.
 * Typical usage is:
 *  <code>
 *   $biglakeService = new Google\Service\BigLakeService(...);
 *   $databases = $biglakeService->projects_locations_catalogs_databases;
 *  </code>
 */
class ProjectsLocationsCatalogsDatabases extends \Google\Service\Resource
{
  /**
   * Creates a new database. (databases.create)
   *
   * @param string $parent Required. The parent resource where this database will
   * be created. Format:
   * projects/{project_id_or_number}/locations/{location_id}/catalogs/{catalog_id}
   * @param Database $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string databaseId Required. The ID to use for the database, which
   * will become the final component of the database's resource name.
   * @return Database
   * @throws \Google\Service\Exception
   */
  public function create($parent, Database $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Database::class);
  }
  /**
   * Deletes an existing database specified by the database ID. (databases.delete)
   *
   * @param string $name Required. The name of the database to delete. Format: pro
   * jects/{project_id_or_number}/locations/{location_id}/catalogs/{catalog_id}/da
   * tabases/{database_id}
   * @param array $optParams Optional parameters.
   * @return Database
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Database::class);
  }
  /**
   * Gets the database specified by the resource name. (databases.get)
   *
   * @param string $name Required. The name of the database to retrieve. Format: p
   * rojects/{project_id_or_number}/locations/{location_id}/catalogs/{catalog_id}/
   * databases/{database_id}
   * @param array $optParams Optional parameters.
   * @return Database
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Database::class);
  }
  /**
   * List all databases in a specified catalog.
   * (databases.listProjectsLocationsCatalogsDatabases)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * databases. Format:
   * projects/{project_id_or_number}/locations/{location_id}/catalogs/{catalog_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of databases to return. The
   * service may return fewer than this value. If unspecified, at most 50
   * databases will be returned. The maximum value is 1000; values above 1000 will
   * be coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListDatabases` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListDatabases` must match the
   * call that provided the page token.
   * @return ListDatabasesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCatalogsDatabases($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDatabasesResponse::class);
  }
  /**
   * Updates an existing database specified by the database ID. (databases.patch)
   *
   * @param string $name Output only. The resource name. Format: projects/{project
   * _id_or_number}/locations/{location_id}/catalogs/{catalog_id}/databases/{datab
   * ase_id}
   * @param Database $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields to update. For the
   * `FieldMask` definition, see https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask If not set, defaults to all
   * of the fields that are allowed to update.
   * @return Database
   * @throws \Google\Service\Exception
   */
  public function patch($name, Database $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Database::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCatalogsDatabases::class, 'Google_Service_BigLakeService_Resource_ProjectsLocationsCatalogsDatabases');
