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

namespace Google\Service\DisplayVideo;

class Channel extends \Google\Model
{
  /**
   * The ID of the advertiser that owns the channel.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Output only. The unique ID of the channel. Assigned by the system.
   *
   * @var string
   */
  public $channelId;
  /**
   * Required. The display name of the channel. Must be UTF-8 encoded with a
   * maximum length of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The resource name of the channel.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Number of line items that are directly targeting this channel
   * negatively.
   *
   * @var string
   */
  public $negativelyTargetedLineItemCount;
  /**
   * The ID of the partner that owns the channel.
   *
   * @var string
   */
  public $partnerId;
  /**
   * Output only. Number of line items that are directly targeting this channel
   * positively.
   *
   * @var string
   */
  public $positivelyTargetedLineItemCount;

  /**
   * The ID of the advertiser that owns the channel.
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
   * Output only. The unique ID of the channel. Assigned by the system.
   *
   * @param string $channelId
   */
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }
  /**
   * @return string
   */
  public function getChannelId()
  {
    return $this->channelId;
  }
  /**
   * Required. The display name of the channel. Must be UTF-8 encoded with a
   * maximum length of 240 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The resource name of the channel.
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
   * Output only. Number of line items that are directly targeting this channel
   * negatively.
   *
   * @param string $negativelyTargetedLineItemCount
   */
  public function setNegativelyTargetedLineItemCount($negativelyTargetedLineItemCount)
  {
    $this->negativelyTargetedLineItemCount = $negativelyTargetedLineItemCount;
  }
  /**
   * @return string
   */
  public function getNegativelyTargetedLineItemCount()
  {
    return $this->negativelyTargetedLineItemCount;
  }
  /**
   * The ID of the partner that owns the channel.
   *
   * @param string $partnerId
   */
  public function setPartnerId($partnerId)
  {
    $this->partnerId = $partnerId;
  }
  /**
   * @return string
   */
  public function getPartnerId()
  {
    return $this->partnerId;
  }
  /**
   * Output only. Number of line items that are directly targeting this channel
   * positively.
   *
   * @param string $positivelyTargetedLineItemCount
   */
  public function setPositivelyTargetedLineItemCount($positivelyTargetedLineItemCount)
  {
    $this->positivelyTargetedLineItemCount = $positivelyTargetedLineItemCount;
  }
  /**
   * @return string
   */
  public function getPositivelyTargetedLineItemCount()
  {
    return $this->positivelyTargetedLineItemCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Channel::class, 'Google_Service_DisplayVideo_Channel');
