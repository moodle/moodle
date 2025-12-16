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

use Google\Service\Appengine\ExportAppImageRequest;
use Google\Service\Appengine\Operation;
use Google\Service\Appengine\Version;

/**
 * The "versions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $appengineService = new Google\Service\Appengine(...);
 *   $versions = $appengineService->projects_locations_applications_services_versions;
 *  </code>
 */
class ProjectsLocationsApplicationsServicesVersions extends \Google\Service\Resource
{
  /**
   * Deletes an existing Version resource. (versions.delete)
   *
   * @param string $projectsId Part of `name`. Required. Name of the resource
   * requested. Example: apps/myapp/services/default/versions/v1.
   * @param string $locationsId Part of `name`. See documentation of `projectsId`.
   * @param string $applicationsId Part of `name`. See documentation of
   * `projectsId`.
   * @param string $servicesId Part of `name`. See documentation of `projectsId`.
   * @param string $versionsId Part of `name`. See documentation of `projectsId`.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($projectsId, $locationsId, $applicationsId, $servicesId, $versionsId, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'servicesId' => $servicesId, 'versionsId' => $versionsId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Exports a user image to Artifact Registry. (versions.exportAppImage)
   *
   * @param string $projectsId Part of `name`. Required. Name of the App Engine
   * version resource. Format: apps/{app}/services/{service}/versions/{version}
   * @param string $locationsId Part of `name`. See documentation of `projectsId`.
   * @param string $applicationsId Part of `name`. See documentation of
   * `projectsId`.
   * @param string $servicesId Part of `name`. See documentation of `projectsId`.
   * @param string $versionsId Part of `name`. See documentation of `projectsId`.
   * @param ExportAppImageRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function exportAppImage($projectsId, $locationsId, $applicationsId, $servicesId, $versionsId, ExportAppImageRequest $postBody, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'servicesId' => $servicesId, 'versionsId' => $versionsId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportAppImage', [$params], Operation::class);
  }
  /**
   * Updates the specified Version resource. You can specify the following fields
   * depending on the App Engine environment and type of scaling that the version
   * resource uses:Standard environment instance_class
   * (https://cloud.google.com/appengine/docs/admin-api/reference/rest/v1/apps.ser
   * vices.versions#Version.FIELDS.instance_class)automatic scaling in the
   * standard environment: automatic_scaling.min_idle_instances
   * (https://cloud.google.com/appengine/docs/admin-api/reference/rest/v1/apps.ser
   * vices.versions#Version.FIELDS.automatic_scaling)
   * automatic_scaling.max_idle_instances
   * (https://cloud.google.com/appengine/docs/admin-api/reference/rest/v1/apps.ser
   * vices.versions#Version.FIELDS.automatic_scaling)
   * automaticScaling.standard_scheduler_settings.max_instances
   * (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#StandardSchedulerSettings)
   * automaticScaling.standard_scheduler_settings.min_instances
   * (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#StandardSchedulerSettings)
   * automaticScaling.standard_scheduler_settings.target_cpu_utilization
   * (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#StandardSchedulerSettings)
   * automaticScaling.standard_scheduler_settings.target_throughput_utilization
   * (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#StandardSchedulerSettings)basic
   * scaling or manual scaling in the standard environment: serving_status
   * (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#Version.FIELDS.serving_status)
   * manual_scaling.instances (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#manualscaling)Flexible
   * environment serving_status (https://cloud.google.com/appengine/docs/admin-api
   * /reference/rest/v1/apps.services.versions#Version.FIELDS.serving_status)autom
   * atic scaling in the flexible environment:
   * automatic_scaling.min_total_instances
   * (https://cloud.google.com/appengine/docs/admin-api/reference/rest/v1/apps.ser
   * vices.versions#Version.FIELDS.automatic_scaling)
   * automatic_scaling.max_total_instances
   * (https://cloud.google.com/appengine/docs/admin-api/reference/rest/v1/apps.ser
   * vices.versions#Version.FIELDS.automatic_scaling)
   * automatic_scaling.cool_down_period_sec
   * (https://cloud.google.com/appengine/docs/admin-api/reference/rest/v1/apps.ser
   * vices.versions#Version.FIELDS.automatic_scaling)
   * automatic_scaling.cpu_utilization.target_utilization
   * (https://cloud.google.com/appengine/docs/admin-api/reference/rest/v1/apps.ser
   * vices.versions#Version.FIELDS.automatic_scaling)manual scaling in the
   * flexible environment: manual_scaling.instances
   * (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#manualscaling) (versions.patch)
   *
   * @param string $projectsId Part of `name`. Required. Name of the resource to
   * update. Example: apps/myapp/services/default/versions/1.
   * @param string $locationsId Part of `name`. See documentation of `projectsId`.
   * @param string $applicationsId Part of `name`. See documentation of
   * `projectsId`.
   * @param string $servicesId Part of `name`. See documentation of `projectsId`.
   * @param string $versionsId Part of `name`. See documentation of `projectsId`.
   * @param Version $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Standard field mask for the set of fields to be
   * updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($projectsId, $locationsId, $applicationsId, $servicesId, $versionsId, Version $postBody, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'servicesId' => $servicesId, 'versionsId' => $versionsId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApplicationsServicesVersions::class, 'Google_Service_Appengine_Resource_ProjectsLocationsApplicationsServicesVersions');
