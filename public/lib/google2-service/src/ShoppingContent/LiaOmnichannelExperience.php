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

namespace Google\Service\ShoppingContent;

class LiaOmnichannelExperience extends \Google\Collection
{
  protected $collection_key = 'pickupTypes';
  /**
   * The CLDR country code (for example, "US").
   *
   * @var string
   */
  public $country;
  /**
   * The Local Store Front (LSF) type for this country. Acceptable values are: -
   * "`ghlsf`" (Google-Hosted Local Store Front) - "`mhlsfBasic`" (Merchant-
   * Hosted Local Store Front Basic) - "`mhlsfFull`" (Merchant-Hosted Local
   * Store Front Full) More details about these types can be found here.
   *
   * @var string
   */
  public $lsfType;
  /**
   * The Pickup types for this country. Acceptable values are: - "`pickupToday`"
   * - "`pickupLater`"
   *
   * @var string[]
   */
  public $pickupTypes;

  /**
   * The CLDR country code (for example, "US").
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The Local Store Front (LSF) type for this country. Acceptable values are: -
   * "`ghlsf`" (Google-Hosted Local Store Front) - "`mhlsfBasic`" (Merchant-
   * Hosted Local Store Front Basic) - "`mhlsfFull`" (Merchant-Hosted Local
   * Store Front Full) More details about these types can be found here.
   *
   * @param string $lsfType
   */
  public function setLsfType($lsfType)
  {
    $this->lsfType = $lsfType;
  }
  /**
   * @return string
   */
  public function getLsfType()
  {
    return $this->lsfType;
  }
  /**
   * The Pickup types for this country. Acceptable values are: - "`pickupToday`"
   * - "`pickupLater`"
   *
   * @param string[] $pickupTypes
   */
  public function setPickupTypes($pickupTypes)
  {
    $this->pickupTypes = $pickupTypes;
  }
  /**
   * @return string[]
   */
  public function getPickupTypes()
  {
    return $this->pickupTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiaOmnichannelExperience::class, 'Google_Service_ShoppingContent_LiaOmnichannelExperience');
