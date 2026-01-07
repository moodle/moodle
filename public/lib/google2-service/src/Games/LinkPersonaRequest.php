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

class LinkPersonaRequest extends \Google\Model
{
  /**
   * 1:1 cardinality between in-game personas and Play Games Services players.
   * By the end of the linking operation only one entry for the player and the
   * persona should remain in the scope of the application. Whether a new link
   * is created or not when this constraint is specified is determined by the
   * chosen `ConflictingLinksResolutionPolicy`: * If `KEEP_EXISTING_LINKS` is
   * specified and the provided persona is already linked to a different player,
   * or the player is already linked to a different persona, no new link will be
   * created and the already existing link(s) will remain as is(are). * If
   * `CREATE_NEW_LINK` is specified and the provided persona is already linked
   * to a different player, or the player is already linked to another persona,
   * the older link(s) will be removed in favour of the new link being created.
   */
  public const CARDINALITY_CONSTRAINT_ONE_PERSONA_TO_ONE_PLAYER = 'ONE_PERSONA_TO_ONE_PLAYER';
  /**
   * If link(s) between a player and persona already exists which would result
   * in violating the specified `RecallTokensCardinalityConstraint` if the new
   * link was created, keep the already existing link(s). For example, if
   * Persona1-Player1 is already linked in the scope of application1 and a new
   * link Persona1-Player2 is attempted to be created in the scope of
   * application1, then the old link will remain and no new link will be added.
   * Note that if the already existing links do violate the specified policy
   * (which could occur if not all `LinkPersona` calls use the same
   * `RecallTokensCardinalityConstraint`) this policy will leave these
   * violations unresolved; in order to resolve conflicts, the {@link
   * `CREATE_NEW_LINK` policy needs to be used to rewrite links resolving
   * conflicts.
   */
  public const CONFLICTING_LINKS_RESOLUTION_POLICY_KEEP_EXISTING_LINKS = 'KEEP_EXISTING_LINKS';
  /**
   * If an existing link between a player and persona already exists which would
   * result in violating the specified `RecallTokensCardinalityConstraint` if
   * the new link was created, replace the already existing link(s) with the new
   * link. For example, if Persona1-Player1 is already linked in the scope of
   * application1 and a new link Persona1-Player2 is attempted to be created in
   * the scope of application1, then the old link will be removed and the new
   * link will be added to replace it.
   */
  public const CONFLICTING_LINKS_RESOLUTION_POLICY_CREATE_NEW_LINK = 'CREATE_NEW_LINK';
  /**
   * Required. Cardinality constraint to observe when linking a persona to a
   * player in the scope of a game.
   *
   * @var string
   */
  public $cardinalityConstraint;
  /**
   * Required. Resolution policy to apply when the linking of a persona to a
   * player would result in violating the specified cardinality constraint.
   *
   * @var string
   */
  public $conflictingLinksResolutionPolicy;
  /**
   * Input only. Optional expiration time.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Required. Stable identifier of the in-game account. Please refrain from re-
   * using the same persona for different games.
   *
   * @var string
   */
  public $persona;
  /**
   * Required. Opaque server-generated string that encodes all the necessary
   * information to identify the PGS player / Google user and application.
   *
   * @var string
   */
  public $sessionId;
  /**
   * Required. Value of the token to create. Opaque to Play Games and assumed to
   * be non-stable (encrypted with key rotation).
   *
   * @var string
   */
  public $token;
  /**
   * Input only. Optional time-to-live.
   *
   * @var string
   */
  public $ttl;

  /**
   * Required. Cardinality constraint to observe when linking a persona to a
   * player in the scope of a game.
   *
   * Accepted values: ONE_PERSONA_TO_ONE_PLAYER
   *
   * @param self::CARDINALITY_CONSTRAINT_* $cardinalityConstraint
   */
  public function setCardinalityConstraint($cardinalityConstraint)
  {
    $this->cardinalityConstraint = $cardinalityConstraint;
  }
  /**
   * @return self::CARDINALITY_CONSTRAINT_*
   */
  public function getCardinalityConstraint()
  {
    return $this->cardinalityConstraint;
  }
  /**
   * Required. Resolution policy to apply when the linking of a persona to a
   * player would result in violating the specified cardinality constraint.
   *
   * Accepted values: KEEP_EXISTING_LINKS, CREATE_NEW_LINK
   *
   * @param self::CONFLICTING_LINKS_RESOLUTION_POLICY_* $conflictingLinksResolutionPolicy
   */
  public function setConflictingLinksResolutionPolicy($conflictingLinksResolutionPolicy)
  {
    $this->conflictingLinksResolutionPolicy = $conflictingLinksResolutionPolicy;
  }
  /**
   * @return self::CONFLICTING_LINKS_RESOLUTION_POLICY_*
   */
  public function getConflictingLinksResolutionPolicy()
  {
    return $this->conflictingLinksResolutionPolicy;
  }
  /**
   * Input only. Optional expiration time.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Required. Stable identifier of the in-game account. Please refrain from re-
   * using the same persona for different games.
   *
   * @param string $persona
   */
  public function setPersona($persona)
  {
    $this->persona = $persona;
  }
  /**
   * @return string
   */
  public function getPersona()
  {
    return $this->persona;
  }
  /**
   * Required. Opaque server-generated string that encodes all the necessary
   * information to identify the PGS player / Google user and application.
   *
   * @param string $sessionId
   */
  public function setSessionId($sessionId)
  {
    $this->sessionId = $sessionId;
  }
  /**
   * @return string
   */
  public function getSessionId()
  {
    return $this->sessionId;
  }
  /**
   * Required. Value of the token to create. Opaque to Play Games and assumed to
   * be non-stable (encrypted with key rotation).
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
  /**
   * Input only. Optional time-to-live.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LinkPersonaRequest::class, 'Google_Service_Games_LinkPersonaRequest');
