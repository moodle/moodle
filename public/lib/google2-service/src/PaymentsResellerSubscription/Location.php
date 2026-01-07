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

class Location extends \Google\Model
{
  /**
   * The postal code this location refers to. Ex. "94043"
   *
   * @var string
   */
  public $postalCode;
  /**
   * 2-letter ISO region code for current content region. Ex. “US” Please refers
   * to: https://en.wikipedia.org/wiki/ISO_3166-1
   *
   * @var string
   */
  public $regionCode;

  /**
   * The postal code this location refers to. Ex. "94043"
   *
   * @param string $postalCode
   */
  public function setPostalCode($postalCode)
  {
    $this->postalCode = $postalCode;
  }
  /**
   * @return string
   */
  public function getPostalCode()
  {
    return $this->postalCode;
  }
  /**
   * 2-letter ISO region code for current content region. Ex. “US” Please refers
   * to: https://en.wikipedia.org/wiki/ISO_3166-1
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Location::class, 'Google_Service_PaymentsResellerSubscription_Location');
