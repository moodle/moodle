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

namespace Google\Service\CloudAlloyDBAdmin\Resource;

use Google\Service\CloudAlloyDBAdmin\ListSupportedDatabaseFlagsResponse;

/**
 * The "supportedDatabaseFlags" collection of methods.
 * Typical usage is:
 *  <code>
 *   $alloydbService = new Google\Service\CloudAlloyDBAdmin(...);
 *   $supportedDatabaseFlags = $alloydbService->projects_locations_supportedDatabaseFlags;
 *  </code>
 */
class ProjectsLocationsSupportedDatabaseFlags extends \Google\Service\Resource
{
  /**
   * Lists SupportedDatabaseFlags for a given project and location.
   * (supportedDatabaseFlags.listProjectsLocationsSupportedDatabaseFlags)
   *
   * @param string $parent Required. The name of the parent resource. The required
   * format is: * projects/{project}/locations/{location} Regardless of the parent
   * specified here, as long it is contains a valid project and location, the
   * service will return a static list of supported flags resources. Note that we
   * do not yet support region-specific flags.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @opt_param string scope Optional. The scope for which supported flags are
   * requested. If not specified, default is DATABASE.
   * @return ListSupportedDatabaseFlagsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSupportedDatabaseFlags($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSupportedDatabaseFlagsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSupportedDatabaseFlags::class, 'Google_Service_CloudAlloyDBAdmin_Resource_ProjectsLocationsSupportedDatabaseFlags');
