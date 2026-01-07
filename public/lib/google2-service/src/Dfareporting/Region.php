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

class Region extends \Google\Model
{
  /**
   * Country code of the country to which this region belongs.
   *
   * @var string
   */
  public $countryCode;
  /**
   * DART ID of the country to which this region belongs.
   *
   * @var string
   */
  public $countryDartId;
  /**
   * DART ID of this region.
   *
   * @var string
   */
  public $dartId;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#region".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this region.
   *
   * @var string
   */
  public $name;
  /**
   * Region code.
   *
   * @var string
   */
  public $regionCode;

  /**
   * Country code of the country to which this region belongs.
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
   * DART ID of the country to which this region belongs.
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
   * DART ID of this region.
   *
   * @param string $dartId
   */
  public function setDartId($dartId)
  {
    $this->dartId = $dartId;
  }
  /**
   * @return string
   */
  public function getDartId()
  {
    return $this->dartId;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#region".
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
   * Name of this region.
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
  /**
   * Region code.
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
class_alias(Region::class, 'Google_Service_Dfareporting_Region');
