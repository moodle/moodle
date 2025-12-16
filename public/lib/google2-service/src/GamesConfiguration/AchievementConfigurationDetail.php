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

namespace Google\Service\GamesConfiguration;

class AchievementConfigurationDetail extends \Google\Model
{
  protected $descriptionType = LocalizedStringBundle::class;
  protected $descriptionDataType = '';
  /**
   * The icon url of this achievement. Writes to this field are ignored.
   *
   * @var string
   */
  public $iconUrl;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `gamesConfiguration#achievementConfigurationDetail`.
   *
   * @var string
   */
  public $kind;
  protected $nameType = LocalizedStringBundle::class;
  protected $nameDataType = '';
  /**
   * Point value for the achievement.
   *
   * @var int
   */
  public $pointValue;
  /**
   * The sort rank of this achievement. Writes to this field are ignored.
   *
   * @var int
   */
  public $sortRank;

  /**
   * Localized strings for the achievement description.
   *
   * @param LocalizedStringBundle $description
   */
  public function setDescription(LocalizedStringBundle $description)
  {
    $this->description = $description;
  }
  /**
   * @return LocalizedStringBundle
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The icon url of this achievement. Writes to this field are ignored.
   *
   * @param string $iconUrl
   */
  public function setIconUrl($iconUrl)
  {
    $this->iconUrl = $iconUrl;
  }
  /**
   * @return string
   */
  public function getIconUrl()
  {
    return $this->iconUrl;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `gamesConfiguration#achievementConfigurationDetail`.
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
   * Localized strings for the achievement name.
   *
   * @param LocalizedStringBundle $name
   */
  public function setName(LocalizedStringBundle $name)
  {
    $this->name = $name;
  }
  /**
   * @return LocalizedStringBundle
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Point value for the achievement.
   *
   * @param int $pointValue
   */
  public function setPointValue($pointValue)
  {
    $this->pointValue = $pointValue;
  }
  /**
   * @return int
   */
  public function getPointValue()
  {
    return $this->pointValue;
  }
  /**
   * The sort rank of this achievement. Writes to this field are ignored.
   *
   * @param int $sortRank
   */
  public function setSortRank($sortRank)
  {
    $this->sortRank = $sortRank;
  }
  /**
   * @return int
   */
  public function getSortRank()
  {
    return $this->sortRank;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AchievementConfigurationDetail::class, 'Google_Service_GamesConfiguration_AchievementConfigurationDetail');
