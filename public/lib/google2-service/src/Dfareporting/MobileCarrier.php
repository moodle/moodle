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

namespace Google\Service\Dfareporting;

class MobileCarrier extends \Google\Model
{
  /**
   * Country code of the country to which this mobile carrier belongs.
   *
   * @var string
   */
  public $countryCode;
  /**
   * DART ID of the country to which this mobile carrier belongs.
   *
   * @var string
   */
  public $countryDartId;
  /**
   * ID of this mobile carrier.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#mobileCarrier".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this mobile carrier.
   *
   * @var string
   */
  public $name;

  /**
   * Country code of the country to which this mobile carrier belongs.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * DART ID of the country to which this mobile carrier belongs.
   *
   * @param string $countryDartId
   */
  public function setCountryDartId($countryDartId)
  {
    $this->countryDartId = $countryDartId;
  }
  /**
   * @return string
   */
  public function getCountryDartId()
  {
    return $this->countryDartId;
  }
  /**
   * ID of this mobile carrier.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#mobileCarrier".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of this mobile carrier.
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
class_alias(MobileCarrier::class, 'Google_Service_Dfareporting_MobileCarrier');
