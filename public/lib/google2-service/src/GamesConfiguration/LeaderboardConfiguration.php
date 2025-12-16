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

namespace Google\Service\GamesConfiguration;

class LeaderboardConfiguration extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const SCORE_ORDER_SCORE_ORDER_UNSPECIFIED = 'SCORE_ORDER_UNSPECIFIED';
  /**
   * Larger scores posted are ranked higher.
   */
  public const SCORE_ORDER_LARGER_IS_BETTER = 'LARGER_IS_BETTER';
  /**
   * Smaller scores posted are ranked higher.
   */
  public const SCORE_ORDER_SMALLER_IS_BETTER = 'SMALLER_IS_BETTER';
  protected $draftType = LeaderboardConfigurationDetail::class;
  protected $draftDataType = '';
  /**
   * The ID of the leaderboard.
   *
   * @var string
   */
  public $id;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `gamesConfiguration#leaderboardConfiguration`.
   *
   * @var string
   */
  public $kind;
  protected $publishedType = LeaderboardConfigurationDetail::class;
  protected $publishedDataType = '';
  /**
   * Maximum score that can be posted to this leaderboard.
   *
   * @var string
   */
  public $scoreMax;
  /**
   * Minimum score that can be posted to this leaderboard.
   *
   * @var string
   */
  public $scoreMin;
  /**
   * @var string
   */
  public $scoreOrder;
  /**
   * The token for this resource.
   *
   * @var string
   */
  public $token;

  /**
   * The draft data of the leaderboard.
   *
   * @param LeaderboardConfigurationDetail $draft
   */
  public function setDraft(LeaderboardConfigurationDetail $draft)
  {
    $this->draft = $draft;
  }
  /**
   * @return LeaderboardConfigurationDetail
   */
  public function getDraft()
  {
    return $this->draft;
  }
  /**
   * The ID of the leaderboard.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `gamesConfiguration#leaderboardConfiguration`.
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
   * The read-only published data of the leaderboard.
   *
   * @param LeaderboardConfigurationDetail $published
   */
  public function setPublished(LeaderboardConfigurationDetail $published)
  {
    $this->published = $published;
  }
  /**
   * @return LeaderboardConfigurationDetail
   */
  public function getPublished()
  {
    return $this->published;
  }
  /**
   * Maximum score that can be posted to this leaderboard.
   *
   * @param string $scoreMax
   */
  public function setScoreMax($scoreMax)
  {
    $this->scoreMax = $scoreMax;
  }
  /**
   * @return string
   */
  public function getScoreMax()
  {
    return $this->scoreMax;
  }
  /**
   * Minimum score that can be posted to this leaderboard.
   *
   * @param string $scoreMin
   */
  public function setScoreMin($scoreMin)
  {
    $this->scoreMin = $scoreMin;
  }
  /**
   * @return string
   */
  public function getScoreMin()
  {
    return $this->scoreMin;
  }
  /**
   * @param self::SCORE_ORDER_* $scoreOrder
   */
  public function setScoreOrder($scoreOrder)
  {
    $this->scoreOrder = $scoreOrder;
  }
  /**
   * @return self::SCORE_ORDER_*
   */
  public function getScoreOrder()
  {
    return $this->scoreOrder;
  }
  /**
   * The token for this resource.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LeaderboardConfiguration::class, 'Google_Service_GamesConfiguration_LeaderboardConfiguration');
