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

namespace Google\Service\MigrationCenterAPI\Resource;

use Google\Service\MigrationCenterAPI\ErrorFrame;
use Google\Service\MigrationCenterAPI\ListErrorFramesResponse;

/**
 * The "errorFrames" collection of methods.
 * Typical usage is:
 *  <code>
 *   $migrationcenterService = new Google\Service\MigrationCenterAPI(...);
 *   $errorFrames = $migrationcenterService->projects_locations_sources_errorFrames;
 *  </code>
 */
class ProjectsLocationsSourcesErrorFrames extends \Google\Service\Resource
{
  /**
   * Gets the details of an error frame. (errorFrames.get)
   *
   * @param string $name Required. The name of the frame to retrieve. Format: proj
   * ects/{project}/locations/{location}/sources/{source}/errorFrames/{error_frame
   * }
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. An optional view mode to control the level
   * of details for the frame. The default is a basic frame view.
   * @return ErrorFrame
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ErrorFrame::class);
  }
  /**
   * Lists all error frames in a given source and location.
   * (errorFrames.listProjectsLocationsSourcesErrorFrames)
   *
   * @param string $parent Required. Parent value (the source) for
   * `ListErrorFramesRequest`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @opt_param string view Optional. An optional view mode to control the level
   * of details of each error frame. The default is a BASIC frame view.
   * @return ListErrorFramesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSourcesErrorFrames($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListErrorFramesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSourcesErrorFrames::class, 'Google_Service_MigrationCenterAPI_Resource_ProjectsLocationsSourcesErrorFrames');
