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

class RouterAdvertisedIpRange extends \Google\Model
{
  /**
   * User-specified description for the IP range.
   *
   * @var string
   */
  public $description;
  /**
   * The IP range to advertise. The value must be a CIDR-formatted string.
   *
   * @var string
   */
  public $range;

  /**
   * User-specified description for the IP range.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The IP range to advertise. The value must be a CIDR-formatted string.
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
class_alias(RouterAdvertisedIpRange::class, 'Google_Service_Compute_RouterAdvertisedIpRange');
