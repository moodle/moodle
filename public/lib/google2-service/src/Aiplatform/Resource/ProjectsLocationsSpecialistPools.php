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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListSpecialistPoolsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SpecialistPool;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "specialistPools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $specialistPools = $aiplatformService->projects_locations_specialistPools;
 *  </code>
 */
class ProjectsLocationsSpecialistPools extends \Google\Service\Resource
{
  /**
   * Creates a SpecialistPool. (specialistPools.create)
   *
   * @param string $parent Required. The parent Project name for the new
   * SpecialistPool. The form is `projects/{project}/locations/{location}`.
   * @param GoogleCloudAiplatformV1SpecialistPool $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1SpecialistPool $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a SpecialistPool as well as all Specialists in the pool.
   * (specialistPools.delete)
   *
   * @param string $name Required. The resource name of the SpecialistPool to
   * delete. Format:
   * `projects/{project}/locations/{location}/specialistPools/{specialist_pool}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force If set to true, any specialist managers in this
   * SpecialistPool will also be deleted. (Otherwise, the request will only work
   * if the SpecialistPool has no specialist managers.)
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a SpecialistPool. (specialistPools.get)
   *
   * @param string $name Required. The name of the SpecialistPool resource. The
   * form is
   * `projects/{project}/locations/{location}/specialistPools/{specialist_pool}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1SpecialistPool
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1SpecialistPool::class);
  }
  /**
   * Lists SpecialistPools in a Location.
   * (specialistPools.listProjectsLocationsSpecialistPools)
   *
   * @param string $parent Required. The name of the SpecialistPool's parent
   * resource. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token. Typically obtained
   * by ListSpecialistPoolsResponse.next_page_token of the previous
   * SpecialistPoolService.ListSpecialistPools call. Return first page if empty.
   * @opt_param string readMask Mask specifying which fields to read. FieldMask
   * represents a set of
   * @return GoogleCloudAiplatformV1ListSpecialistPoolsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSpecialistPools($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListSpecialistPoolsResponse::class);
  }
  /**
   * Updates a SpecialistPool. (specialistPools.patch)
   *
   * @param string $name Required. The resource name of the SpecialistPool.
   * @param GoogleCloudAiplatformV1SpecialistPool $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask applies to the
   * resource.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1SpecialistPool $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSpecialistPools::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsSpecialistPools');
