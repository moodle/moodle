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

use Google\Service\DisplayVideo\AssignedTargetingOption;
use Google\Service\DisplayVideo\ListAdGroupAssignedTargetingOptionsResponse;

/**
 * The "assignedTargetingOptions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $assignedTargetingOptions = $displayvideoService->advertisers_adGroups_targetingTypes_assignedTargetingOptions;
 *  </code>
 */
class AdvertisersAdGroupsTargetingTypesAssignedTargetingOptions extends \Google\Service\Resource
{
  /**
   * Gets a single targeting option assigned to an ad group. Inherited assigned
   * targeting options are not included. (assignedTargetingOptions.get)
   *
   * @param string $advertiserId Required. The ID of the advertiser the ad group
   * belongs to.
   * @param string $adGroupId Required. The ID of the ad group the assigned
   * targeting option belongs to.
   * @param string $targetingType Required. Identifies the type of this assigned
   * targeting option. Supported targeting types include: *
   * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_APP` *
   * `TARGETING_TYPE_APP_CATEGORY` * `TARGETING_TYPE_AUDIENCE_GROUP` *
   * `TARGETING_TYPE_CATEGORY` * `TARGETING_TYPE_GENDER` *
   * `TARGETING_TYPE_HOUSEHOLD_INCOME` * `TARGETING_TYPE_KEYWORD` *
   * `TARGETING_TYPE_PARENTAL_STATUS` * `TARGETING_TYPE_SESSION_POSITION` *
   * `TARGETING_TYPE_URL` * `TARGETING_TYPE_YOUTUBE_CHANNEL` *
   * `TARGETING_TYPE_YOUTUBE_VIDEO`
   * @param string $assignedTargetingOptionId Required. An identifier unique to
   * the targeting type in this line item that identifies the assigned targeting
   * option being requested.
   * @param array $optParams Optional parameters.
   * @return AssignedTargetingOption
   * @throws \Google\Service\Exception
   */
  public function get($advertiserId, $adGroupId, $targetingType, $assignedTargetingOptionId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'adGroupId' => $adGroupId, 'targetingType' => $targetingType, 'assignedTargetingOptionId' => $assignedTargetingOptionId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AssignedTargetingOption::class);
  }
  /**
   * Lists the targeting options assigned to an ad group. Inherited assigned
   * targeting options are not included. (assignedTargetingOptions.listAdvertisers
   * AdGroupsTargetingTypesAssignedTargetingOptions)
   *
   * @param string $advertiserId Required. The ID of the advertiser the ad group
   * belongs to.
   * @param string $adGroupId Required. The ID of the ad group to list assigned
   * targeting options for.
   * @param string $targetingType Required. Identifies the type of assigned
   * targeting options to list. Supported targeting types include: *
   * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_APP` *
   * `TARGETING_TYPE_APP_CATEGORY` * `TARGETING_TYPE_AUDIENCE_GROUP` *
   * `TARGETING_TYPE_CATEGORY` * `TARGETING_TYPE_GENDER` *
   * `TARGETING_TYPE_HOUSEHOLD_INCOME` * `TARGETING_TYPE_KEYWORD` *
   * `TARGETING_TYPE_PARENTAL_STATUS` * `TARGETING_TYPE_SESSION_POSITION` *
   * `TARGETING_TYPE_URL` * `TARGETING_TYPE_YOUTUBE_CHANNEL` *
   * `TARGETING_TYPE_YOUTUBE_VIDEO`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Allows filtering by assigned targeting
   * option fields. Supported syntax: * Filter expressions are made up of one or
   * more restrictions. * Restrictions can be combined by the logical operator
   * `OR`. * A restriction has the form of `{field} {operator} {value}`. * All
   * fields must use the `EQUALS (=)` operator. Supported fields: *
   * `assignedTargetingOptionId` Examples: * `AssignedTargetingOption` resources
   * with ID 1 or 2: `assignedTargetingOptionId="1" OR
   * assignedTargetingOptionId="2"` The length of this field should be no more
   * than 500 characters. Reference our [filter `LIST` requests](/display-
   * video/api/guides/how-tos/filters) guide for more information.
   * @opt_param string orderBy Optional. Field by which to sort the list.
   * Acceptable values are: * `assignedTargetingOptionId` (default) The default
   * sorting order is ascending. To specify descending order for a field, a suffix
   * "desc" should be added to the field name. Example: `assignedTargetingOptionId
   * desc`.
   * @opt_param int pageSize Optional. Requested page size. Must be between `1`
   * and `5000`. If unspecified will default to `100`. Returns error code
   * `INVALID_ARGUMENT` if an invalid value is specified.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return. Typically, this is the value of next_page_token
   * returned from the previous call to `ListAdGroupAssignedTargetingOptions`
   * method. If not specified, the first page of results will be returned.
   * @return ListAdGroupAssignedTargetingOptionsResponse
   * @throws \Google\Service\Exception
   */
  public function listAdvertisersAdGroupsTargetingTypesAssignedTargetingOptions($advertiserId, $adGroupId, $targetingType, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'adGroupId' => $adGroupId, 'targetingType' => $targetingType];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAdGroupAssignedTargetingOptionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertisersAdGroupsTargetingTypesAssignedTargetingOptions::class, 'Google_Service_DisplayVideo_Resource_AdvertisersAdGroupsTargetingTypesAssignedTargetingOptions');
