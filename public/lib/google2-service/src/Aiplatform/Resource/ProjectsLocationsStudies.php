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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListStudiesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1LookupStudyRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1Study;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "studies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $studies = $aiplatformService->projects_locations_studies;
 *  </code>
 */
class ProjectsLocationsStudies extends \Google\Service\Resource
{
  /**
   * Creates a Study. A resource name will be generated after creation of the
   * Study. (studies.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the CustomJob in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1Study $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Study
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1Study $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1Study::class);
  }
  /**
   * Deletes a Study. (studies.delete)
   *
   * @param string $name Required. The name of the Study resource to be deleted.
   * Format: `projects/{project}/locations/{location}/studies/{study}`
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Gets a Study by name. (studies.get)
   *
   * @param string $name Required. The name of the Study resource. Format:
   * `projects/{project}/locations/{location}/studies/{study}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Study
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Study::class);
  }
  /**
   * Lists all the studies in a region for an associated project.
   * (studies.listProjectsLocationsStudies)
   *
   * @param string $parent Required. The resource name of the Location to list the
   * Study from. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of studies to return per
   * "page" of results. If unspecified, service will pick an appropriate default.
   * @opt_param string pageToken Optional. A page token to request the next page
   * of results. If unspecified, there are no subsequent pages.
   * @return GoogleCloudAiplatformV1ListStudiesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsStudies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListStudiesResponse::class);
  }
  /**
   * Looks a study up using the user-defined display_name field instead of the
   * fully qualified resource name. (studies.lookup)
   *
   * @param string $parent Required. The resource name of the Location to get the
   * Study from. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1LookupStudyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Study
   * @throws \Google\Service\Exception
   */
  public function lookup($parent, GoogleCloudAiplatformV1LookupStudyRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('lookup', [$params], GoogleCloudAiplatformV1Study::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsStudies::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsStudies');
