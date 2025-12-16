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

class LeaderboardEntry extends \Google\Model
{
  /**
   * The score is an all-time score.
   */
  public const TIME_SPAN_ALL_TIME = 'ALL_TIME';
  /**
   * The score is a weekly score.
   */
  public const TIME_SPAN_WEEKLY = 'WEEKLY';
  /**
   * The score is a daily score.
   */
  public const TIME_SPAN_DAILY = 'DAILY';
  /**
   * The localized string for the numerical value of this score.
   *
   * @var string
   */
  public $formattedScore;
  /**
   * The localized string for the rank of this score for this leaderboard.
   *
   * @var string
   */
  public $formattedScoreRank;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#leaderboardEntry`.
   *
   * @var string
   */
  public $kind;
  protected $playerType = Player::class;
  protected $playerDataType = '';
  /**
   * The rank of this score for this leaderboard.
   *
   * @var string
   */
  public $scoreRank;
  /**
   * Additional information about the score. Values must contain no more than 64
   * URI-safe characters as defined by section 2.3 of RFC 3986.
   *
   * @var string
   */
  public $scoreTag;
  /**
   * The numerical value of this score.
   *
   * @var string
   */
  public $scoreValue;
  /**
   * The time span of this high score.
   *
   * @var string
   */
  public $timeSpan;
  /**
   * The timestamp at which this score was recorded, in milliseconds since the
   * epoch in UTC.
   *
   * @var string
   */
  public $writeTimestampMillis;

  /**
   * The localized string for the numerical value of this score.
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
   * The localized string for the rank of this score for this leaderboard.
   *
   * @param string $formattedScoreRank
   */
  public function setFormattedScoreRank($formattedScoreRank)
  {
    $this->formattedScoreRank = $formattedScoreRank;
  }
  /**
   * @return string
   */
  public function getFormattedScoreRank()
  {
    return $this->formattedScoreRank;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#leaderboardEntry`.
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
   * The player who holds this score.
   *
   * @param Player $player
   */
  public function setPlayer(Player $player)
  {
    $this->player = $player;
  }
  /**
   * @return Player
   */
  public function getPlayer()
  {
    return $this->player;
  }
  /**
   * The rank of this score for this leaderboard.
   *
   * @param string $scoreRank
   */
  public function setScoreRank($scoreRank)
  {
    $this->scoreRank = $scoreRank;
  }
  /**
   * @return string
   */
  public function getScoreRank()
  {
    return $this->scoreRank;
  }
  /**
   * Additional information about the score. Values must contain no more than 64
   * URI-safe characters as defined by section 2.3 of RFC 3986.
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
   * The numerical value of this score.
   *
   * @param string $scoreValue
   */
  public function setScoreValue($scoreValue)
  {
    $this->scoreValue = $scoreValue;
  }
  /**
   * @return string
   */
  public function getScoreValue()
  {
    return $this->scoreValue;
  }
  /**
   * The time span of this high score.
   *
   * Accepted values: ALL_TIME, WEEKLY, DAILY
   *
   * @param self::TIME_SPAN_* $timeSpan
   */
  public function setTimeSpan($timeSpan)
  {
    $this->timeSpan = $timeSpan;
  }
  /**
   * @return self::TIME_SPAN_*
   */
  public function getTimeSpan()
  {
    return $this->timeSpan;
  }
  /**
   * The timestamp at which this score was recorded, in milliseconds since the
   * epoch in UTC.
   *
   * @param string $writeTimestampMillis
   */
  public function setWriteTimestampMillis($writeTimestampMillis)
  {
    $this->writeTimestampMillis = $writeTimestampMillis;
  }
  /**
   * @return string
   */
  public function getWriteTimestampMillis()
  {
    return $this->writeTimestampMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LeaderboardEntry::class, 'Google_Service_Games_LeaderboardEntry');
