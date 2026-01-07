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

class GoogleCloudChannelV1ListTransferableOffersRequest extends \Google\Model
{
  /**
   * Optional. The Billing Account to look up Offers for. Format:
   * accounts/{account_id}/billingAccounts/{billing_account_id}. This field is
   * only relevant for multi-currency accounts. It should be left empty for
   * single currency accounts.
   *
   * @var string
   */
  public $billingAccount;
  /**
   * Customer's Cloud Identity ID
   *
   * @var string
   */
  public $cloudIdentityId;
  /**
   * A reseller should create a customer and use the resource name of that
   * customer here.
   *
   * @var string
   */
  public $customerName;
  /**
   * Optional. The BCP-47 language code. For example, "en-US". The response will
   * localize in the corresponding language code, if specified. The default
   * value is "en-US".
   *
   * @var string
   */
  public $languageCode;
  /**
   * Requested page size. Server might return fewer results than requested. If
   * unspecified, returns at most 100 offers. The maximum value is 1000; the
   * server will coerce values above 1000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A token for a page of results other than the first page. Obtained using
   * ListTransferableOffersResponse.next_page_token of the previous
   * CloudChannelService.ListTransferableOffers call.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. The SKU to look up Offers for.
   *
   * @var string
   */
  public $sku;

  /**
   * Optional. The Billing Account to look up Offers for. Format:
   * accounts/{account_id}/billingAccounts/{billing_account_id}. This field is
   * only relevant for multi-currency accounts. It should be left empty for
   * single currency accounts.
   *
   * @param string $billingAccount
   */
  public function setBillingAccount($billingAccount)
  {
    $this->billingAccount = $billingAccount;
  }
  /**
   * @return string
   */
  public function getBillingAccount()
  {
    return $this->billingAccount;
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
   * A reseller should create a customer and use the resource name of that
   * customer here.
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
   * Optional. The BCP-47 language code. For example, "en-US". The response will
   * localize in the corresponding language code, if specified. The default
   * value is "en-US".
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
   * Requested page size. Server might return fewer results than requested. If
   * unspecified, returns at most 100 offers. The maximum value is 1000; the
   * server will coerce values above 1000.
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
   * ListTransferableOffersResponse.next_page_token of the previous
   * CloudChannelService.ListTransferableOffers call.
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
  /**
   * Required. The SKU to look up Offers for.
   *
   * @param string $sku
   */
  public function setSku($sku)
  {
    $this->sku = $sku;
  }
  /**
   * @return string
   */
  public function getSku()
  {
    return $this->sku;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1ListTransferableOffersRequest::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1ListTransferableOffersRequest');
