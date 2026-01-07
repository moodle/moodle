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

class StatsResponse extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "avgSessionLengthMinutes" => "avg_session_length_minutes",
        "churnProbability" => "churn_probability",
        "daysSinceLastPlayed" => "days_since_last_played",
        "highSpenderProbability" => "high_spender_probability",
        "numPurchases" => "num_purchases",
        "numSessions" => "num_sessions",
        "numSessionsPercentile" => "num_sessions_percentile",
        "spendPercentile" => "spend_percentile",
        "spendProbability" => "spend_probability",
        "totalSpendNext28Days" => "total_spend_next_28_days",
  ];
  /**
   * Average session length in minutes of the player. E.g., 1, 30, 60, ... . Not
   * populated if there is not enough information.
   *
   * @var float
   */
  public $avgSessionLengthMinutes;
  /**
   * The probability of the player not returning to play the game in the next
   * day. E.g., 0, 0.1, 0.5, ..., 1.0. Not populated if there is not enough
   * information.
   *
   * @var float
   */
  public $churnProbability;
  /**
   * Number of days since the player last played this game. E.g., 0, 1, 5, 10,
   * ... . Not populated if there is not enough information.
   *
   * @var int
   */
  public $daysSinceLastPlayed;
  /**
   * The probability of the player going to spend beyond a threshold amount of
   * money. E.g., 0, 0.25, 0.50, 0.75. Not populated if there is not enough
   * information.
   *
   * @var float
   */
  public $highSpenderProbability;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#statsResponse`.
   *
   * @var string
   */
  public $kind;
  /**
   * Number of in-app purchases made by the player in this game. E.g., 0, 1, 5,
   * 10, ... . Not populated if there is not enough information.
   *
   * @var int
   */
  public $numPurchases;
  /**
   * The approximate number of sessions of the player within the last 28 days,
   * where a session begins when the player is connected to Play Games Services
   * and ends when they are disconnected. E.g., 0, 1, 5, 10, ... . Not populated
   * if there is not enough information.
   *
   * @var int
   */
  public $numSessions;
  /**
   * The approximation of the sessions percentile of the player within the last
   * 30 days, where a session begins when the player is connected to Play Games
   * Services and ends when they are disconnected. E.g., 0, 0.25, 0.5, 0.75. Not
   * populated if there is not enough information.
   *
   * @var float
   */
  public $numSessionsPercentile;
  /**
   * The approximate spend percentile of the player in this game. E.g., 0, 0.25,
   * 0.5, 0.75. Not populated if there is not enough information.
   *
   * @var float
   */
  public $spendPercentile;
  /**
   * The probability of the player going to spend the game in the next seven
   * days. E.g., 0, 0.25, 0.50, 0.75. Not populated if there is not enough
   * information.
   *
   * @var float
   */
  public $spendProbability;
  /**
   * The predicted amount of money that the player going to spend in the next 28
   * days. E.g., 1, 30, 60, ... . Not populated if there is not enough
   * information.
   *
   * @var float
   */
  public $totalSpendNext28Days;

  /**
   * Average session length in minutes of the player. E.g., 1, 30, 60, ... . Not
   * populated if there is not enough information.
   *
   * @param float $avgSessionLengthMinutes
   */
  public function setAvgSessionLengthMinutes($avgSessionLengthMinutes)
  {
    $this->avgSessionLengthMinutes = $avgSessionLengthMinutes;
  }
  /**
   * @return float
   */
  public function getAvgSessionLengthMinutes()
  {
    return $this->avgSessionLengthMinutes;
  }
  /**
   * The probability of the player not returning to play the game in the next
   * day. E.g., 0, 0.1, 0.5, ..., 1.0. Not populated if there is not enough
   * information.
   *
   * @param float $churnProbability
   */
  public function setChurnProbability($churnProbability)
  {
    $this->churnProbability = $churnProbability;
  }
  /**
   * @return float
   */
  public function getChurnProbability()
  {
    return $this->churnProbability;
  }
  /**
   * Number of days since the player last played this game. E.g., 0, 1, 5, 10,
   * ... . Not populated if there is not enough information.
   *
   * @param int $daysSinceLastPlayed
   */
  public function setDaysSinceLastPlayed($daysSinceLastPlayed)
  {
    $this->daysSinceLastPlayed = $daysSinceLastPlayed;
  }
  /**
   * @return int
   */
  public function getDaysSinceLastPlayed()
  {
    return $this->daysSinceLastPlayed;
  }
  /**
   * The probability of the player going to spend beyond a threshold amount of
   * money. E.g., 0, 0.25, 0.50, 0.75. Not populated if there is not enough
   * information.
   *
   * @param float $highSpenderProbability
   */
  public function setHighSpenderProbability($highSpenderProbability)
  {
    $this->highSpenderProbability = $highSpenderProbability;
  }
  /**
   * @return float
   */
  public function getHighSpenderProbability()
  {
    return $this->highSpenderProbability;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#statsResponse`.
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
   * Number of in-app purchases made by the player in this game. E.g., 0, 1, 5,
   * 10, ... . Not populated if there is not enough information.
   *
   * @param int $numPurchases
   */
  public function setNumPurchases($numPurchases)
  {
    $this->numPurchases = $numPurchases;
  }
  /**
   * @return int
   */
  public function getNumPurchases()
  {
    return $this->numPurchases;
  }
  /**
   * The approximate number of sessions of the player within the last 28 days,
   * where a session begins when the player is connected to Play Games Services
   * and ends when they are disconnected. E.g., 0, 1, 5, 10, ... . Not populated
   * if there is not enough information.
   *
   * @param int $numSessions
   */
  public function setNumSessions($numSessions)
  {
    $this->numSessions = $numSessions;
  }
  /**
   * @return int
   */
  public function getNumSessions()
  {
    return $this->numSessions;
  }
  /**
   * The approximation of the sessions percentile of the player within the last
   * 30 days, where a session begins when the player is connected to Play Games
   * Services and ends when they are disconnected. E.g., 0, 0.25, 0.5, 0.75. Not
   * populated if there is not enough information.
   *
   * @param float $numSessionsPercentile
   */
  public function setNumSessionsPercentile($numSessionsPercentile)
  {
    $this->numSessionsPercentile = $numSessionsPercentile;
  }
  /**
   * @return float
   */
  public function getNumSessionsPercentile()
  {
    return $this->numSessionsPercentile;
  }
  /**
   * The approximate spend percentile of the player in this game. E.g., 0, 0.25,
   * 0.5, 0.75. Not populated if there is not enough information.
   *
   * @param float $spendPercentile
   */
  public function setSpendPercentile($spendPercentile)
  {
    $this->spendPercentile = $spendPercentile;
  }
  /**
   * @return float
   */
  public function getSpendPercentile()
  {
    return $this->spendPercentile;
  }
  /**
   * The probability of the player going to spend the game in the next seven
   * days. E.g., 0, 0.25, 0.50, 0.75. Not populated if there is not enough
   * information.
   *
   * @param float $spendProbability
   */
  public function setSpendProbability($spendProbability)
  {
    $this->spendProbability = $spendProbability;
  }
  /**
   * @return float
   */
  public function getSpendProbability()
  {
    return $this->spendProbability;
  }
  /**
   * The predicted amount of money that the player going to spend in the next 28
   * days. E.g., 1, 30, 60, ... . Not populated if there is not enough
   * information.
   *
   * @param float $totalSpendNext28Days
   */
  public function setTotalSpendNext28Days($totalSpendNext28Days)
  {
    $this->totalSpendNext28Days = $totalSpendNext28Days;
  }
  /**
   * @return float
   */
  public function getTotalSpendNext28Days()
  {
    return $this->totalSpendNext28Days;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StatsResponse::class, 'Google_Service_Games_StatsResponse');
