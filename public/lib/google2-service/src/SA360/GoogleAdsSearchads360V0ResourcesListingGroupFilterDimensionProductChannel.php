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

class GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductChannel extends \Google\Model
{
  /**
   * Not specified.
   */
  public const CHANNEL_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const CHANNEL_UNKNOWN = 'UNKNOWN';
  /**
   * The item is sold online.
   */
  public const CHANNEL_ONLINE = 'ONLINE';
  /**
   * The item is sold in local stores.
   */
  public const CHANNEL_LOCAL = 'LOCAL';
  /**
   * Value of the locality.
   *
   * @var string
   */
  public $channel;

  /**
   * Value of the locality.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ONLINE, LOCAL
   *
   * @param self::CHANNEL_* $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return self::CHANNEL_*
   */
  public function getChannel()
  {
    return $this->channel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductChannel::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductChannel');
