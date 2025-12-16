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

use Google\Service\Appengine\Operation;
use Google\Service\Appengine\Service;

/**
 * The "services" collection of methods.
 * Typical usage is:
 *  <code>
 *   $appengineService = new Google\Service\Appengine(...);
 *   $services = $appengineService->projects_locations_applications_services;
 *  </code>
 */
class ProjectsLocationsApplicationsServices extends \Google\Service\Resource
{
  /**
   * Deletes the specified service and all enclosed versions. (services.delete)
   *
   * @param string $projectsId Part of `name`. Required. Name of the resource
   * requested. Example: apps/myapp/services/default.
   * @param string $locationsId Part of `name`. See documentation of `projectsId`.
   * @param string $applicationsId Part of `name`. See documentation of
   * `projectsId`.
   * @param string $servicesId Part of `name`. See documentation of `projectsId`.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($projectsId, $locationsId, $applicationsId, $servicesId, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'servicesId' => $servicesId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Updates the configuration of the specified service. (services.patch)
   *
   * @param string $projectsId Part of `name`. Required. Name of the resource to
   * update. Example: apps/myapp/services/default.
   * @param string $locationsId Part of `name`. See documentation of `projectsId`.
   * @param string $applicationsId Part of `name`. See documentation of
   * `projectsId`.
   * @param string $servicesId Part of `name`. See documentation of `projectsId`.
   * @param Service $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool migrateTraffic Set to true to gradually shift traffic to one
   * or more versions that you specify. By default, traffic is shifted
   * immediately. For gradual traffic migration, the target versions must be
   * located within instances that are configured for both warmup requests
   * (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#InboundServiceType) and
   * automatic scaling (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#AutomaticScaling). You must
   * specify the shardBy (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services#ShardBy) field in the Service resource.
   * Gradual traffic migration is not supported in the App Engine flexible
   * environment. For examples, see Migrating and Splitting Traffic
   * (https://cloud.google.com/appengine/docs/admin-api/migrating-splitting-
   * traffic).
   * @opt_param string updateMask Required. Standard field mask for the set of
   * fields to be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($projectsId, $locationsId, $applicationsId, $servicesId, Service $postBody, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'servicesId' => $servicesId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApplicationsServices::class, 'Google_Service_Appengine_Resource_ProjectsLocationsApplicationsServices');
