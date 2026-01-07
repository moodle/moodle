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

class RetrieveGamesPlayerTokensResponse extends \Google\Collection
{
  protected $collection_key = 'gamePlayerTokens';
  protected $gamePlayerTokensType = GamePlayerToken::class;
  protected $gamePlayerTokensDataType = 'array';

  /**
   * The requested applications along with the recall tokens for the player. If
   * the player does not have recall tokens for an application, that application
   * is not included in the response.
   *
   * @param GamePlayerToken[] $gamePlayerTokens
   */
  public function setGamePlayerTokens($gamePlayerTokens)
  {
    $this->gamePlayerTokens = $gamePlayerTokens;
  }
  /**
   * @return GamePlayerToken[]
   */
  public function getGamePlayerTokens()
  {
    return $this->gamePlayerTokens;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RetrieveGamesPlayerTokensResponse::class, 'Google_Service_Games_RetrieveGamesPlayerTokensResponse');
