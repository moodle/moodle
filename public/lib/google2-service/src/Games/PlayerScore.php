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

class PlayerScore extends \Google\Model
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
   * The formatted score for this player score.
   *
   * @var string
   */
  public $formattedScore;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#playerScore`.
   *
   * @var string
   */
  public $kind;
  /**
   * The numerical value for this player score.
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
   * The time span for this player score.
   *
   * @var string
   */
  public $timeSpan;

  /**
   * The formatted score for this player score.
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
   * string `games#playerScore`.
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
   * The numerical value for this player score.
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
   * The time span for this player score.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlayerScore::class, 'Google_Service_Games_PlayerScore');
