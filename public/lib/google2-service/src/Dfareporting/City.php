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

class City extends \Google\Model
{
  /**
   * Country code of the country to which this city belongs.
   *
   * @var string
   */
  public $countryCode;
  /**
   * DART ID of the country to which this city belongs.
   *
   * @var string
   */
  public $countryDartId;
  /**
   * DART ID of this city. This is the ID used for targeting and generating
   * reports.
   *
   * @var string
   */
  public $dartId;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#city".
   *
   * @var string
   */
  public $kind;
  /**
   * Metro region code of the metro region (DMA) to which this city belongs.
   *
   * @var string
   */
  public $metroCode;
  /**
   * ID of the metro region (DMA) to which this city belongs.
   *
   * @var string
   */
  public $metroDmaId;
  /**
   * Name of this city.
   *
   * @var string
   */
  public $name;
  /**
   * Region code of the region to which this city belongs.
   *
   * @var string
   */
  public $regionCode;
  /**
   * DART ID of the region to which this city belongs.
   *
   * @var string
   */
  public $regionDartId;

  /**
   * Country code of the country to which this city belongs.
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
   * DART ID of the country to which this city belongs.
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
   * DART ID of this city. This is the ID used for targeting and generating
   * reports.
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
   * "dfareporting#city".
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
   * Metro region code of the metro region (DMA) to which this city belongs.
   *
   * @param string $metroCode
   */
  public function setMetroCode($metroCode)
  {
    $this->metroCode = $metroCode;
  }
  /**
   * @return string
   */
  public function getMetroCode()
  {
    return $this->metroCode;
  }
  /**
   * ID of the metro region (DMA) to which this city belongs.
   *
   * @param string $metroDmaId
   */
  public function setMetroDmaId($metroDmaId)
  {
    $this->metroDmaId = $metroDmaId;
  }
  /**
   * @return string
   */
  public function getMetroDmaId()
  {
    return $this->metroDmaId;
  }
  /**
   * Name of this city.
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
   * Region code of the region to which this city belongs.
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
  /**
   * DART ID of the region to which this city belongs.
   *
   * @param string $regionDartId
   */
  public function setRegionDartId($regionDartId)
  {
    $this->regionDartId = $regionDartId;
  }
  /**
   * @return string
   */
  public function getRegionDartId()
  {
    return $this->regionDartId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(City::class, 'Google_Service_Dfareporting_City');
