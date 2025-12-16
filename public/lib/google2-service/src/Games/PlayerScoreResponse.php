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

class PlayerScoreResponse extends \Google\Collection
{
  protected $collection_key = 'unbeatenScores';
  /**
   * The time spans where the submitted score is better than the existing score
   * for that time span.
   *
   * @var string[]
   */
  public $beatenScoreTimeSpans;
  /**
   * The formatted value of the submitted score.
   *
   * @var string
   */
  public $formattedScore;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#playerScoreResponse`.
   *
   * @var string
   */
  public $kind;
  /**
   * The leaderboard ID that this score was submitted to.
   *
   * @var string
   */
  public $leaderboardId;
  /**
   * Additional information about this score. Values will contain no more than
   * 64 URI-safe characters as defined by section 2.3 of RFC 3986.
   *
   * @var string
   */
  public $scoreTag;
  protected $unbeatenScoresType = PlayerScore::class;
  protected $unbeatenScoresDataType = 'array';

  /**
   * The time spans where the submitted score is better than the existing score
   * for that time span.
   *
   * @param string[] $beatenScoreTimeSpans
   */
  public function setBeatenScoreTimeSpans($beatenScoreTimeSpans)
  {
    $this->beatenScoreTimeSpans = $beatenScoreTimeSpans;
  }
  /**
   * @return string[]
   */
  public function getBeatenScoreTimeSpans()
  {
    return $this->beatenScoreTimeSpans;
  }
  /**
   * The formatted value of the submitted score.
   *
   * @param string $formattedScore
   */
  public function setFormattedScore($formattedScore)
  {
    $this->formattedScore = $formattedScore;
  }
  /**
   * @return string
   */
  public function getFormattedScore()
  {
    return $this->formattedScore;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#playerScoreResponse`.
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
   * The leaderboard ID that this score was submitted to.
   *
   * @param string $leaderboardId
   */
  public function setLeaderboardId($leaderboardId)
  {
    $this->leaderboardId = $leaderboardId;
  }
  /**
   * @return string
   */
  public function getLeaderboardId()
  {
    return $this->leaderboardId;
  }
  /**
   * Additional information about this score. Values will contain no more than
   * 64 URI-safe characters as defined by section 2.3 of RFC 3986.
   *
   * @param string $scoreTag
   */
  public function setScoreTag($scoreTag)
  {
    $this->scoreTag = $scoreTag;
  }
  /**
   * @return string
   */
  public function getScoreTag()
  {
    return $this->scoreTag;
  }
  /**
   * The scores in time spans that have not been beaten. As an example, the
   * submitted score may be better than the player's `DAILY` score, but not
   * better than the player's scores for the `WEEKLY` or `ALL_TIME` time spans.
   *
   * @param PlayerScore[] $unbeatenScores
   */
  public function setUnbeatenScores($unbeatenScores)
  {
    $this->unbeatenScores = $unbeatenScores;
  }
  /**
   * @return PlayerScore[]
   */
  public function getUnbeatenScores()
  {
    return $this->unbeatenScores;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlayerScoreResponse::class, 'Google_Service_Games_PlayerScoreResponse');
