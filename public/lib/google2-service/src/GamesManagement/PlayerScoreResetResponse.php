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

class PlayerScoreResetResponse extends \Google\Collection
{
  protected $collection_key = 'resetScoreTimeSpans';
  /**
   * The ID of an leaderboard for which player state has been updated.
   *
   * @var string
   */
  public $definitionId;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `gamesManagement#playerScoreResetResponse`.
   *
   * @var string
   */
  public $kind;
  /**
   * The time spans of the updated score. Possible values are: - "`ALL_TIME`" -
   * The score is an all-time score. - "`WEEKLY`" - The score is a weekly score.
   * - "`DAILY`" - The score is a daily score.
   *
   * @var string[]
   */
  public $resetScoreTimeSpans;

  /**
   * The ID of an leaderboard for which player state has been updated.
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
   * string `gamesManagement#playerScoreResetResponse`.
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
   * The time spans of the updated score. Possible values are: - "`ALL_TIME`" -
   * The score is an all-time score. - "`WEEKLY`" - The score is a weekly score.
   * - "`DAILY`" - The score is a daily score.
   *
   * @param string[] $resetScoreTimeSpans
   */
  public function setResetScoreTimeSpans($resetScoreTimeSpans)
  {
    $this->resetScoreTimeSpans = $resetScoreTimeSpans;
  }
  /**
   * @return string[]
   */
  public function getResetScoreTimeSpans()
  {
    return $this->resetScoreTimeSpans;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlayerScoreResetResponse::class, 'Google_Service_GamesManagement_PlayerScoreResetResponse');
