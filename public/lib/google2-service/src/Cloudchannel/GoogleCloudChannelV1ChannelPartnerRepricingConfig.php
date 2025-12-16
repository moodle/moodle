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

class GoogleCloudChannelV1ChannelPartnerRepricingConfig extends \Google\Model
{
  /**
   * Output only. Resource name of the ChannelPartnerRepricingConfig. Format: ac
   * counts/{account_id}/channelPartnerLinks/{channel_partner_id}/channelPartner
   * RepricingConfigs/{id}.
   *
   * @var string
   */
  public $name;
  protected $repricingConfigType = GoogleCloudChannelV1RepricingConfig::class;
  protected $repricingConfigDataType = '';
  /**
   * Output only. Timestamp of an update to the repricing rule. If `update_time`
   * is after RepricingConfig.effective_invoice_month then it indicates this was
   * set mid-month.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Resource name of the ChannelPartnerRepricingConfig. Format: ac
   * counts/{account_id}/channelPartnerLinks/{channel_partner_id}/channelPartner
   * RepricingConfigs/{id}.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The configuration for bill modifications made by a reseller
   * before sending it to ChannelPartner.
   *
   * @param GoogleCloudChannelV1RepricingConfig $repricingConfig
   */
  public function setRepricingConfig(GoogleCloudChannelV1RepricingConfig $repricingConfig)
  {
    $this->repricingConfig = $repricingConfig;
  }
  /**
   * @return GoogleCloudChannelV1RepricingConfig
   */
  public function getRepricingConfig()
  {
    return $this->repricingConfig;
  }
  /**
   * Output only. Timestamp of an update to the repricing rule. If `update_time`
   * is after RepricingConfig.effective_invoice_month then it indicates this was
   * set mid-month.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1ChannelPartnerRepricingConfig::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1ChannelPartnerRepricingConfig');
