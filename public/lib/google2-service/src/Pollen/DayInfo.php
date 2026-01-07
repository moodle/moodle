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

namespace Google\Service\Pollen;

class DayInfo extends \Google\Collection
{
  protected $collection_key = 'pollenTypeInfo';
  protected $dateType = Date::class;
  protected $dateDataType = '';
  protected $plantInfoType = PlantInfo::class;
  protected $plantInfoDataType = 'array';
  protected $pollenTypeInfoType = PollenTypeInfo::class;
  protected $pollenTypeInfoDataType = 'array';

  /**
   * The date in UTC at which the pollen forecast data is represented.
   *
   * @param Date $date
   */
  public function setDate(Date $date)
  {
    $this->date = $date;
  }
  /**
   * @return Date
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * This list will include up to 15 pollen species affecting the location
   * specified in the request.
   *
   * @param PlantInfo[] $plantInfo
   */
  public function setPlantInfo($plantInfo)
  {
    $this->plantInfo = $plantInfo;
  }
  /**
   * @return PlantInfo[]
   */
  public function getPlantInfo()
  {
    return $this->plantInfo;
  }
  /**
   * This list will include up to three pollen types (GRASS, WEED, TREE)
   * affecting the location specified in the request.
   *
   * @param PollenTypeInfo[] $pollenTypeInfo
   */
  public function setPollenTypeInfo($pollenTypeInfo)
  {
    $this->pollenTypeInfo = $pollenTypeInfo;
  }
  /**
   * @return PollenTypeInfo[]
   */
  public function getPollenTypeInfo()
  {
    return $this->pollenTypeInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DayInfo::class, 'Google_Service_Pollen_DayInfo');
