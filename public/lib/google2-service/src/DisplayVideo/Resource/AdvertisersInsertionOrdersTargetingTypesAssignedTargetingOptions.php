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
use Google\Service\DisplayVideo\DisplayvideoEmpty;
use Google\Service\DisplayVideo\ListInsertionOrderAssignedTargetingOptionsResponse;

/**
 * The "assignedTargetingOptions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $assignedTargetingOptions = $displayvideoService->advertisers_insertionOrders_targetingTypes_assignedTargetingOptions;
 *  </code>
 */
class AdvertisersInsertionOrdersTargetingTypesAssignedTargetingOptions extends \Google\Service\Resource
{
  /**
   * Assigns a targeting option to an insertion order. Returns the assigned
   * targeting option if successful. Supported targeting types: *
   * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_BROWSER` *
   * `TARGETING_TYPE_CATEGORY` * `TARGETING_TYPE_CHANNEL` *
   * `TARGETING_TYPE_DEVICE_MAKE_MODEL` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_GENDER` *
   * `TARGETING_TYPE_KEYWORD` * `TARGETING_TYPE_LANGUAGE` *
   * `TARGETING_TYPE_NEGATIVE_KEYWORD_LIST` * `TARGETING_TYPE_OPERATING_SYSTEM` *
   * `TARGETING_TYPE_PARENTAL_STATUS` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` * `TARGETING_TYPE_VIEWABILITY`
   * (assignedTargetingOptions.create)
   *
   * @param string $advertiserId Required. The ID of the advertiser the insertion
   * order belongs to.
   * @param string $insertionOrderId Required. The ID of the insertion order the
   * assigned targeting option will belong to.
   * @param string $targetingType Required. Identifies the type of this assigned
   * targeting option. Supported targeting types: * `TARGETING_TYPE_AGE_RANGE` *
   * `TARGETING_TYPE_BROWSER` * `TARGETING_TYPE_CATEGORY` *
   * `TARGETING_TYPE_CHANNEL` * `TARGETING_TYPE_DEVICE_MAKE_MODEL` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_GENDER` *
   * `TARGETING_TYPE_KEYWORD` * `TARGETING_TYPE_LANGUAGE` *
   * `TARGETING_TYPE_NEGATIVE_KEYWORD_LIST` * `TARGETING_TYPE_OPERATING_SYSTEM` *
   * `TARGETING_TYPE_PARENTAL_STATUS` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` * `TARGETING_TYPE_VIEWABILITY`
   * @param AssignedTargetingOption $postBody
   * @param array $optParams Optional parameters.
   * @return AssignedTargetingOption
   * @throws \Google\Service\Exception
   */
  public function create($advertiserId, $insertionOrderId, $targetingType, AssignedTargetingOption $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'insertionOrderId' => $insertionOrderId, 'targetingType' => $targetingType, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], AssignedTargetingOption::class);
  }
  /**
   * Deletes an assigned targeting option from an insertion order. Supported
   * targeting types: * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_BROWSER` *
   * `TARGETING_TYPE_CATEGORY` * `TARGETING_TYPE_CHANNEL` *
   * `TARGETING_TYPE_DEVICE_MAKE_MODEL` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_GENDER` *
   * `TARGETING_TYPE_KEYWORD` * `TARGETING_TYPE_LANGUAGE` *
   * `TARGETING_TYPE_NEGATIVE_KEYWORD_LIST` * `TARGETING_TYPE_OPERATING_SYSTEM` *
   * `TARGETING_TYPE_PARENTAL_STATUS` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` * `TARGETING_TYPE_VIEWABILITY`
   * (assignedTargetingOptions.delete)
   *
   * @param string $advertiserId Required. The ID of the advertiser the insertion
   * order belongs to.
   * @param string $insertionOrderId Required. The ID of the insertion order the
   * assigned targeting option belongs to.
   * @param string $targetingType Required. Identifies the type of this assigned
   * targeting option. Supported targeting types: * `TARGETING_TYPE_AGE_RANGE` *
   * `TARGETING_TYPE_BROWSER` * `TARGETING_TYPE_CATEGORY` *
   * `TARGETING_TYPE_CHANNEL` * `TARGETING_TYPE_DEVICE_MAKE_MODEL` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_GENDER` *
   * `TARGETING_TYPE_KEYWORD` * `TARGETING_TYPE_LANGUAGE` *
   * `TARGETING_TYPE_NEGATIVE_KEYWORD_LIST` * `TARGETING_TYPE_OPERATING_SYSTEM` *
   * `TARGETING_TYPE_PARENTAL_STATUS` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` * `TARGETING_TYPE_VIEWABILITY`
   * @param string $assignedTargetingOptionId Required. The ID of the assigned
   * targeting option to delete.
   * @param array $optParams Optional parameters.
   * @return DisplayvideoEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($advertiserId, $insertionOrderId, $targetingType, $assignedTargetingOptionId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'insertionOrderId' => $insertionOrderId, 'targetingType' => $targetingType, 'assignedTargetingOptionId' => $assignedTargetingOptionId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DisplayvideoEmpty::class);
  }
  /**
   * Gets a single targeting option assigned to an insertion order.
   * (assignedTargetingOptions.get)
   *
   * @param string $advertiserId Required. The ID of the advertiser the insertion
   * order belongs to.
   * @param string $insertionOrderId Required. The ID of the insertion order the
   * assigned targeting option belongs to.
   * @param string $targetingType Required. Identifies the type of this assigned
   * targeting option. Supported targeting types include: *
   * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_APP` *
   * `TARGETING_TYPE_APP_CATEGORY` * `TARGETING_TYPE_AUDIENCE_GROUP` *
   * `TARGETING_TYPE_AUDIO_CONTENT_TYPE` *
   * `TARGETING_TYPE_AUTHORIZED_SELLER_STATUS` * `TARGETING_TYPE_BROWSER` *
   * `TARGETING_TYPE_BUSINESS_CHAIN` * `TARGETING_TYPE_CARRIER_AND_ISP` *
   * `TARGETING_TYPE_CATEGORY` * `TARGETING_TYPE_CHANNEL` *
   * `TARGETING_TYPE_CONTENT_DURATION` * `TARGETING_TYPE_CONTENT_GENRE` *
   * `TARGETING_TYPE_CONTENT_INSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_STREAM_TYPE` * `TARGETING_TYPE_DAY_AND_TIME` *
   * `TARGETING_TYPE_DEVICE_MAKE_MODEL` * `TARGETING_TYPE_DEVICE_TYPE` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_EXCHANGE` *
   * `TARGETING_TYPE_GENDER` * `TARGETING_TYPE_GEO_REGION` *
   * `TARGETING_TYPE_HOUSEHOLD_INCOME` * `TARGETING_TYPE_INVENTORY_SOURCE` *
   * `TARGETING_TYPE_INVENTORY_SOURCE_GROUP` * `TARGETING_TYPE_KEYWORD` *
   * `TARGETING_TYPE_LANGUAGE` * `TARGETING_TYPE_NATIVE_CONTENT_POSITION` *
   * `TARGETING_TYPE_NEGATIVE_KEYWORD_LIST` * `TARGETING_TYPE_OMID` *
   * `TARGETING_TYPE_ON_SCREEN_POSITION` * `TARGETING_TYPE_OPERATING_SYSTEM` *
   * `TARGETING_TYPE_PARENTAL_STATUS` * `TARGETING_TYPE_POI` *
   * `TARGETING_TYPE_PROXIMITY_LOCATION_LIST` *
   * `TARGETING_TYPE_REGIONAL_LOCATION_LIST` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` * `TARGETING_TYPE_SUB_EXCHANGE`
   * * `TARGETING_TYPE_THIRD_PARTY_VERIFIER` * `TARGETING_TYPE_URL` *
   * `TARGETING_TYPE_USER_REWARDED_CONTENT` * `TARGETING_TYPE_VIDEO_PLAYER_SIZE` *
   * `TARGETING_TYPE_VIEWABILITY`
   * @param string $assignedTargetingOptionId Required. An identifier unique to
   * the targeting type in this insertion order that identifies the assigned
   * targeting option being requested.
   * @param array $optParams Optional parameters.
   * @return AssignedTargetingOption
   * @throws \Google\Service\Exception
   */
  public function get($advertiserId, $insertionOrderId, $targetingType, $assignedTargetingOptionId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'insertionOrderId' => $insertionOrderId, 'targetingType' => $targetingType, 'assignedTargetingOptionId' => $assignedTargetingOptionId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AssignedTargetingOption::class);
  }
  /**
   * Lists the targeting options assigned to an insertion order. (assignedTargetin
   * gOptions.listAdvertisersInsertionOrdersTargetingTypesAssignedTargetingOptions
   * )
   *
   * @param string $advertiserId Required. The ID of the advertiser the insertion
   * order belongs to.
   * @param string $insertionOrderId Required. The ID of the insertion order to
   * list assigned targeting options for.
   * @param string $targetingType Required. Identifies the type of assigned
   * targeting options to list. Supported targeting types include: *
   * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_APP` *
   * `TARGETING_TYPE_APP_CATEGORY` * `TARGETING_TYPE_AUDIENCE_GROUP` *
   * `TARGETING_TYPE_AUDIO_CONTENT_TYPE` *
   * `TARGETING_TYPE_AUTHORIZED_SELLER_STATUS` * `TARGETING_TYPE_BROWSER` *
   * `TARGETING_TYPE_BUSINESS_CHAIN` * `TARGETING_TYPE_CARRIER_AND_ISP` *
   * `TARGETING_TYPE_CATEGORY` * `TARGETING_TYPE_CHANNEL` *
   * `TARGETING_TYPE_CONTENT_DURATION` * `TARGETING_TYPE_CONTENT_GENRE` *
   * `TARGETING_TYPE_CONTENT_INSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_STREAM_TYPE` * `TARGETING_TYPE_DAY_AND_TIME` *
   * `TARGETING_TYPE_DEVICE_MAKE_MODEL` * `TARGETING_TYPE_DEVICE_TYPE` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_EXCHANGE` *
   * `TARGETING_TYPE_GENDER` * `TARGETING_TYPE_GEO_REGION` *
   * `TARGETING_TYPE_HOUSEHOLD_INCOME` * `TARGETING_TYPE_INVENTORY_SOURCE` *
   * `TARGETING_TYPE_INVENTORY_SOURCE_GROUP` * `TARGETING_TYPE_KEYWORD` *
   * `TARGETING_TYPE_LANGUAGE` * `TARGETING_TYPE_NATIVE_CONTENT_POSITION` *
   * `TARGETING_TYPE_NEGATIVE_KEYWORD_LIST` * `TARGETING_TYPE_OMID` *
   * `TARGETING_TYPE_ON_SCREEN_POSITION` * `TARGETING_TYPE_OPERATING_SYSTEM` *
   * `TARGETING_TYPE_PARENTAL_STATUS` * `TARGETING_TYPE_POI` *
   * `TARGETING_TYPE_PROXIMITY_LOCATION_LIST` *
   * `TARGETING_TYPE_REGIONAL_LOCATION_LIST` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` * `TARGETING_TYPE_SUB_EXCHANGE`
   * * `TARGETING_TYPE_THIRD_PARTY_VERIFIER` * `TARGETING_TYPE_URL` *
   * `TARGETING_TYPE_USER_REWARDED_CONTENT` * `TARGETING_TYPE_VIDEO_PLAYER_SIZE` *
   * `TARGETING_TYPE_VIEWABILITY`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Allows filtering by assigned targeting option
   * fields. Supported syntax: * Filter expressions are made up of one or more
   * restrictions. * Restrictions can be combined by the logical operator `OR`. *
   * A restriction has the form of `{field} {operator} {value}`. * All fields must
   * use the `EQUALS (=)` operator. Supported fields: *
   * `assignedTargetingOptionId` * `inheritance` Examples: *
   * `AssignedTargetingOption` resources with ID 1 or 2:
   * `assignedTargetingOptionId="1" OR assignedTargetingOptionId="2"` *
   * `AssignedTargetingOption` resources with inheritance status of
   * `NOT_INHERITED` or `INHERITED_FROM_PARTNER`: `inheritance="NOT_INHERITED" OR
   * inheritance="INHERITED_FROM_PARTNER"` The length of this field should be no
   * more than 500 characters. Reference our [filter `LIST` requests](/display-
   * video/api/guides/how-tos/filters) guide for more information.
   * @opt_param string orderBy Field by which to sort the list. Acceptable values
   * are: * `assignedTargetingOptionId` (default) The default sorting order is
   * ascending. To specify descending order for a field, a suffix "desc" should be
   * added to the field name. Example: `assignedTargetingOptionId desc`.
   * @opt_param int pageSize Requested page size. Must be between `1` and `5000`.
   * If unspecified will default to `100`. Returns error code `INVALID_ARGUMENT`
   * if an invalid value is specified.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return. Typically, this is the value of next_page_token returned from
   * the previous call to `ListInsertionOrderAssignedTargetingOptions` method. If
   * not specified, the first page of results will be returned.
   * @return ListInsertionOrderAssignedTargetingOptionsResponse
   * @throws \Google\Service\Exception
   */
  public function listAdvertisersInsertionOrdersTargetingTypesAssignedTargetingOptions($advertiserId, $insertionOrderId, $targetingType, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'insertionOrderId' => $insertionOrderId, 'targetingType' => $targetingType];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInsertionOrderAssignedTargetingOptionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertisersInsertionOrdersTargetingTypesAssignedTargetingOptions::class, 'Google_Service_DisplayVideo_Resource_AdvertisersInsertionOrdersTargetingTypesAssignedTargetingOptions');
