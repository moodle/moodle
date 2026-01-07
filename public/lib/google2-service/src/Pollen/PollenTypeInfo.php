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

class PollenTypeInfo extends \Google\Collection
{
  /**
   * Unspecified plant type.
   */
  public const CODE_POLLEN_TYPE_UNSPECIFIED = 'POLLEN_TYPE_UNSPECIFIED';
  /**
   * Grass pollen type.
   */
  public const CODE_GRASS = 'GRASS';
  /**
   * Tree pollen type.
   */
  public const CODE_TREE = 'TREE';
  /**
   * Weed pollen type.
   */
  public const CODE_WEED = 'WEED';
  protected $collection_key = 'healthRecommendations';
  /**
   * The pollen type's code name. For example: "GRASS"
   *
   * @var string
   */
  public $code;
  /**
   * A human readable representation of the pollen type name. Example: "Grass"
   *
   * @var string
   */
  public $displayName;
  /**
   * Textual list of explanations, related to health insights based on the
   * current pollen levels.
   *
   * @var string[]
   */
  public $healthRecommendations;
  /**
   * Indication whether the plant is in season or not.
   *
   * @var bool
   */
  public $inSeason;
  protected $indexInfoType = IndexInfo::class;
  protected $indexInfoDataType = '';

  /**
   * The pollen type's code name. For example: "GRASS"
   *
   * Accepted values: POLLEN_TYPE_UNSPECIFIED, GRASS, TREE, WEED
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
   * A human readable representation of the pollen type name. Example: "Grass"
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
   * Textual list of explanations, related to health insights based on the
   * current pollen levels.
   *
   * @param string[] $healthRecommendations
   */
  public function setHealthRecommendations($healthRecommendations)
  {
    $this->healthRecommendations = $healthRecommendations;
  }
  /**
   * @return string[]
   */
  public function getHealthRecommendations()
  {
    return $this->healthRecommendations;
  }
  /**
   * Indication whether the plant is in season or not.
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
   * Contains the Universal Pollen Index (UPI) data for the pollen type.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PollenTypeInfo::class, 'Google_Service_Pollen_PollenTypeInfo');
