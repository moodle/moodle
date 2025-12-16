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

use Google\Service\BigLakeService\Catalog;
use Google\Service\BigLakeService\ListCatalogsResponse;

/**
 * The "catalogs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $biglakeService = new Google\Service\BigLakeService(...);
 *   $catalogs = $biglakeService->projects_locations_catalogs;
 *  </code>
 */
class ProjectsLocationsCatalogs extends \Google\Service\Resource
{
  /**
   * Creates a new catalog. (catalogs.create)
   *
   * @param string $parent Required. The parent resource where this catalog will
   * be created. Format: projects/{project_id_or_number}/locations/{location_id}
   * @param Catalog $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string catalogId Required. The ID to use for the catalog, which
   * will become the final component of the catalog's resource name.
   * @return Catalog
   * @throws \Google\Service\Exception
   */
  public function create($parent, Catalog $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Catalog::class);
  }
  /**
   * Deletes an existing catalog specified by the catalog ID. (catalogs.delete)
   *
   * @param string $name Required. The name of the catalog to delete. Format:
   * projects/{project_id_or_number}/locations/{location_id}/catalogs/{catalog_id}
   * @param array $optParams Optional parameters.
   * @return Catalog
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Catalog::class);
  }
  /**
   * Gets the catalog specified by the resource name. (catalogs.get)
   *
   * @param string $name Required. The name of the catalog to retrieve. Format:
   * projects/{project_id_or_number}/locations/{location_id}/catalogs/{catalog_id}
   * @param array $optParams Optional parameters.
   * @return Catalog
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Catalog::class);
  }
  /**
   * List all catalogs in a specified project.
   * (catalogs.listProjectsLocationsCatalogs)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * catalogs. Format: projects/{project_id_or_number}/locations/{location_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of catalogs to return. The service
   * may return fewer than this value. If unspecified, at most 50 catalogs will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListCatalogs` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListCatalogs` must match the
   * call that provided the page token.
   * @return ListCatalogsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCatalogs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCatalogsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCatalogs::class, 'Google_Service_BigLakeService_Resource_ProjectsLocationsCatalogs');
