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

class AchievementUpdateResponse extends \Google\Model
{
  /**
   * Achievement is hidden.
   */
  public const CURRENT_STATE_HIDDEN = 'HIDDEN';
  /**
   * Achievement is revealed.
   */
  public const CURRENT_STATE_REVEALED = 'REVEALED';
  /**
   * Achievement is unlocked.
   */
  public const CURRENT_STATE_UNLOCKED = 'UNLOCKED';
  /**
   * The achievement this update is was applied to.
   *
   * @var string
   */
  public $achievementId;
  /**
   * The current state of the achievement.
   *
   * @var string
   */
  public $currentState;
  /**
   * The current steps recorded for this achievement if it is incremental.
   *
   * @var int
   */
  public $currentSteps;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementUpdateResponse`.
   *
   * @var string
   */
  public $kind;
  /**
   * Whether this achievement was newly unlocked (that is, whether the unlock
   * request for the achievement was the first for the player).
   *
   * @var bool
   */
  public $newlyUnlocked;
  /**
   * Whether the requested updates actually affected the achievement.
   *
   * @var bool
   */
  public $updateOccurred;

  /**
   * The achievement this update is was applied to.
   *
   * @param string $achievementId
   */
  public function setAchievementId($achievementId)
  {
    $this->achievementId = $achievementId;
  }
  /**
   * @return string
   */
  public function getAchievementId()
  {
    return $this->achievementId;
  }
  /**
   * The current state of the achievement.
   *
   * Accepted values: HIDDEN, REVEALED, UNLOCKED
   *
   * @param self::CURRENT_STATE_* $currentState
   */
  public function setCurrentState($currentState)
  {
    $this->currentState = $currentState;
  }
  /**
   * @return self::CURRENT_STATE_*
   */
  public function getCurrentState()
  {
    return $this->currentState;
  }
  /**
   * The current steps recorded for this achievement if it is incremental.
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
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementUpdateResponse`.
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
   * Whether this achievement was newly unlocked (that is, whether the unlock
   * request for the achievement was the first for the player).
   *
   * @param bool $newlyUnlocked
   */
  public function setNewlyUnlocked($newlyUnlocked)
  {
    $this->newlyUnlocked = $newlyUnlocked;
  }
  /**
   * @return bool
   */
  public function getNewlyUnlocked()
  {
    return $this->newlyUnlocked;
  }
  /**
   * Whether the requested updates actually affected the achievement.
   *
   * @param bool $updateOccurred
   */
  public function setUpdateOccurred($updateOccurred)
  {
    $this->updateOccurred = $updateOccurred;
  }
  /**
   * @return bool
   */
  public function getUpdateOccurred()
  {
    return $this->updateOccurred;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AchievementUpdateResponse::class, 'Google_Service_Games_AchievementUpdateResponse');
