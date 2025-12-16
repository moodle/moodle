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

class LinkPersonaResponse extends \Google\Model
{
  /**
   * The link specified in the request was created.
   */
  public const STATE_LINK_CREATED = 'LINK_CREATED';
  /**
   * The link specified in the request was not created because already existing
   * links would result in the new link violating the specified
   * `RecallTokensCardinalityConstraint` if created.
   */
  public const STATE_PERSONA_OR_PLAYER_ALREADY_LINKED = 'PERSONA_OR_PLAYER_ALREADY_LINKED';
  /**
   * Output only. State of a persona linking attempt.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. State of a persona linking attempt.
   *
   * Accepted values: LINK_CREATED, PERSONA_OR_PLAYER_ALREADY_LINKED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LinkPersonaResponse::class, 'Google_Service_Games_LinkPersonaResponse');
