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

namespace Google\Service\AppHub\Resource;

use Google\Service\AppHub\ExtendedMetadataSchema;
use Google\Service\AppHub\ListExtendedMetadataSchemasResponse;

/**
 * The "extendedMetadataSchemas" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apphubService = new Google\Service\AppHub(...);
 *   $extendedMetadataSchemas = $apphubService->projects_locations_extendedMetadataSchemas;
 *  </code>
 */
class ProjectsLocationsExtendedMetadataSchemas extends \Google\Service\Resource
{
  /**
   * Gets an Extended Metadata Schema. (extendedMetadataSchemas.get)
   *
   * @param string $name Required. Schema resource name Format:
   * projects//locations//extendedMetadataSchemas/ could be
   * "apphub.googleapis.com/Name"
   * @param array $optParams Optional parameters.
   * @return ExtendedMetadataSchema
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ExtendedMetadataSchema::class);
  }
  /**
   * Lists Extended Metadata Schemas available in a host project and location.
   * (extendedMetadataSchemas.listProjectsLocationsExtendedMetadataSchemas)
   *
   * @param string $parent Required. Project and location to list Extended
   * Metadata Schemas on. Expected format:
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListExtendedMetadataSchemasResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsExtendedMetadataSchemas($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListExtendedMetadataSchemasResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsExtendedMetadataSchemas::class, 'Google_Service_AppHub_Resource_ProjectsLocationsExtendedMetadataSchemas');
