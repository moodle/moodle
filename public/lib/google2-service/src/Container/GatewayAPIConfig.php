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

namespace Google\Service\Container;

class GatewayAPIConfig extends \Google\Model
{
  /**
   * Default value.
   */
  public const CHANNEL_CHANNEL_UNSPECIFIED = 'CHANNEL_UNSPECIFIED';
  /**
   * Gateway API support is disabled
   */
  public const CHANNEL_CHANNEL_DISABLED = 'CHANNEL_DISABLED';
  /**
   * Deprecated: use CHANNEL_STANDARD instead. Gateway API support is enabled,
   * experimental CRDs are installed
   *
   * @deprecated
   */
  public const CHANNEL_CHANNEL_EXPERIMENTAL = 'CHANNEL_EXPERIMENTAL';
  /**
   * Gateway API support is enabled, standard CRDs are installed
   */
  public const CHANNEL_CHANNEL_STANDARD = 'CHANNEL_STANDARD';
  /**
   * The Gateway API release channel to use for Gateway API.
   *
   * @var string
   */
  public $channel;

  /**
   * The Gateway API release channel to use for Gateway API.
   *
   * Accepted values: CHANNEL_UNSPECIFIED, CHANNEL_DISABLED,
   * CHANNEL_EXPERIMENTAL, CHANNEL_STANDARD
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
class_alias(GatewayAPIConfig::class, 'Google_Service_Container_GatewayAPIConfig');
