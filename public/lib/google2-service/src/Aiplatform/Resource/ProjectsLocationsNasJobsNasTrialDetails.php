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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListNasTrialDetailsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1NasTrialDetail;

/**
 * The "nasTrialDetails" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $nasTrialDetails = $aiplatformService->projects_locations_nasJobs_nasTrialDetails;
 *  </code>
 */
class ProjectsLocationsNasJobsNasTrialDetails extends \Google\Service\Resource
{
  /**
   * Gets a NasTrialDetail. (nasTrialDetails.get)
   *
   * @param string $name Required. The name of the NasTrialDetail resource.
   * Format: `projects/{project}/locations/{location}/nasJobs/{nas_job}/nasTrialDe
   * tails/{nas_trial_detail}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1NasTrialDetail
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1NasTrialDetail::class);
  }
  /**
   * List top NasTrialDetails of a NasJob.
   * (nasTrialDetails.listProjectsLocationsNasJobsNasTrialDetails)
   *
   * @param string $parent Required. The name of the NasJob resource. Format:
   * `projects/{project}/locations/{location}/nasJobs/{nas_job}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token. Typically obtained
   * via ListNasTrialDetailsResponse.next_page_token of the previous
   * JobService.ListNasTrialDetails call.
   * @return GoogleCloudAiplatformV1ListNasTrialDetailsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNasJobsNasTrialDetails($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListNasTrialDetailsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNasJobsNasTrialDetails::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsNasJobsNasTrialDetails');
