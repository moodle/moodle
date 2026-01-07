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

class PlantInfo extends \Google\Model
{
  /**
   * Unspecified plant code.
   */
  public const CODE_PLANT_UNSPECIFIED = 'PLANT_UNSPECIFIED';
  /**
   * Alder is classified as a tree pollen type.
   */
  public const CODE_ALDER = 'ALDER';
  /**
   * Ash is classified as a tree pollen type.
   */
  public const CODE_ASH = 'ASH';
  /**
   * Birch is classified as a tree pollen type.
   */
  public const CODE_BIRCH = 'BIRCH';
  /**
   * Cottonwood is classified as a tree pollen type.
   */
  public const CODE_COTTONWOOD = 'COTTONWOOD';
  /**
   * Elm is classified as a tree pollen type.
   */
  public const CODE_ELM = 'ELM';
  /**
   * Maple is classified as a tree pollen type.
   */
  public const CODE_MAPLE = 'MAPLE';
  /**
   * Olive is classified as a tree pollen type.
   */
  public const CODE_OLIVE = 'OLIVE';
  /**
   * Juniper is classified as a tree pollen type.
   */
  public const CODE_JUNIPER = 'JUNIPER';
  /**
   * Oak is classified as a tree pollen type.
   */
  public const CODE_OAK = 'OAK';
  /**
   * Pine is classified as a tree pollen type.
   */
  public const CODE_PINE = 'PINE';
  /**
   * Cypress pine is classified as a tree pollen type.
   */
  public const CODE_CYPRESS_PINE = 'CYPRESS_PINE';
  /**
   * Hazel is classified as a tree pollen type.
   */
  public const CODE_HAZEL = 'HAZEL';
  /**
   * Graminales is classified as a grass pollen type.
   */
  public const CODE_GRAMINALES = 'GRAMINALES';
  /**
   * Ragweed is classified as a weed pollen type.
   */
  public const CODE_RAGWEED = 'RAGWEED';
  /**
   * Mugwort is classified as a weed pollen type.
   */
  public const CODE_MUGWORT = 'MUGWORT';
  /**
   * Japanese cedar is classified as a tree pollen type.
   */
  public const CODE_JAPANESE_CEDAR = 'JAPANESE_CEDAR';
  /**
   * Japanese cypress is classified as a tree pollen type.
   */
  public const CODE_JAPANESE_CYPRESS = 'JAPANESE_CYPRESS';
  /**
   * The plant code name. For example: "COTTONWOOD". A list of all available
   * codes could be found here.
   *
   * @var string
   */
  public $code;
  /**
   * A human readable representation of the plant name. Example: “Cottonwood".
   *
   * @var string
   */
  public $displayName;
  /**
   * Indication of either the plant is in season or not.
   *
   * @var bool
   */
  public $inSeason;
  protected $indexInfoType = IndexInfo::class;
  protected $indexInfoDataType = '';
  protected $plantDescriptionType = PlantDescription::class;
  protected $plantDescriptionDataType = '';

  /**
   * The plant code name. For example: "COTTONWOOD". A list of all available
   * codes could be found here.
   *
   * Accepted values: PLANT_UNSPECIFIED, ALDER, ASH, BIRCH, COTTONWOOD, ELM,
   * MAPLE, OLIVE, JUNIPER, OAK, PINE, CYPRESS_PINE, HAZEL, GRAMINALES, RAGWEED,
   * MUGWORT, JAPANESE_CEDAR, JAPANESE_CYPRESS
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * A human readable representation of the plant name. Example: “Cottonwood".
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
   * Indication of either the plant is in season or not.
   *
   * @param bool $inSeason
   */
  public function setInSeason($inSeason)
  {
    $this->inSeason = $inSeason;
  }
  /**
   * @return bool
   */
  public function getInSeason()
  {
    return $this->inSeason;
  }
  /**
   * This object contains data representing specific pollen index value,
   * category and description.
   *
   * @param IndexInfo $indexInfo
   */
  public function setIndexInfo(IndexInfo $indexInfo)
  {
    $this->indexInfo = $indexInfo;
  }
  /**
   * @return IndexInfo
   */
  public function getIndexInfo()
  {
    return $this->indexInfo;
  }
  /**
   * Contains general information about plants, including details on their
   * seasonality, special shapes and colors, information about allergic cross-
   * reactions, and plant photos.
   *
   * @param PlantDescription $plantDescription
   */
  public function setPlantDescription(PlantDescription $plantDescription)
  {
    $this->plantDescription = $plantDescription;
  }
  /**
   * @return PlantDescription
   */
  public function getPlantDescription()
  {
    return $this->plantDescription;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlantInfo::class, 'Google_Service_Pollen_PlantInfo');
