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

class PlayerExperienceInfo extends \Google\Model
{
  /**
   * The current number of experience points for the player.
   *
   * @var string
   */
  public $currentExperiencePoints;
  protected $currentLevelType = PlayerLevel::class;
  protected $currentLevelDataType = '';
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#playerExperienceInfo`.
   *
   * @var string
   */
  public $kind;
  /**
   * The timestamp when the player was leveled up, in millis since Unix epoch
   * UTC.
   *
   * @var string
   */
  public $lastLevelUpTimestampMillis;
  protected $nextLevelType = PlayerLevel::class;
  protected $nextLevelDataType = '';

  /**
   * The current number of experience points for the player.
   *
   * @param string $currentExperiencePoints
   */
  public function setCurrentExperiencePoints($currentExperiencePoints)
  {
    $this->currentExperiencePoints = $currentExperiencePoints;
  }
  /**
   * @return string
   */
  public function getCurrentExperiencePoints()
  {
    return $this->currentExperiencePoints;
  }
  /**
   * The current level of the player.
   *
   * @param PlayerLevel $currentLevel
   */
  public function setCurrentLevel(PlayerLevel $currentLevel)
  {
    $this->currentLevel = $currentLevel;
  }
  /**
   * @return PlayerLevel
   */
  public function getCurrentLevel()
  {
    return $this->currentLevel;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#playerExperienceInfo`.
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
   * The timestamp when the player was leveled up, in millis since Unix epoch
   * UTC.
   *
   * @param string $lastLevelUpTimestampMillis
   */
  public function setLastLevelUpTimestampMillis($lastLevelUpTimestampMillis)
  {
    $this->lastLevelUpTimestampMillis = $lastLevelUpTimestampMillis;
  }
  /**
   * @return string
   */
  public function getLastLevelUpTimestampMillis()
  {
    return $this->lastLevelUpTimestampMillis;
  }
  /**
   * The next level of the player. If the current level is the maximum level,
   * this should be same as the current level.
   *
   * @param PlayerLevel $nextLevel
   */
  public function setNextLevel(PlayerLevel $nextLevel)
  {
    $this->nextLevel = $nextLevel;
  }
  /**
   * @return PlayerLevel
   */
  public function getNextLevel()
  {
    return $this->nextLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlayerExperienceInfo::class, 'Google_Service_Games_PlayerExperienceInfo');
