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

class PlayerAchievement extends \Google\Model
{
  /**
   * Achievement is hidden.
   */
  public const ACHIEVEMENT_STATE_HIDDEN = 'HIDDEN';
  /**
   * Achievement is revealed.
   */
  public const ACHIEVEMENT_STATE_REVEALED = 'REVEALED';
  /**
   * Achievement is unlocked.
   */
  public const ACHIEVEMENT_STATE_UNLOCKED = 'UNLOCKED';
  /**
   * The state of the achievement.
   *
   * @var string
   */
  public $achievementState;
  /**
   * The current steps for an incremental achievement.
   *
   * @var int
   */
  public $currentSteps;
  /**
   * Experience points earned for the achievement. This field is absent for
   * achievements that have not yet been unlocked and 0 for achievements that
   * have been unlocked by testers but that are unpublished.
   *
   * @var string
   */
  public $experiencePoints;
  /**
   * The current steps for an incremental achievement as a string.
   *
   * @var string
   */
  public $formattedCurrentStepsString;
  /**
   * The ID of the achievement.
   *
   * @var string
   */
  public $id;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#playerAchievement`.
   *
   * @var string
   */
  public $kind;
  /**
   * The timestamp of the last modification to this achievement's state.
   *
   * @var string
   */
  public $lastUpdatedTimestamp;

  /**
   * The state of the achievement.
   *
   * Accepted values: HIDDEN, REVEALED, UNLOCKED
   *
   * @param self::ACHIEVEMENT_STATE_* $achievementState
   */
  public function setAchievementState($achievementState)
  {
    $this->achievementState = $achievementState;
  }
  /**
   * @return self::ACHIEVEMENT_STATE_*
   */
  public function getAchievementState()
  {
    return $this->achievementState;
  }
  /**
   * The current steps for an incremental achievement.
   *
   * @param int $currentSteps
   */
  public function setCurrentSteps($currentSteps)
  {
    $this->currentSteps = $currentSteps;
  }
  /**
   * @return int
   */
  public function getCurrentSteps()
  {
    return $this->currentSteps;
  }
  /**
   * Experience points earned for the achievement. This field is absent for
   * achievements that have not yet been unlocked and 0 for achievements that
   * have been unlocked by testers but that are unpublished.
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
   * The current steps for an incremental achievement as a string.
   *
   * @param string $formattedCurrentStepsString
   */
  public function setFormattedCurrentStepsString($formattedCurrentStepsString)
  {
    $this->formattedCurrentStepsString = $formattedCurrentStepsString;
  }
  /**
   * @return string
   */
  public function getFormattedCurrentStepsString()
  {
    return $this->formattedCurrentStepsString;
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
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#playerAchievement`.
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
   * The timestamp of the last modification to this achievement's state.
   *
   * @param string $lastUpdatedTimestamp
   */
  public function setLastUpdatedTimestamp($lastUpdatedTimestamp)
  {
    $this->lastUpdatedTimestamp = $lastUpdatedTimestamp;
  }
  /**
   * @return string
   */
  public function getLastUpdatedTimestamp()
  {
    return $this->lastUpdatedTimestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlayerAchievement::class, 'Google_Service_Games_PlayerAchievement');
