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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickRankingParams extends \Google\Model
{
  /**
   * Unknown (default).
   */
  public const PRIORITY_UNKNOWN = 'UNKNOWN';
  /**
   * Critical.
   */
  public const PRIORITY_CRITICAL = 'CRITICAL';
  /**
   * Important.
   */
  public const PRIORITY_IMPORTANT = 'IMPORTANT';
  /**
   * High.
   */
  public const PRIORITY_HIGH = 'HIGH';
  /**
   * Normal.
   */
  public const PRIORITY_NORMAL = 'NORMAL';
  /**
   * Best effort.
   */
  public const PRIORITY_BEST_EFFORT = 'BEST_EFFORT';
  /**
   * Fixed, i.e., the card is time sensitive.
   */
  public const TYPE_FIXED = 'FIXED';
  /**
   * Flexible, i.e., the card is not time sensitive.
   */
  public const TYPE_FLEXIBLE = 'FLEXIBLE';
  /**
   * The end-time that this object will expect to occur. If the type is marked
   * as FIXED, then this end-time will persist after bidding. If the type is
   * marked as FLEXIBLE, this field is NOT expected to be filled and will be
   * filled in after it has won a bid. Expected to be set when type is set to
   * FIXED.
   *
   * @var string
   */
  public $endTimeMs;
  /**
   * The priority to determine between objects that have the same start_time_ms
   * The lower-value of priority == ranked higher. Max-priority = 0. Expected to
   * be set for all types.
   *
   * @var string
   */
  public $priority;
  /**
   * The score of the card to be used to break priority-ties
   *
   * @var float
   */
  public $score;
  /**
   * The span that this card will take in the stream Expected to be set when
   * type is set to FLEXIBLE.
   *
   * @var string
   */
  public $spanMs;
  /**
   * The start-time that this object will bid-for If the type is marked as
   * FIXED, then this start-time will persist after bidding. If the type is
   * marked as FLEXIBLE, then it will occur at the given time or sometime after
   * the requested time. Expected to be set for all types.
   *
   * @var string
   */
  public $startTimeMs;
  /**
   * The packing type of this object.
   *
   * @var string
   */
  public $type;

  /**
   * The end-time that this object will expect to occur. If the type is marked
   * as FIXED, then this end-time will persist after bidding. If the type is
   * marked as FLEXIBLE, this field is NOT expected to be filled and will be
   * filled in after it has won a bid. Expected to be set when type is set to
   * FIXED.
   *
   * @param string $endTimeMs
   */
  public function setEndTimeMs($endTimeMs)
  {
    $this->endTimeMs = $endTimeMs;
  }
  /**
   * @return string
   */
  public function getEndTimeMs()
  {
    return $this->endTimeMs;
  }
  /**
   * The priority to determine between objects that have the same start_time_ms
   * The lower-value of priority == ranked higher. Max-priority = 0. Expected to
   * be set for all types.
   *
   * Accepted values: UNKNOWN, CRITICAL, IMPORTANT, HIGH, NORMAL, BEST_EFFORT
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * The score of the card to be used to break priority-ties
   *
   * @param float $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
  /**
   * The span that this card will take in the stream Expected to be set when
   * type is set to FLEXIBLE.
   *
   * @param string $spanMs
   */
  public function setSpanMs($spanMs)
  {
    $this->spanMs = $spanMs;
  }
  /**
   * @return string
   */
  public function getSpanMs()
  {
    return $this->spanMs;
  }
  /**
   * The start-time that this object will bid-for If the type is marked as
   * FIXED, then this start-time will persist after bidding. If the type is
   * marked as FLEXIBLE, then it will occur at the given time or sometime after
   * the requested time. Expected to be set for all types.
   *
   * @param string $startTimeMs
   */
  public function setStartTimeMs($startTimeMs)
  {
    $this->startTimeMs = $startTimeMs;
  }
  /**
   * @return string
   */
  public function getStartTimeMs()
  {
    return $this->startTimeMs;
  }
  /**
   * The packing type of this object.
   *
   * Accepted values: FIXED, FLEXIBLE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickRankingParams::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickRankingParams');
