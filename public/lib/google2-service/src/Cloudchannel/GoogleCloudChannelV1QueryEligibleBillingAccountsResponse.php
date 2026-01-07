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

class GoogleCloudChannelV1QueryEligibleBillingAccountsResponse extends \Google\Collection
{
  protected $collection_key = 'skuPurchaseGroups';
  protected $skuPurchaseGroupsType = GoogleCloudChannelV1SkuPurchaseGroup::class;
  protected $skuPurchaseGroupsDataType = 'array';

  /**
   * List of SKU purchase groups where each group represents a set of SKUs that
   * must be purchased using the same billing account. Each SKU from
   * [QueryEligibleBillingAccountsRequest.skus] will appear in exactly one SKU
   * group.
   *
   * @param GoogleCloudChannelV1SkuPurchaseGroup[] $skuPurchaseGroups
   */
  public function setSkuPurchaseGroups($skuPurchaseGroups)
  {
    $this->skuPurchaseGroups = $skuPurchaseGroups;
  }
  /**
   * @return GoogleCloudChannelV1SkuPurchaseGroup[]
   */
  public function getSkuPurchaseGroups()
  {
    return $this->skuPurchaseGroups;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1QueryEligibleBillingAccountsResponse::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1QueryEligibleBillingAccountsResponse');
