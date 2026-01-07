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

use Google\Service\DisplayVideo\BulkListAdGroupAssignedTargetingOptionsResponse;
use Google\Service\DisplayVideo\ListYoutubeAdGroupsResponse;
use Google\Service\DisplayVideo\YoutubeAdGroup;

/**
 * The "youtubeAdGroups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $youtubeAdGroups = $displayvideoService->advertisers_youtubeAdGroups;
 *  </code>
 */
class AdvertisersYoutubeAdGroups extends \Google\Service\Resource
{
  /**
   * Lists assigned targeting options for multiple YouTube ad groups across
   * targeting types. Inherieted assigned targeting options are not included.
   * (youtubeAdGroups.bulkListAdGroupAssignedTargetingOptions)
   *
   * @param string $advertiserId Required. The ID of the advertiser the line items
   * belongs to.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Allows filtering by assigned targeting
   * option fields. Supported syntax: * Filter expressions are made up of one or
   * more restrictions. * Restrictions can be combined by the logical operator
   * `OR`. * A restriction has the form of `{field} {operator} {value}`. * All
   * fields must use the `EQUALS (=)` operator. Supported fields: *
   * `targetingType` Examples: * `AssignedTargetingOption` resources of targeting
   * type `TARGETING_TYPE_YOUTUBE_VIDEO` or `TARGETING_TYPE_YOUTUBE_CHANNEL`:
   * `targetingType="TARGETING_TYPE_YOUTUBE_VIDEO" OR
   * targetingType="TARGETING_TYPE_YOUTUBE_CHANNEL"` The length of this field
   * should be no more than 500 characters. Reference our [filter `LIST`
   * requests](/display-video/api/guides/how-tos/filters) guide for more
   * information.
   * @opt_param string orderBy Optional. Field by which to sort the list.
   * Acceptable values are: * `youtubeAdGroupId` (acceptable in v2) * `adGroupId`
   * (acceptable in v3) * `assignedTargetingOption.targetingType` The default
   * sorting order is ascending. To specify descending order for a field, a suffix
   * "desc" should be added to the field name. Example: `targetingType desc`.
   * @opt_param int pageSize Optional. Requested page size. The size must be an
   * integer between `1` and `5000`. If unspecified, the default is `5000`.
   * Returns error code `INVALID_ARGUMENT` if an invalid value is specified.
   * @opt_param string pageToken Optional. A token that lets the client fetch the
   * next page of results. Typically, this is the value of next_page_token
   * returned from the previous call to the
   * `BulkListAdGroupAssignedTargetingOptions` method. If not specified, the first
   * page of results will be returned.
   * @opt_param string youtubeAdGroupIds Required. The IDs of the youtube ad
   * groups to list assigned targeting options for.
   * @return BulkListAdGroupAssignedTargetingOptionsResponse
   */
  public function bulkListAdGroupAssignedTargetingOptions($advertiserId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId];
    $params = array_merge($params, $optParams);
    return $this->call('bulkListAdGroupAssignedTargetingOptions', [$params], BulkListAdGroupAssignedTargetingOptionsResponse::class);
  }
  /**
   * Gets a YouTube ad group. (youtubeAdGroups.get)
   *
   * @param string $advertiserId Required. The ID of the advertiser this ad group
   * belongs to.
   * @param string $youtubeAdGroupId Required. The ID of the ad group to fetch.
   * @param array $optParams Optional parameters.
   * @return YoutubeAdGroup
   */
  public function get($advertiserId, $youtubeAdGroupId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'youtubeAdGroupId' => $youtubeAdGroupId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], YoutubeAdGroup::class);
  }
  /**
   * Lists YouTube ad groups. (youtubeAdGroups.listAdvertisersYoutubeAdGroups)
   *
   * @param string $advertiserId Required. The ID of the advertiser the ad groups
   * belongs to.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Allows filtering by custom YouTube ad group fields.
   * Supported syntax: * Filter expressions are made up of one or more
   * restrictions. * Restrictions can be combined by `AND` and `OR`. A sequence of
   * restrictions implicitly uses `AND`. * A restriction has the form of `{field}
   * {operator} {value}`. * All fields must use the `EQUALS (=)` operator.
   * Supported properties: * `adGroupId` * `displayName` * `entityStatus` *
   * `lineItemId` * `adGroupFormat` Examples: * All ad groups under an line item:
   * `lineItemId="1234"` * All `ENTITY_STATUS_ACTIVE` or `ENTITY_STATUS_PAUSED`
   * `YOUTUBE_AND_PARTNERS_AD_GROUP_FORMAT_IN_STREAM` ad groups under an
   * advertiser: `(entityStatus="ENTITY_STATUS_ACTIVE" OR
   * entityStatus="ENTITY_STATUS_PAUSED") AND
   * adGroupFormat="YOUTUBE_AND_PARTNERS_AD_GROUP_FORMAT_IN_STREAM"` The length of
   * this field should be no more than 500 characters. Reference our [filter
   * `LIST` requests](/display-video/api/guides/how-tos/filters) guide for more
   * information.
   * @opt_param string orderBy Field by which to sort the list. Acceptable values
   * are: * `displayName` (default) * `entityStatus` The default sorting order is
   * ascending. To specify descending order for a field, a suffix "desc" should be
   * added to the field name. Example: `displayName desc`.
   * @opt_param int pageSize Requested page size. Must be between `1` and `200`.
   * If unspecified will default to `100`. Returns error code `INVALID_ARGUMENT`
   * if an invalid value is specified.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return. Typically, this is the value of next_page_token returned from
   * the previous call to `ListYoutubeAdGroups` method. If not specified, the
   * first page of results will be returned.
   * @return ListYoutubeAdGroupsResponse
   */
  public function listAdvertisersYoutubeAdGroups($advertiserId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListYoutubeAdGroupsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertisersYoutubeAdGroups::class, 'Google_Service_DisplayVideo_Resource_AdvertisersYoutubeAdGroups');
