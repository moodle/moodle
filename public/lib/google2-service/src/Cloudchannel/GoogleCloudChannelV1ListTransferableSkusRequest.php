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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1ListTransferableSkusRequest extends \Google\Model
{
  /**
   * Optional. The super admin of the resold customer generates this token to
   * authorize a reseller to access their Cloud Identity and purchase
   * entitlements on their behalf. You can omit this token after authorization.
   * See https://support.google.com/a/answer/7643790 for more details.
   *
   * @var string
   */
  public $authToken;
  /**
   * Customer's Cloud Identity ID
   *
   * @var string
   */
  public $cloudIdentityId;
  /**
   * A reseller is required to create a customer and use the resource name of
   * the created customer here. Customer_name uses the format:
   * accounts/{account_id}/customers/{customer_id}
   *
   * @var string
   */
  public $customerName;
  /**
   * The BCP-47 language code. For example, "en-US". The response will localize
   * in the corresponding language code, if specified. The default value is "en-
   * US". Optional.
   *
   * @var string
   */
  public $languageCode;
  /**
   * The requested page size. Server might return fewer results than requested.
   * If unspecified, returns at most 100 SKUs. The maximum value is 1000; the
   * server will coerce values above 1000. Optional.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A token for a page of results other than the first page. Obtained using
   * ListTransferableSkusResponse.next_page_token of the previous
   * CloudChannelService.ListTransferableSkus call. Optional.
   *
   * @var string
   */
  public $pageToken;

  /**
   * Optional. The super admin of the resold customer generates this token to
   * authorize a reseller to access their Cloud Identity and purchase
   * entitlements on their behalf. You can omit this token after authorization.
   * See https://support.google.com/a/answer/7643790 for more details.
   *
   * @param string $authToken
   */
  public function setAuthToken($authToken)
  {
    $this->authToken = $authToken;
  }
  /**
   * @return string
   */
  public function getAuthToken()
  {
    return $this->authToken;
  }
  /**
   * Customer's Cloud Identity ID
   *
   * @param string $cloudIdentityId
   */
  public function setCloudIdentityId($cloudIdentityId)
  {
    $this->cloudIdentityId = $cloudIdentityId;
  }
  /**
   * @return string
   */
  public function getCloudIdentityId()
  {
    return $this->cloudIdentityId;
  }
  /**
   * A reseller is required to create a customer and use the resource name of
   * the created customer here. Customer_name uses the format:
   * accounts/{account_id}/customers/{customer_id}
   *
   * @param string $customerName
   */
  public function setCustomerName($customerName)
  {
    $this->customerName = $customerName;
  }
  /**
   * @return string
   */
  public function getCustomerName()
  {
    return $this->customerName;
  }
  /**
   * The BCP-47 language code. For example, "en-US". The response will localize
   * in the corresponding language code, if specified. The default value is "en-
   * US". Optional.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The requested page size. Server might return fewer results than requested.
   * If unspecified, returns at most 100 SKUs. The maximum value is 1000; the
   * server will coerce values above 1000. Optional.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * A token for a page of results other than the first page. Obtained using
   * ListTransferableSkusResponse.next_page_token of the previous
   * CloudChannelService.ListTransferableSkus call. Optional.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1ListTransferableSkusRequest::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1ListTransferableSkusRequest');
