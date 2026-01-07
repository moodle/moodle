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

class PickupServicesPickupService extends \Google\Model
{
  /**
   * The name of the carrier (for example, `"UPS"`). Always present.
   *
   * @var string
   */
  public $carrierName;
  /**
   * The CLDR country code of the carrier (for example, "US"). Always present.
   *
   * @var string
   */
  public $country;
  /**
   * The name of the pickup service (for example, `"Access point"`). Always
   * present.
   *
   * @var string
   */
  public $serviceName;

  /**
   * The name of the carrier (for example, `"UPS"`). Always present.
   *
   * @param string $carrierName
   */
  public function setCarrierName($carrierName)
  {
    $this->carrierName = $carrierName;
  }
  /**
   * @return string
   */
  public function getCarrierName()
  {
    return $this->carrierName;
  }
  /**
   * The CLDR country code of the carrier (for example, "US"). Always present.
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
   * The name of the pickup service (for example, `"Access point"`). Always
   * present.
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PickupServicesPickupService::class, 'Google_Service_ShoppingContent_PickupServicesPickupService');
