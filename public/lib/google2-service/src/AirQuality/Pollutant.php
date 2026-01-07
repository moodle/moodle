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

namespace Google\Service\AirQuality;

class Pollutant extends \Google\Model
{
  protected $additionalInfoType = AdditionalInfo::class;
  protected $additionalInfoDataType = '';
  /**
   * The pollutant's code name (for example, "so2"). For a list of supported
   * pollutant codes, see [Reported pollutants](/maps/documentation/air-
   * quality/pollutants#reported_pollutants).
   *
   * @var string
   */
  public $code;
  protected $concentrationType = Concentration::class;
  protected $concentrationDataType = '';
  /**
   * The pollutant's display name. For example: "NOx".
   *
   * @var string
   */
  public $displayName;
  /**
   * The pollutant's full name. For chemical compounds, this is the IUPAC name.
   * Example: "Sulfur Dioxide". For more information about the IUPAC names
   * table, see https://iupac.org/what-we-do/periodic-table-of-elements/.
   *
   * @var string
   */
  public $fullName;

  /**
   * Additional information about the pollutant.
   *
   * @param AdditionalInfo $additionalInfo
   */
  public function setAdditionalInfo(AdditionalInfo $additionalInfo)
  {
    $this->additionalInfo = $additionalInfo;
  }
  /**
   * @return AdditionalInfo
   */
  public function getAdditionalInfo()
  {
    return $this->additionalInfo;
  }
  /**
   * The pollutant's code name (for example, "so2"). For a list of supported
   * pollutant codes, see [Reported pollutants](/maps/documentation/air-
   * quality/pollutants#reported_pollutants).
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * The pollutant's concentration level measured by one of the standard air
   * pollutation measure units.
   *
   * @param Concentration $concentration
   */
  public function setConcentration(Concentration $concentration)
  {
    $this->concentration = $concentration;
  }
  /**
   * @return Concentration
   */
  public function getConcentration()
  {
    return $this->concentration;
  }
  /**
   * The pollutant's display name. For example: "NOx".
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The pollutant's full name. For chemical compounds, this is the IUPAC name.
   * Example: "Sulfur Dioxide". For more information about the IUPAC names
   * table, see https://iupac.org/what-we-do/periodic-table-of-elements/.
   *
   * @param string $fullName
   */
  public function setFullName($fullName)
  {
    $this->fullName = $fullName;
  }
  /**
   * @return string
   */
  public function getFullName()
  {
    return $this->fullName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Pollutant::class, 'Google_Service_AirQuality_Pollutant');
