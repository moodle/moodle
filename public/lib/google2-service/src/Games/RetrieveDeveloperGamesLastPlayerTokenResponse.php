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

class RetrieveDeveloperGamesLastPlayerTokenResponse extends \Google\Model
{
  protected $gamePlayerTokenType = GamePlayerToken::class;
  protected $gamePlayerTokenDataType = '';

  /**
   * The recall token associated with the requested PGS Player principal. It can
   * be unset if there is no recall token associated with the requested
   * principal.
   *
   * @param GamePlayerToken $gamePlayerToken
   */
  public function setGamePlayerToken(GamePlayerToken $gamePlayerToken)
  {
    $this->gamePlayerToken = $gamePlayerToken;
  }
  /**
   * @return GamePlayerToken
   */
  public function getGamePlayerToken()
  {
    return $this->gamePlayerToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RetrieveDeveloperGamesLastPlayerTokenResponse::class, 'Google_Service_Games_RetrieveDeveloperGamesLastPlayerTokenResponse');
