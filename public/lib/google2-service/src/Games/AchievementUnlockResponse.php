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

class AchievementUnlockResponse extends \Google\Model
{
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementUnlockResponse`.
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
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementUnlockResponse`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AchievementUnlockResponse::class, 'Google_Service_Games_AchievementUnlockResponse');
