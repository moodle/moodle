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

namespace Google\Service\VersionHistory;

class Channel extends \Google\Model
{
  public const CHANNEL_TYPE_CHANNEL_TYPE_UNSPECIFIED = 'CHANNEL_TYPE_UNSPECIFIED';
  /**
   * The Stable channel.
   */
  public const CHANNEL_TYPE_STABLE = 'STABLE';
  /**
   * The Beta channel.
   */
  public const CHANNEL_TYPE_BETA = 'BETA';
  /**
   * The Dev channel.
   */
  public const CHANNEL_TYPE_DEV = 'DEV';
  /**
   * The Canary channel.
   */
  public const CHANNEL_TYPE_CANARY = 'CANARY';
  /**
   * The Canary channel for Chrome, with DCHECK/ASAN enabled.
   */
  public const CHANNEL_TYPE_CANARY_ASAN = 'CANARY_ASAN';
  public const CHANNEL_TYPE_ALL = 'ALL';
  /**
   * The Extended Stable channel for Chrome.
   */
  public const CHANNEL_TYPE_EXTENDED = 'EXTENDED';
  /**
   * The Long-term support channel for ChromeOS.
   */
  public const CHANNEL_TYPE_LTS = 'LTS';
  /**
   * The Long-term support candidate channel for ChromeOS.
   */
  public const CHANNEL_TYPE_LTC = 'LTC';
  /**
   * Type of channel.
   *
   * @var string
   */
  public $channelType;
  /**
   * Channel name. Format is "{product}/platforms/{platform}/channels/{channel}"
   *
   * @var string
   */
  public $name;

  /**
   * Type of channel.
   *
   * Accepted values: CHANNEL_TYPE_UNSPECIFIED, STABLE, BETA, DEV, CANARY,
   * CANARY_ASAN, ALL, EXTENDED, LTS, LTC
   *
   * @param self::CHANNEL_TYPE_* $channelType
   */
  public function setChannelType($channelType)
  {
    $this->channelType = $channelType;
  }
  /**
   * @return self::CHANNEL_TYPE_*
   */
  public function getChannelType()
  {
    return $this->channelType;
  }
  /**
   * Channel name. Format is "{product}/platforms/{platform}/channels/{channel}"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Channel::class, 'Google_Service_VersionHistory_Channel');
