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

namespace Google\Service\Appengine\Resource;

use Google\Service\Appengine\Application;
use Google\Service\Appengine\Operation;

/**
 * The "applications" collection of methods.
 * Typical usage is:
 *  <code>
 *   $appengineService = new Google\Service\Appengine(...);
 *   $applications = $appengineService->projects_locations_applications;
 *  </code>
 */
class ProjectsLocationsApplications extends \Google\Service\Resource
{
  /**
   * Updates the specified Application resource. You can update the following
   * fields: auth_domain - Google authentication domain for controlling user
   * access to the application. default_cookie_expiration - Cookie expiration
   * policy for the application. iap - Identity-Aware Proxy properties for the
   * application. (applications.patch)
   *
   * @param string $projectsId Part of `name`. Required. Name of the Application
   * resource to update. Example: apps/myapp.
   * @param string $locationsId Part of `name`. See documentation of `projectsId`.
   * @param string $applicationsId Part of `name`. See documentation of
   * `projectsId`.
   * @param Application $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Standard field mask for the set of
   * fields to be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($projectsId, $locationsId, $applicationsId, Application $postBody, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApplications::class, 'Google_Service_Appengine_Resource_ProjectsLocationsApplications');
