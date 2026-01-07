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

class AchievementRevealResponse extends \Google\Model
{
  /**
   * Achievement is revealed.
   */
  public const CURRENT_STATE_REVEALED = 'REVEALED';
  /**
   * Achievement is unlocked.
   */
  public const CURRENT_STATE_UNLOCKED = 'UNLOCKED';
  /**
   * The current state of the achievement for which a reveal was attempted. This
   * might be `UNLOCKED` if the achievement was already unlocked.
   *
   * @var string
   */
  public $currentState;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementRevealResponse`.
   *
   * @var string
   */
  public $kind;

  /**
   * The current state of the achievement for which a reveal was attempted. This
   * might be `UNLOCKED` if the achievement was already unlocked.
   *
   * Accepted values: REVEALED, UNLOCKED
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
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementRevealResponse`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AchievementRevealResponse::class, 'Google_Service_Games_AchievementRevealResponse');
