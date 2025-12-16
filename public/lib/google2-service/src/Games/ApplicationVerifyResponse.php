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

class ApplicationVerifyResponse extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "alternatePlayerId" => "alternate_player_id",
        "playerId" => "player_id",
  ];
  /**
   * An alternate ID that was once used for the player that was issued the auth
   * token used in this request. (This field is not normally populated.)
   *
   * @var string
   */
  public $alternatePlayerId;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#applicationVerifyResponse`.
   *
   * @var string
   */
  public $kind;
  /**
   * The ID of the player that was issued the auth token used in this request.
   *
   * @var string
   */
  public $playerId;

  /**
   * An alternate ID that was once used for the player that was issued the auth
   * token used in this request. (This field is not normally populated.)
   *
   * @param string $alternatePlayerId
   */
  public function setAlternatePlayerId($alternatePlayerId)
  {
    $this->alternatePlayerId = $alternatePlayerId;
  }
  /**
   * @return string
   */
  public function getAlternatePlayerId()
  {
    return $this->alternatePlayerId;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#applicationVerifyResponse`.
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
   * The ID of the player that was issued the auth token used in this request.
   *
   * @param string $playerId
   */
  public function setPlayerId($playerId)
  {
    $this->playerId = $playerId;
  }
  /**
   * @return string
   */
  public function getPlayerId()
  {
    return $this->playerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationVerifyResponse::class, 'Google_Service_Games_ApplicationVerifyResponse');
