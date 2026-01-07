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

class GoogleCloudChannelV1alpha1ChannelPartnerEvent extends \Google\Model
{
  /**
   * Default value. Does not display if there are no errors.
   */
  public const EVENT_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The Channel Partner link state changed.
   */
  public const EVENT_TYPE_LINK_STATE_CHANGED = 'LINK_STATE_CHANGED';
  /**
   * The Channel Partner's Partner Advantage information changed. This can
   * entail the Channel Partner's authorization to sell a product in a
   * particular region.
   */
  public const EVENT_TYPE_PARTNER_ADVANTAGE_INFO_CHANGED = 'PARTNER_ADVANTAGE_INFO_CHANGED';
  /**
   * Resource name for the Channel Partner Link. Channel_partner uses the
   * format: accounts/{account_id}/channelPartnerLinks/{channel_partner_id}
   *
   * @var string
   */
  public $channelPartner;
  /**
   * Type of event which happened for the channel partner.
   *
   * @var string
   */
  public $eventType;

  /**
   * Resource name for the Channel Partner Link. Channel_partner uses the
   * format: accounts/{account_id}/channelPartnerLinks/{channel_partner_id}
   *
   * @param string $channelPartner
   */
  public function setChannelPartner($channelPartner)
  {
    $this->channelPartner = $channelPartner;
  }
  /**
   * @return string
   */
  public function getChannelPartner()
  {
    return $this->channelPartner;
  }
  /**
   * Type of event which happened for the channel partner.
   *
   * Accepted values: TYPE_UNSPECIFIED, LINK_STATE_CHANGED,
   * PARTNER_ADVANTAGE_INFO_CHANGED
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1ChannelPartnerEvent::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1ChannelPartnerEvent');
