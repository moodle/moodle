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

namespace Google\Service\Assuredworkloads\Resource;

use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateRequest;
use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1ListWorkloadUpdatesResponse;
use Google\Service\Assuredworkloads\GoogleLongrunningOperation;

/**
 * The "updates" collection of methods.
 * Typical usage is:
 *  <code>
 *   $assuredworkloadsService = new Google\Service\Assuredworkloads(...);
 *   $updates = $assuredworkloadsService->organizations_locations_workloads_updates;
 *  </code>
 */
class OrganizationsLocationsWorkloadsUpdates extends \Google\Service\Resource
{
  /**
   * This endpoint creates a new operation to apply the given update.
   * (updates.apply)
   *
   * @param string $name Required. The resource name of the update. Format: organi
   * zations/{org_id}/locations/{location_id}/workloads/{workload_id}/updates/{upd
   * ate_id}
   * @param GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function apply($name, GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('apply', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * This endpoint lists all updates for the given workload.
   * (updates.listOrganizationsLocationsWorkloadsUpdates)
   *
   * @param string $parent Required.
   * organizations/{org_id}/locations/{location_id}/workloads/{workload_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Page size. The default value is 20 and the max
   * allowed value is 100.
   * @opt_param string pageToken Page token returned from previous request.
   * @return GoogleCloudAssuredworkloadsV1ListWorkloadUpdatesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsWorkloadsUpdates($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAssuredworkloadsV1ListWorkloadUpdatesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsWorkloadsUpdates::class, 'Google_Service_Assuredworkloads_Resource_OrganizationsLocationsWorkloadsUpdates');
