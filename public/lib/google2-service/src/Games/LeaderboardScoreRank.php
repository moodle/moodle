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

class LeaderboardScoreRank extends \Google\Model
{
  /**
   * The number of scores in the leaderboard as a string.
   *
   * @var string
   */
  public $formattedNumScores;
  /**
   * The rank in the leaderboard as a string.
   *
   * @var string
   */
  public $formattedRank;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#leaderboardScoreRank`.
   *
   * @var string
   */
  public $kind;
  /**
   * The number of scores in the leaderboard.
   *
   * @var string
   */
  public $numScores;
  /**
   * The rank in the leaderboard.
   *
   * @var string
   */
  public $rank;

  /**
   * The number of scores in the leaderboard as a string.
   *
   * @param string $formattedNumScores
   */
  public function setFormattedNumScores($formattedNumScores)
  {
    $this->formattedNumScores = $formattedNumScores;
  }
  /**
   * @return string
   */
  public function getFormattedNumScores()
  {
    return $this->formattedNumScores;
  }
  /**
   * The rank in the leaderboard as a string.
   *
   * @param string $formattedRank
   */
  public function setFormattedRank($formattedRank)
  {
    $this->formattedRank = $formattedRank;
  }
  /**
   * @return string
   */
  public function getFormattedRank()
  {
    return $this->formattedRank;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#leaderboardScoreRank`.
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
   * The number of scores in the leaderboard.
   *
   * @param string $numScores
   */
  public function setNumScores($numScores)
  {
    $this->numScores = $numScores;
  }
  /**
   * @return string
   */
  public function getNumScores()
  {
    return $this->numScores;
  }
  /**
   * The rank in the leaderboard.
   *
   * @param string $rank
   */
  public function setRank($rank)
  {
    $this->rank = $rank;
  }
  /**
   * @return string
   */
  public function getRank()
  {
    return $this->rank;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LeaderboardScoreRank::class, 'Google_Service_Games_LeaderboardScoreRank');
