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

class GoogleCloudChannelV1alpha1ProvisionedService extends \Google\Model
{
  /**
   * Output only. The product pertaining to the provisioning resource as
   * specified in the Offer.
   *
   * @var string
   */
  public $productId;
  /**
   * Output only. Provisioning ID of the entitlement. For Google Workspace, this
   * is the underlying Subscription ID. For Google Cloud, this is the Billing
   * Account ID of the billing subaccount.
   *
   * @var string
   */
  public $provisioningId;
  /**
   * Output only. The SKU pertaining to the provisioning resource as specified
   * in the Offer.
   *
   * @var string
   */
  public $skuId;

  /**
   * Output only. The product pertaining to the provisioning resource as
   * specified in the Offer.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * Output only. Provisioning ID of the entitlement. For Google Workspace, this
   * is the underlying Subscription ID. For Google Cloud, this is the Billing
   * Account ID of the billing subaccount.
   *
   * @param string $provisioningId
   */
  public function setProvisioningId($provisioningId)
  {
    $this->provisioningId = $provisioningId;
  }
  /**
   * @return string
   */
  public function getProvisioningId()
  {
    return $this->provisioningId;
  }
  /**
   * Output only. The SKU pertaining to the provisioning resource as specified
   * in the Offer.
   *
   * @param string $skuId
   */
  public function setSkuId($skuId)
  {
    $this->skuId = $skuId;
  }
  /**
   * @return string
   */
  public function getSkuId()
  {
    return $this->skuId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1ProvisionedService::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1ProvisionedService');
