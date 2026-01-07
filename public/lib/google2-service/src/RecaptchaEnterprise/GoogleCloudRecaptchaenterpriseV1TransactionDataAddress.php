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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1TransactionDataAddress extends \Google\Collection
{
  protected $collection_key = 'address';
  /**
   * Optional. The first lines of the address. The first line generally contains
   * the street name and number, and further lines may include information such
   * as an apartment number.
   *
   * @var string[]
   */
  public $address;
  /**
   * Optional. The state, province, or otherwise administrative area of the
   * address.
   *
   * @var string
   */
  public $administrativeArea;
  /**
   * Optional. The town/city of the address.
   *
   * @var string
   */
  public $locality;
  /**
   * Optional. The postal or ZIP code of the address.
   *
   * @var string
   */
  public $postalCode;
  /**
   * Optional. The recipient name, potentially including information such as
   * "care of".
   *
   * @var string
   */
  public $recipient;
  /**
   * Optional. The CLDR country/region of the address.
   *
   * @var string
   */
  public $regionCode;

  /**
   * Optional. The first lines of the address. The first line generally contains
   * the street name and number, and further lines may include information such
   * as an apartment number.
   *
   * @param string[] $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string[]
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Optional. The state, province, or otherwise administrative area of the
   * address.
   *
   * @param string $administrativeArea
   */
  public function setAdministrativeArea($administrativeArea)
  {
    $this->administrativeArea = $administrativeArea;
  }
  /**
   * @return string
   */
  public function getAdministrativeArea()
  {
    return $this->administrativeArea;
  }
  /**
   * Optional. The town/city of the address.
   *
   * @param string $locality
   */
  public function setLocality($locality)
  {
    $this->locality = $locality;
  }
  /**
   * @return string
   */
  public function getLocality()
  {
    return $this->locality;
  }
  /**
   * Optional. The postal or ZIP code of the address.
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
   * Optional. The recipient name, potentially including information such as
   * "care of".
   *
   * @param string $recipient
   */
  public function setRecipient($recipient)
  {
    $this->recipient = $recipient;
  }
  /**
   * @return string
   */
  public function getRecipient()
  {
    return $this->recipient;
  }
  /**
   * Optional. The CLDR country/region of the address.
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
class_alias(GoogleCloudRecaptchaenterpriseV1TransactionDataAddress::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1TransactionDataAddress');
