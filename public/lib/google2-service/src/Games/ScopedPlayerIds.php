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

class ScopedPlayerIds extends \Google\Model
{
  /**
   * Identifier of the player across all games of the given developer. Every
   * player has the same developer_player_key in all games of one developer.
   * Developer player key changes for the game if the game is transferred to
   * another developer. Note that game_player_id will stay unchanged.
   *
   * @var string
   */
  public $developerPlayerKey;
  /**
   * Game-scoped player identifier. This is the same id that is returned in
   * GetPlayer game_player_id field.
   *
   * @var string
   */
  public $gamePlayerId;

  /**
   * Identifier of the player across all games of the given developer. Every
   * player has the same developer_player_key in all games of one developer.
   * Developer player key changes for the game if the game is transferred to
   * another developer. Note that game_player_id will stay unchanged.
   *
   * @param string $developerPlayerKey
   */
  public function setDeveloperPlayerKey($developerPlayerKey)
  {
    $this->developerPlayerKey = $developerPlayerKey;
  }
  /**
   * @return string
   */
  public function getDeveloperPlayerKey()
  {
    return $this->developerPlayerKey;
  }
  /**
   * Game-scoped player identifier. This is the same id that is returned in
   * GetPlayer game_player_id field.
   *
   * @param string $gamePlayerId
   */
  public function setGamePlayerId($gamePlayerId)
  {
    $this->gamePlayerId = $gamePlayerId;
  }
  /**
   * @return string
   */
  public function getGamePlayerId()
  {
    return $this->gamePlayerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScopedPlayerIds::class, 'Google_Service_Games_ScopedPlayerIds');
