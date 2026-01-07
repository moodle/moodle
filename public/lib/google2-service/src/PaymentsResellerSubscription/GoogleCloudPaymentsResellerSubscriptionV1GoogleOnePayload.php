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

namespace Google\Service\PaymentsResellerSubscription;

class GoogleCloudPaymentsResellerSubscriptionV1GoogleOnePayload extends \Google\Collection
{
  protected $collection_key = 'campaigns';
  /**
   * @var string[]
   */
  public $campaigns;
  /**
   * @var string
   */
  public $offering;
  /**
   * @var string
   */
  public $salesChannel;
  /**
   * @var string
   */
  public $storeId;

  /**
   * @param string[]
   */
  public function setCampaigns($campaigns)
  {
    $this->campaigns = $campaigns;
  }
  /**
   * @return string[]
   */
  public function getCampaigns()
  {
    return $this->campaigns;
  }
  /**
   * @param string
   */
  public function setOffering($offering)
  {
    $this->offering = $offering;
  }
  /**
   * @return string
   */
  public function getOffering()
  {
    return $this->offering;
  }
  /**
   * @param string
   */
  public function setSalesChannel($salesChannel)
  {
    $this->salesChannel = $salesChannel;
  }
  /**
   * @return string
   */
  public function getSalesChannel()
  {
    return $this->salesChannel;
  }
  /**
   * @param string
   */
  public function setStoreId($storeId)
  {
    $this->storeId = $storeId;
  }
  /**
   * @return string
   */
  public function getStoreId()
  {
    return $this->storeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPaymentsResellerSubscriptionV1GoogleOnePayload::class, 'Google_Service_PaymentsResellerSubscription_GoogleCloudPaymentsResellerSubscriptionV1GoogleOnePayload');
