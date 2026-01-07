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

class PlayerLeaderboardScore extends \Google\Model
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
  protected $internal_gapi_mappings = [
        "leaderboardId" => "leaderboard_id",
  ];
  protected $friendsRankType = LeaderboardScoreRank::class;
  protected $friendsRankDataType = '';
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#playerLeaderboardScore`.
   *
   * @var string
   */
  public $kind;
  /**
   * The ID of the leaderboard this score is in.
   *
   * @var string
   */
  public $leaderboardId;
  protected $publicRankType = LeaderboardScoreRank::class;
  protected $publicRankDataType = '';
  /**
   * The formatted value of this score.
   *
   * @var string
   */
  public $scoreString;
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
  protected $socialRankType = LeaderboardScoreRank::class;
  protected $socialRankDataType = '';
  /**
   * The time span of this score.
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
  public $writeTimestamp;

  /**
   * The rank of the score in the friends collection for this leaderboard.
   *
   * @param LeaderboardScoreRank $friendsRank
   */
  public function setFriendsRank(LeaderboardScoreRank $friendsRank)
  {
    $this->friendsRank = $friendsRank;
  }
  /**
   * @return LeaderboardScoreRank
   */
  public function getFriendsRank()
  {
    return $this->friendsRank;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#playerLeaderboardScore`.
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
   * The ID of the leaderboard this score is in.
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
   * The public rank of the score in this leaderboard. This object will not be
   * present if the user is not sharing their scores publicly.
   *
   * @param LeaderboardScoreRank $publicRank
   */
  public function setPublicRank(LeaderboardScoreRank $publicRank)
  {
    $this->publicRank = $publicRank;
  }
  /**
   * @return LeaderboardScoreRank
   */
  public function getPublicRank()
  {
    return $this->publicRank;
  }
  /**
   * The formatted value of this score.
   *
   * @param string $scoreString
   */
  public function setScoreString($scoreString)
  {
    $this->scoreString = $scoreString;
  }
  /**
   * @return string
   */
  public function getScoreString()
  {
    return $this->scoreString;
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
   * The social rank of the score in this leaderboard.
   *
   * @param LeaderboardScoreRank $socialRank
   */
  public function setSocialRank(LeaderboardScoreRank $socialRank)
  {
    $this->socialRank = $socialRank;
  }
  /**
   * @return LeaderboardScoreRank
   */
  public function getSocialRank()
  {
    return $this->socialRank;
  }
  /**
   * The time span of this score.
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
   * @param string $writeTimestamp
   */
  public function setWriteTimestamp($writeTimestamp)
  {
    $this->writeTimestamp = $writeTimestamp;
  }
  /**
   * @return string
   */
  public function getWriteTimestamp()
  {
    return $this->writeTimestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlayerLeaderboardScore::class, 'Google_Service_Games_PlayerLeaderboardScore');
