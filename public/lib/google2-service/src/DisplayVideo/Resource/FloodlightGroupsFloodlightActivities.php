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

namespace Google\Service\DisplayVideo\Resource;

use Google\Service\DisplayVideo\FloodlightActivity;
use Google\Service\DisplayVideo\ListFloodlightActivitiesResponse;

/**
 * The "floodlightActivities" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $floodlightActivities = $displayvideoService->floodlightGroups_floodlightActivities;
 *  </code>
 */
class FloodlightGroupsFloodlightActivities extends \Google\Service\Resource
{
  /**
   * Gets a Floodlight activity. (floodlightActivities.get)
   *
   * @param string $floodlightGroupId Required. The ID of the parent Floodlight
   * group to which the requested Floodlight activity belongs.
   * @param string $floodlightActivityId Required. The ID of the Floodlight
   * activity to fetch.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string partnerId Required. The ID of the partner through which the
   * Floodlight activity is being accessed.
   * @return FloodlightActivity
   * @throws \Google\Service\Exception
   */
  public function get($floodlightGroupId, $floodlightActivityId, $optParams = [])
  {
    $params = ['floodlightGroupId' => $floodlightGroupId, 'floodlightActivityId' => $floodlightActivityId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], FloodlightActivity::class);
  }
  /**
   * Lists Floodlight activities in a Floodlight group.
   * (floodlightActivities.listFloodlightGroupsFloodlightActivities)
   *
   * @param string $floodlightGroupId Required. The ID of the parent Floodlight
   * group to which the requested Floodlight activities belong.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Optional. Field by which to sort the list.
   * Acceptable values are: * `displayName` (default) * `floodlightActivityId` The
   * default sorting order is ascending. To specify descending order for a field,
   * a suffix "desc" should be added to the field name. Example: `displayName
   * desc`.
   * @opt_param int pageSize Optional. Requested page size. Must be between `1`
   * and `200`. If unspecified will default to `100`. Returns error code
   * `INVALID_ARGUMENT` if an invalid value is specified.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return. Typically, this is the value of next_page_token
   * returned from the previous call to `ListFloodlightActivities` method. If not
   * specified, the first page of results will be returned.
   * @opt_param string partnerId Required. The ID of the partner through which the
   * Floodlight activities are being accessed.
   * @return ListFloodlightActivitiesResponse
   * @throws \Google\Service\Exception
   */
  public function listFloodlightGroupsFloodlightActivities($floodlightGroupId, $optParams = [])
  {
    $params = ['floodlightGroupId' => $floodlightGroupId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListFloodlightActivitiesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FloodlightGroupsFloodlightActivities::class, 'Google_Service_DisplayVideo_Resource_FloodlightGroupsFloodlightActivities');
