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

namespace Google\Service\Games;

class AchievementDefinition extends \Google\Model
{
  /**
   * Achievement is either locked or unlocked.
   */
  public const ACHIEVEMENT_TYPE_STANDARD = 'STANDARD';
  /**
   * Achievement is incremental.
   */
  public const ACHIEVEMENT_TYPE_INCREMENTAL = 'INCREMENTAL';
  /**
   * Achievement is hidden.
   */
  public const INITIAL_STATE_HIDDEN = 'HIDDEN';
  /**
   * Achievement is revealed.
   */
  public const INITIAL_STATE_REVEALED = 'REVEALED';
  /**
   * Achievement is unlocked.
   */
  public const INITIAL_STATE_UNLOCKED = 'UNLOCKED';
  /**
   * The type of the achievement.
   *
   * @var string
   */
  public $achievementType;
  /**
   * The description of the achievement.
   *
   * @var string
   */
  public $description;
  /**
   * Experience points which will be earned when unlocking this achievement.
   *
   * @var string
   */
  public $experiencePoints;
  /**
   * The total steps for an incremental achievement as a string.
   *
   * @var string
   */
  public $formattedTotalSteps;
  /**
   * The ID of the achievement.
   *
   * @var string
   */
  public $id;
  /**
   * The initial state of the achievement.
   *
   * @var string
   */
  public $initialState;
  /**
   * Indicates whether the revealed icon image being returned is a default
   * image, or is provided by the game.
   *
   * @var bool
   */
  public $isRevealedIconUrlDefault;
  /**
   * Indicates whether the unlocked icon image being returned is a default
   * image, or is game-provided.
   *
   * @var bool
   */
  public $isUnlockedIconUrlDefault;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementDefinition`.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the achievement.
   *
   * @var string
   */
  public $name;
  /**
   * The image URL for the revealed achievement icon.
   *
   * @var string
   */
  public $revealedIconUrl;
  /**
   * The total steps for an incremental achievement.
   *
   * @var int
   */
  public $totalSteps;
  /**
   * The image URL for the unlocked achievement icon.
   *
   * @var string
   */
  public $unlockedIconUrl;

  /**
   * The type of the achievement.
   *
   * Accepted values: STANDARD, INCREMENTAL
   *
   * @param self::ACHIEVEMENT_TYPE_* $achievementType
   */
  public function setAchievementType($achievementType)
  {
    $this->achievementType = $achievementType;
  }
  /**
   * @return self::ACHIEVEMENT_TYPE_*
   */
  public function getAchievementType()
  {
    return $this->achievementType;
  }
  /**
   * The description of the achievement.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Experience points which will be earned when unlocking this achievement.
   *
   * @param string $experiencePoints
   */
  public function setExperiencePoints($experiencePoints)
  {
    $this->experiencePoints = $experiencePoints;
  }
  /**
   * @return string
   */
  public function getExperiencePoints()
  {
    return $this->experiencePoints;
  }
  /**
   * The total steps for an incremental achievement as a string.
   *
   * @param string $formattedTotalSteps
   */
  public function setFormattedTotalSteps($formattedTotalSteps)
  {
    $this->formattedTotalSteps = $formattedTotalSteps;
  }
  /**
   * @return string
   */
  public function getFormattedTotalSteps()
  {
    return $this->formattedTotalSteps;
  }
  /**
   * The ID of the achievement.
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
   * The initial state of the achievement.
   *
   * Accepted values: HIDDEN, REVEALED, UNLOCKED
   *
   * @param self::INITIAL_STATE_* $initialState
   */
  public function setInitialState($initialState)
  {
    $this->initialState = $initialState;
  }
  /**
   * @return self::INITIAL_STATE_*
   */
  public function getInitialState()
  {
    return $this->initialState;
  }
  /**
   * Indicates whether the revealed icon image being returned is a default
   * image, or is provided by the game.
   *
   * @param bool $isRevealedIconUrlDefault
   */
  public function setIsRevealedIconUrlDefault($isRevealedIconUrlDefault)
  {
    $this->isRevealedIconUrlDefault = $isRevealedIconUrlDefault;
  }
  /**
   * @return bool
   */
  public function getIsRevealedIconUrlDefault()
  {
    return $this->isRevealedIconUrlDefault;
  }
  /**
   * Indicates whether the unlocked icon image being returned is a default
   * image, or is game-provided.
   *
   * @param bool $isUnlockedIconUrlDefault
   */
  public function setIsUnlockedIconUrlDefault($isUnlockedIconUrlDefault)
  {
    $this->isUnlockedIconUrlDefault = $isUnlockedIconUrlDefault;
  }
  /**
   * @return bool
   */
  public function getIsUnlockedIconUrlDefault()
  {
    return $this->isUnlockedIconUrlDefault;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementDefinition`.
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
   * The name of the achievement.
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
   * The image URL for the revealed achievement icon.
   *
   * @param string $revealedIconUrl
   */
  public function setRevealedIconUrl($revealedIconUrl)
  {
    $this->revealedIconUrl = $revealedIconUrl;
  }
  /**
   * @return string
   */
  public function getRevealedIconUrl()
  {
    return $this->revealedIconUrl;
  }
  /**
   * The total steps for an incremental achievement.
   *
   * @param int $totalSteps
   */
  public function setTotalSteps($totalSteps)
  {
    $this->totalSteps = $totalSteps;
  }
  /**
   * @return int
   */
  public function getTotalSteps()
  {
    return $this->totalSteps;
  }
  /**
   * The image URL for the unlocked achievement icon.
   *
   * @param string $unlockedIconUrl
   */
  public function setUnlockedIconUrl($unlockedIconUrl)
  {
    $this->unlockedIconUrl = $unlockedIconUrl;
  }
  /**
   * @return string
   */
  public function getUnlockedIconUrl()
  {
    return $this->unlockedIconUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AchievementDefinition::class, 'Google_Service_Games_AchievementDefinition');
