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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesDoubleClickCampaignManagerSetting extends \Google\Model
{
  /**
   * Output only. ID of the Campaign Manager advertiser associated with this
   * customer.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Output only. ID of the Campaign Manager network associated with this
   * customer.
   *
   * @var string
   */
  public $networkId;
  /**
   * Output only. Time zone of the Campaign Manager network associated with this
   * customer in IANA Time Zone Database format, such as America/New_York.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Output only. ID of the Campaign Manager advertiser associated with this
   * customer.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Output only. ID of the Campaign Manager network associated with this
   * customer.
   *
   * @param string $networkId
   */
  public function setNetworkId($networkId)
  {
    $this->networkId = $networkId;
  }
  /**
   * @return string
   */
  public function getNetworkId()
  {
    return $this->networkId;
  }
  /**
   * Output only. Time zone of the Campaign Manager network associated with this
   * customer in IANA Time Zone Database format, such as America/New_York.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesDoubleClickCampaignManagerSetting::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesDoubleClickCampaignManagerSetting');
