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

class AchievementSetStepsAtLeastResponse extends \Google\Model
{
  /**
   * The current steps recorded for this incremental achievement.
   *
   * @var int
   */
  public $currentSteps;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementSetStepsAtLeastResponse`.
   *
   * @var string
   */
  public $kind;
  /**
   * Whether the current steps for the achievement has reached the number of
   * steps required to unlock.
   *
   * @var bool
   */
  public $newlyUnlocked;

  /**
   * The current steps recorded for this incremental achievement.
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
   * string `games#achievementSetStepsAtLeastResponse`.
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
   * Whether the current steps for the achievement has reached the number of
   * steps required to unlock.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AchievementSetStepsAtLeastResponse::class, 'Google_Service_Games_AchievementSetStepsAtLeastResponse');
