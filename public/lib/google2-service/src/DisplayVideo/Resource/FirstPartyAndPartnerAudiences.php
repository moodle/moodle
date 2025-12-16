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

use Google\Service\DisplayVideo\EditCustomerMatchMembersRequest;
use Google\Service\DisplayVideo\EditCustomerMatchMembersResponse;
use Google\Service\DisplayVideo\FirstPartyAndPartnerAudience;
use Google\Service\DisplayVideo\ListFirstPartyAndPartnerAudiencesResponse;

/**
 * The "firstPartyAndPartnerAudiences" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $firstPartyAndPartnerAudiences = $displayvideoService->firstPartyAndPartnerAudiences;
 *  </code>
 */
class FirstPartyAndPartnerAudiences extends \Google\Service\Resource
{
  /**
   * Creates a FirstPartyAndPartnerAudience. Only supported for the following
   * audience_type: * `CUSTOMER_MATCH_CONTACT_INFO` * `CUSTOMER_MATCH_DEVICE_ID`
   * (firstPartyAndPartnerAudiences.create)
   *
   * @param FirstPartyAndPartnerAudience $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string advertiserId Required. The ID of the advertiser under whom
   * the FirstPartyAndPartnerAudience will be created.
   * @return FirstPartyAndPartnerAudience
   * @throws \Google\Service\Exception
   */
  public function create(FirstPartyAndPartnerAudience $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], FirstPartyAndPartnerAudience::class);
  }
  /**
   * Updates the member list of a Customer Match audience. Only supported for the
   * following audience_type: * `CUSTOMER_MATCH_CONTACT_INFO` *
   * `CUSTOMER_MATCH_DEVICE_ID`
   * (firstPartyAndPartnerAudiences.editCustomerMatchMembers)
   *
   * @param string $firstPartyAndPartnerAudienceId Required. The ID of the
   * Customer Match FirstPartyAndPartnerAudience whose members will be edited.
   * @param EditCustomerMatchMembersRequest $postBody
   * @param array $optParams Optional parameters.
   * @return EditCustomerMatchMembersResponse
   * @throws \Google\Service\Exception
   */
  public function editCustomerMatchMembers($firstPartyAndPartnerAudienceId, EditCustomerMatchMembersRequest $postBody, $optParams = [])
  {
    $params = ['firstPartyAndPartnerAudienceId' => $firstPartyAndPartnerAudienceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('editCustomerMatchMembers', [$params], EditCustomerMatchMembersResponse::class);
  }
  /**
   * Gets a first party or partner audience. (firstPartyAndPartnerAudiences.get)
   *
   * @param string $firstPartyAndPartnerAudienceId Required. The ID of the first
   * party and partner audience to fetch.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string advertiserId The ID of the advertiser that has access to
   * the fetched first party and partner audience.
   * @opt_param string partnerId The ID of the partner that has access to the
   * fetched first party and partner audience.
   * @return FirstPartyAndPartnerAudience
   * @throws \Google\Service\Exception
   */
  public function get($firstPartyAndPartnerAudienceId, $optParams = [])
  {
    $params = ['firstPartyAndPartnerAudienceId' => $firstPartyAndPartnerAudienceId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], FirstPartyAndPartnerAudience::class);
  }
  /**
   * Lists first party and partner audiences. The order is defined by the order_by
   * parameter. (firstPartyAndPartnerAudiences.listFirstPartyAndPartnerAudiences)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string advertiserId The ID of the advertiser that has access to
   * the fetched first party and partner audiences.
   * @opt_param string filter Optional. Allows filtering by first party and
   * partner audience fields. Supported syntax: * Filter expressions for first
   * party and partner audiences can only contain at most one restriction. * A
   * restriction has the form of `{field} {operator} {value}`. * All fields must
   * use the `HAS (:)` operator. Supported fields: * `displayName` Examples: * All
   * first party and partner audiences for which the display name contains
   * "Google": `displayName:"Google"`. The length of this field should be no more
   * than 500 characters. Reference our [filter `LIST` requests](/display-
   * video/api/guides/how-tos/filters) guide for more information.
   * @opt_param string orderBy Optional. Field by which to sort the list.
   * Acceptable values are: * `FirstPartyAndPartnerAudienceId` (default) *
   * `displayName` The default sorting order is ascending. To specify descending
   * order for a field, a suffix "desc" should be added to the field name.
   * Example: `displayName desc`.
   * @opt_param int pageSize Optional. Requested page size. Must be between `1`
   * and `5000`. If unspecified, this value defaults to `5000`. Returns error code
   * `INVALID_ARGUMENT` if an invalid value is specified.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return. Typically, this is the value of next_page_token
   * returned from the previous call to `ListFirstPartyAndPartnerAudiences`
   * method. If not specified, the first page of results will be returned.
   * @opt_param string partnerId The ID of the partner that has access to the
   * fetched first party and partner audiences.
   * @return ListFirstPartyAndPartnerAudiencesResponse
   * @throws \Google\Service\Exception
   */
  public function listFirstPartyAndPartnerAudiences($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListFirstPartyAndPartnerAudiencesResponse::class);
  }
  /**
   * Updates an existing FirstPartyAndPartnerAudience. Only supported for the
   * following audience_type: * `CUSTOMER_MATCH_CONTACT_INFO` *
   * `CUSTOMER_MATCH_DEVICE_ID` (firstPartyAndPartnerAudiences.patch)
   *
   * @param string $firstPartyAndPartnerAudienceId Identifier. The unique ID of
   * the first party and partner audience. Assigned by the system.
   * @param FirstPartyAndPartnerAudience $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string advertiserId Required. The ID of the owner advertiser of
   * the updated FirstPartyAndPartnerAudience.
   * @opt_param string updateMask Required. The mask to control which fields to
   * update. Updates are only supported for the following fields: * `displayName`
   * * `description` * `membershipDurationDays`
   * @return FirstPartyAndPartnerAudience
   * @throws \Google\Service\Exception
   */
  public function patch($firstPartyAndPartnerAudienceId, FirstPartyAndPartnerAudience $postBody, $optParams = [])
  {
    $params = ['firstPartyAndPartnerAudienceId' => $firstPartyAndPartnerAudienceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], FirstPartyAndPartnerAudience::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirstPartyAndPartnerAudiences::class, 'Google_Service_DisplayVideo_Resource_FirstPartyAndPartnerAudiences');
