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

class ScoreSubmission extends \Google\Model
{
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#scoreSubmission`.
   *
   * @var string
   */
  public $kind;
  /**
   * The leaderboard this score is being submitted to.
   *
   * @var string
   */
  public $leaderboardId;
  /**
   * The new score being submitted.
   *
   * @var string
   */
  public $score;
  /**
   * Additional information about this score. Values will contain no more than
   * 64 URI-safe characters as defined by section 2.3 of RFC 3986.
   *
   * @var string
   */
  public $scoreTag;
  /**
   * Signature Values will contain URI-safe characters as defined by section 2.3
   * of RFC 3986.
   *
   * @var string
   */
  public $signature;

  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#scoreSubmission`.
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
   * The leaderboard this score is being submitted to.
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
   * The new score being submitted.
   *
   * @param string $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return string
   */
  public function getScore()
  {
    return $this->score;
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
   * Signature Values will contain URI-safe characters as defined by section 2.3
   * of RFC 3986.
   *
   * @param string $signature
   */
  public function setSignature($signature)
  {
    $this->signature = $signature;
  }
  /**
   * @return string
   */
  public function getSignature()
  {
    return $this->signature;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScoreSubmission::class, 'Google_Service_Games_ScoreSubmission');
