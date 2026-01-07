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

namespace Google\Service\YouTube;

class ChannelToStoreLinkDetails extends \Google\Model
{
  protected $billingDetailsType = ChannelToStoreLinkDetailsBillingDetails::class;
  protected $billingDetailsDataType = '';
  protected $merchantAffiliateProgramDetailsType = ChannelToStoreLinkDetailsMerchantAffiliateProgramDetails::class;
  protected $merchantAffiliateProgramDetailsDataType = '';
  /**
   * Google Merchant Center id of the store.
   *
   * @var string
   */
  public $merchantId;
  /**
   * Name of the store.
   *
   * @var string
   */
  public $storeName;
  /**
   * Landing page of the store.
   *
   * @var string
   */
  public $storeUrl;

  /**
   * Information specific to billing (read-only).
   *
   * @param ChannelToStoreLinkDetailsBillingDetails $billingDetails
   */
  public function setBillingDetails(ChannelToStoreLinkDetailsBillingDetails $billingDetails)
  {
    $this->billingDetails = $billingDetails;
  }
  /**
   * @return ChannelToStoreLinkDetailsBillingDetails
   */
  public function getBillingDetails()
  {
    return $this->billingDetails;
  }
  /**
   * Information specific to merchant affiliate program (read-only).
   *
   * @param ChannelToStoreLinkDetailsMerchantAffiliateProgramDetails $merchantAffiliateProgramDetails
   */
  public function setMerchantAffiliateProgramDetails(ChannelToStoreLinkDetailsMerchantAffiliateProgramDetails $merchantAffiliateProgramDetails)
  {
    $this->merchantAffiliateProgramDetails = $merchantAffiliateProgramDetails;
  }
  /**
   * @return ChannelToStoreLinkDetailsMerchantAffiliateProgramDetails
   */
  public function getMerchantAffiliateProgramDetails()
  {
    return $this->merchantAffiliateProgramDetails;
  }
  /**
   * Google Merchant Center id of the store.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  /**
   * Name of the store.
   *
   * @param string $storeName
   */
  public function setStoreName($storeName)
  {
    $this->storeName = $storeName;
  }
  /**
   * @return string
   */
  public function getStoreName()
  {
    return $this->storeName;
  }
  /**
   * Landing page of the store.
   *
   * @param string $storeUrl
   */
  public function setStoreUrl($storeUrl)
  {
    $this->storeUrl = $storeUrl;
  }
  /**
   * @return string
   */
  public function getStoreUrl()
  {
    return $this->storeUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelToStoreLinkDetails::class, 'Google_Service_YouTube_ChannelToStoreLinkDetails');
