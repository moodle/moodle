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

use Google\Service\DisplayVideo\BulkEditAssignedTargetingOptionsRequest;
use Google\Service\DisplayVideo\BulkEditAssignedTargetingOptionsResponse;
use Google\Service\DisplayVideo\BulkListAssignedTargetingOptionsResponse;
use Google\Service\DisplayVideo\BulkUpdateLineItemsRequest;
use Google\Service\DisplayVideo\BulkUpdateLineItemsResponse;
use Google\Service\DisplayVideo\DisplayvideoEmpty;
use Google\Service\DisplayVideo\DuplicateLineItemRequest;
use Google\Service\DisplayVideo\DuplicateLineItemResponse;
use Google\Service\DisplayVideo\GenerateDefaultLineItemRequest;
use Google\Service\DisplayVideo\LineItem;
use Google\Service\DisplayVideo\ListLineItemsResponse;

/**
 * The "lineItems" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $lineItems = $displayvideoService->advertisers_lineItems;
 *  </code>
 */
class AdvertisersLineItems extends \Google\Service\Resource
{
  /**
   * Bulk edits targeting options under multiple line items. The operation will
   * delete the assigned targeting options provided in
   * BulkEditAssignedTargetingOptionsRequest.delete_requests and then create the
   * assigned targeting options provided in
   * BulkEditAssignedTargetingOptionsRequest.create_requests. Requests to this
   * endpoint cannot be made concurrently with the following requests updating the
   * same line item: * lineItems.bulkUpdate * lineItems.patch *
   * assignedTargetingOptions.create * assignedTargetingOptions.delete YouTube &
   * Partners line items cannot be created or updated using the API.
   * (lineItems.bulkEditAssignedTargetingOptions)
   *
   * @param string $advertiserId Required. The ID of the advertiser the line items
   * belong to.
   * @param BulkEditAssignedTargetingOptionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BulkEditAssignedTargetingOptionsResponse
   * @throws \Google\Service\Exception
   */
  public function bulkEditAssignedTargetingOptions($advertiserId, BulkEditAssignedTargetingOptionsRequest $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('bulkEditAssignedTargetingOptions', [$params], BulkEditAssignedTargetingOptionsResponse::class);
  }
  /**
   * Lists assigned targeting options for multiple line items across targeting
   * types. (lineItems.bulkListAssignedTargetingOptions)
   *
   * @param string $advertiserId Required. The ID of the advertiser the line items
   * belongs to.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Allows filtering by assigned targeting option
   * fields. Supported syntax: * Filter expressions are made up of one or more
   * restrictions. * Restrictions can be combined by the logical operator `OR` on
   * the same field. * A restriction has the form of `{field} {operator} {value}`.
   * * All fields must use the `EQUALS (=)` operator. Supported fields: *
   * `targetingType` * `inheritance` Examples: * `AssignedTargetingOption`
   * resources of targeting type `TARGETING_TYPE_PROXIMITY_LOCATION_LIST` or
   * `TARGETING_TYPE_CHANNEL`:
   * `targetingType="TARGETING_TYPE_PROXIMITY_LOCATION_LIST" OR
   * targetingType="TARGETING_TYPE_CHANNEL"` * `AssignedTargetingOption` resources
   * with inheritance status of `NOT_INHERITED` or `INHERITED_FROM_PARTNER`:
   * `inheritance="NOT_INHERITED" OR inheritance="INHERITED_FROM_PARTNER"` The
   * length of this field should be no more than 500 characters. Reference our
   * [filter `LIST` requests](/display-video/api/guides/how-tos/filters) guide for
   * more information.
   * @opt_param string lineItemIds Required. The IDs of the line items to list
   * assigned targeting options for.
   * @opt_param string orderBy Field by which to sort the list. Acceptable values
   * are: * `lineItemId` (default) * `assignedTargetingOption.targetingType` The
   * default sorting order is ascending. To specify descending order for a field,
   * a suffix "desc" should be added to the field name. Example: `targetingType
   * desc`.
   * @opt_param int pageSize Requested page size. The size must be an integer
   * between `1` and `5000`. If unspecified, the default is `5000`. Returns error
   * code `INVALID_ARGUMENT` if an invalid value is specified.
   * @opt_param string pageToken A token that lets the client fetch the next page
   * of results. Typically, this is the value of next_page_token returned from the
   * previous call to the `BulkListAssignedTargetingOptions` method. If not
   * specified, the first page of results will be returned.
   * @return BulkListAssignedTargetingOptionsResponse
   * @throws \Google\Service\Exception
   */
  public function bulkListAssignedTargetingOptions($advertiserId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId];
    $params = array_merge($params, $optParams);
    return $this->call('bulkListAssignedTargetingOptions', [$params], BulkListAssignedTargetingOptionsResponse::class);
  }
  /**
   * Updates multiple line items. Requests to this endpoint cannot be made
   * concurrently with the following requests updating the same line item: *
   * BulkEditAssignedTargetingOptions * UpdateLineItem *
   * assignedTargetingOptions.create * assignedTargetingOptions.delete YouTube &
   * Partners line items cannot be created or updated using the API.
   * (lineItems.bulkUpdate)
   *
   * @param string $advertiserId Required. The ID of the advertiser this line item
   * belongs to.
   * @param BulkUpdateLineItemsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BulkUpdateLineItemsResponse
   * @throws \Google\Service\Exception
   */
  public function bulkUpdate($advertiserId, BulkUpdateLineItemsRequest $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('bulkUpdate', [$params], BulkUpdateLineItemsResponse::class);
  }
  /**
   * Creates a new line item. Returns the newly created line item if successful.
   * YouTube & Partners line items cannot be created or updated using the API.
   * (lineItems.create)
   *
   * @param string $advertiserId Output only. The unique ID of the advertiser the
   * line item belongs to.
   * @param LineItem $postBody
   * @param array $optParams Optional parameters.
   * @return LineItem
   * @throws \Google\Service\Exception
   */
  public function create($advertiserId, LineItem $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], LineItem::class);
  }
  /**
   * Deletes a line item. Returns error code `NOT_FOUND` if the line item does not
   * exist. The line item should be archived first, i.e. set entity_status to
   * `ENTITY_STATUS_ARCHIVED`, to be able to delete it. YouTube & Partners line
   * items cannot be created or updated using the API. (lineItems.delete)
   *
   * @param string $advertiserId The ID of the advertiser this line item belongs
   * to.
   * @param string $lineItemId The ID of the line item to delete.
   * @param array $optParams Optional parameters.
   * @return DisplayvideoEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($advertiserId, $lineItemId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'lineItemId' => $lineItemId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DisplayvideoEmpty::class);
  }
  /**
   * Duplicates a line item. Returns the ID of the created line item if
   * successful. YouTube & Partners line items cannot be created or updated using
   * the API. **This method regularly experiences high latency.** We recommend
   * [increasing your default timeout](/display-video/api/guides/best-
   * practices/timeouts#client_library_timeout) to avoid errors.
   * (lineItems.duplicate)
   *
   * @param string $advertiserId Required. The ID of the advertiser this line item
   * belongs to.
   * @param string $lineItemId Required. The ID of the line item to duplicate.
   * @param DuplicateLineItemRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DuplicateLineItemResponse
   * @throws \Google\Service\Exception
   */
  public function duplicate($advertiserId, $lineItemId, DuplicateLineItemRequest $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'lineItemId' => $lineItemId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('duplicate', [$params], DuplicateLineItemResponse::class);
  }
  /**
   * Creates a new line item with settings (including targeting) inherited from
   * the insertion order and an `ENTITY_STATUS_DRAFT` entity_status. Returns the
   * newly created line item if successful. There are default values based on the
   * three fields: * The insertion order's insertion_order_type * The insertion
   * order's automation_type * The given line_item_type YouTube & Partners line
   * items cannot be created or updated using the API. (lineItems.generateDefault)
   *
   * @param string $advertiserId Required. The ID of the advertiser this line item
   * belongs to.
   * @param GenerateDefaultLineItemRequest $postBody
   * @param array $optParams Optional parameters.
   * @return LineItem
   * @throws \Google\Service\Exception
   */
  public function generateDefault($advertiserId, GenerateDefaultLineItemRequest $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateDefault', [$params], LineItem::class);
  }
  /**
   * Gets a line item. (lineItems.get)
   *
   * @param string $advertiserId Required. The ID of the advertiser this line item
   * belongs to.
   * @param string $lineItemId Required. The ID of the line item to fetch.
   * @param array $optParams Optional parameters.
   * @return LineItem
   * @throws \Google\Service\Exception
   */
  public function get($advertiserId, $lineItemId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'lineItemId' => $lineItemId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], LineItem::class);
  }
  /**
   * Lists line items in an advertiser. The order is defined by the order_by
   * parameter. If a filter by entity_status is not specified, line items with
   * `ENTITY_STATUS_ARCHIVED` will not be included in the results.
   * (lineItems.listAdvertisersLineItems)
   *
   * @param string $advertiserId Required. The ID of the advertiser to list line
   * items for.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Allows filtering by line item fields. Supported
   * syntax: * Filter expressions are made up of one or more restrictions. *
   * Restrictions can be combined by `AND` or `OR` logical operators. A sequence
   * of restrictions implicitly uses `AND`. * A restriction has the form of
   * `{field} {operator} {value}`. * The `updateTime` field must use the `GREATER
   * THAN OR EQUAL TO (>=)` or `LESS THAN OR EQUAL TO (<=)` operators. * All other
   * fields must use the `EQUALS (=)` operator. Supported fields: * `campaignId` *
   * `displayName` * `entityStatus` * `insertionOrderId` * `lineItemId` *
   * `lineItemType` * `updateTime` (input in ISO 8601 format, or `YYYY-MM-
   * DDTHH:MM:SSZ`) Examples: * All line items under an insertion order:
   * `insertionOrderId="1234"` * All `ENTITY_STATUS_ACTIVE` or
   * `ENTITY_STATUS_PAUSED` and `LINE_ITEM_TYPE_DISPLAY_DEFAULT` line items under
   * an advertiser: `(entityStatus="ENTITY_STATUS_ACTIVE" OR
   * entityStatus="ENTITY_STATUS_PAUSED") AND
   * lineItemType="LINE_ITEM_TYPE_DISPLAY_DEFAULT"` * All line items with an
   * update time less than or equal to 2020-11-04T18:54:47Z (format of ISO 8601):
   * `updateTime<="2020-11-04T18:54:47Z"` * All line items with an update time
   * greater than or equal to 2020-11-04T18:54:47Z (format of ISO 8601):
   * `updateTime>="2020-11-04T18:54:47Z"` The length of this field should be no
   * more than 500 characters. Reference our [filter `LIST` requests](/display-
   * video/api/guides/how-tos/filters) guide for more information.
   * @opt_param string orderBy Field by which to sort the list. Acceptable values
   * are: * `displayName` (default) * `entityStatus` * `updateTime` The default
   * sorting order is ascending. To specify descending order for a field, a suffix
   * "desc" should be added to the field name. Example: `displayName desc`.
   * @opt_param int pageSize Requested page size. Must be between `1` and `200`.
   * If unspecified will default to `100`. Returns error code `INVALID_ARGUMENT`
   * if an invalid value is specified.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return. Typically, this is the value of next_page_token returned from
   * the previous call to `ListLineItems` method. If not specified, the first page
   * of results will be returned.
   * @return ListLineItemsResponse
   * @throws \Google\Service\Exception
   */
  public function listAdvertisersLineItems($advertiserId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListLineItemsResponse::class);
  }
  /**
   * Updates an existing line item. Returns the updated line item if successful.
   * Requests to this endpoint cannot be made concurrently with the following
   * requests updating the same line item: * BulkEditAssignedTargetingOptions *
   * BulkUpdateLineItems * assignedTargetingOptions.create *
   * assignedTargetingOptions.delete YouTube & Partners line items cannot be
   * created or updated using the API. **This method regularly experiences high
   * latency.** We recommend [increasing your default timeout](/display-
   * video/api/guides/best-practices/timeouts#client_library_timeout) to avoid
   * errors. (lineItems.patch)
   *
   * @param string $advertiserId Output only. The unique ID of the advertiser the
   * line item belongs to.
   * @param string $lineItemId Output only. The unique ID of the line item.
   * Assigned by the system.
   * @param LineItem $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The mask to control which fields to
   * update.
   * @return LineItem
   * @throws \Google\Service\Exception
   */
  public function patch($advertiserId, $lineItemId, LineItem $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'lineItemId' => $lineItemId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], LineItem::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertisersLineItems::class, 'Google_Service_DisplayVideo_Resource_AdvertisersLineItems');
