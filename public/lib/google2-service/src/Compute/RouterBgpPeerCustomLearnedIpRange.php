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

namespace Google\Service\Compute;

class RouterBgpPeerCustomLearnedIpRange extends \Google\Model
{
  /**
   * The custom learned route IP address range. Must be a valid CIDR-formatted
   * prefix. If an IP address is provided without a subnet mask, it is
   * interpreted as, for IPv4, a `/32` singular IP address range, and, for IPv6,
   * `/128`.
   *
   * @var string
   */
  public $range;

  /**
   * The custom learned route IP address range. Must be a valid CIDR-formatted
   * prefix. If an IP address is provided without a subnet mask, it is
   * interpreted as, for IPv4, a `/32` singular IP address range, and, for IPv6,
   * `/128`.
   *
   * @param string $range
   */
  public function setRange($range)
  {
    $this->range = $range;
  }
  /**
   * @return string
   */
  public function getRange()
  {
    return $this->range;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterBgpPeerCustomLearnedIpRange::class, 'Google_Service_Compute_RouterBgpPeerCustomLearnedIpRange');
