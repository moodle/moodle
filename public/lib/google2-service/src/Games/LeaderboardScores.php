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

class LeaderboardScores extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $itemsType = LeaderboardEntry::class;
  protected $itemsDataType = 'array';
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#leaderboardScores`.
   *
   * @var string
   */
  public $kind;
  /**
   * The pagination token for the next page of results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The total number of scores in the leaderboard.
   *
   * @var string
   */
  public $numScores;
  protected $playerScoreType = LeaderboardEntry::class;
  protected $playerScoreDataType = '';
  /**
   * The pagination token for the previous page of results.
   *
   * @var string
   */
  public $prevPageToken;

  /**
   * The scores in the leaderboard.
   *
   * @param LeaderboardEntry[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return LeaderboardEntry[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#leaderboardScores`.
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
   * The pagination token for the next page of results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * The total number of scores in the leaderboard.
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
   * The score of the requesting player on the leaderboard. The player's score
   * may appear both here and in the list of scores above. If you are viewing a
   * public leaderboard and the player is not sharing their gameplay information
   * publicly, the `scoreRank`and `formattedScoreRank` values will not be
   * present.
   *
   * @param LeaderboardEntry $playerScore
   */
  public function setPlayerScore(LeaderboardEntry $playerScore)
  {
    $this->playerScore = $playerScore;
  }
  /**
   * @return LeaderboardEntry
   */
  public function getPlayerScore()
  {
    return $this->playerScore;
  }
  /**
   * The pagination token for the previous page of results.
   *
   * @param string $prevPageToken
   */
  public function setPrevPageToken($prevPageToken)
  {
    $this->prevPageToken = $prevPageToken;
  }
  /**
   * @return string
   */
  public function getPrevPageToken()
  {
    return $this->prevPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LeaderboardScores::class, 'Google_Service_Games_LeaderboardScores');
