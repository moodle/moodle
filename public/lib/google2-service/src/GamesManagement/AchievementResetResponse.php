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

namespace Google\Service\GamesManagement;

class AchievementResetResponse extends \Google\Model
{
  /**
   * The current state of the achievement. This is the same as the initial state
   * of the achievement. Possible values are: - "`HIDDEN`"- Achievement is
   * hidden. - "`REVEALED`" - Achievement is revealed. - "`UNLOCKED`" -
   * Achievement is unlocked.
   *
   * @var string
   */
  public $currentState;
  /**
   * The ID of an achievement for which player state has been updated.
   *
   * @var string
   */
  public $definitionId;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `gamesManagement#achievementResetResponse`.
   *
   * @var string
   */
  public $kind;
  /**
   * Flag to indicate if the requested update actually occurred.
   *
   * @var bool
   */
  public $updateOccurred;

  /**
   * The current state of the achievement. This is the same as the initial state
   * of the achievement. Possible values are: - "`HIDDEN`"- Achievement is
   * hidden. - "`REVEALED`" - Achievement is revealed. - "`UNLOCKED`" -
   * Achievement is unlocked.
   *
   * @param string $currentState
   */
  public function setCurrentState($currentState)
  {
    $this->currentState = $currentState;
  }
  /**
   * @return string
   */
  public function getCurrentState()
  {
    return $this->currentState;
  }
  /**
   * The ID of an achievement for which player state has been updated.
   *
   * @param string $definitionId
   */
  public function setDefinitionId($definitionId)
  {
    $this->definitionId = $definitionId;
  }
  /**
   * @return string
   */
  public function getDefinitionId()
  {
    return $this->definitionId;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `gamesManagement#achievementResetResponse`.
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
   * Flag to indicate if the requested update actually occurred.
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
class_alias(AchievementResetResponse::class, 'Google_Service_GamesManagement_AchievementResetResponse');
