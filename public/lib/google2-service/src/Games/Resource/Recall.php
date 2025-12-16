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

namespace Google\Service\Games\Resource;

use Google\Service\Games\LinkPersonaRequest;
use Google\Service\Games\LinkPersonaResponse;
use Google\Service\Games\ResetPersonaRequest;
use Google\Service\Games\ResetPersonaResponse;
use Google\Service\Games\RetrieveDeveloperGamesLastPlayerTokenResponse;
use Google\Service\Games\RetrieveGamesPlayerTokensResponse;
use Google\Service\Games\RetrievePlayerTokensResponse;
use Google\Service\Games\UnlinkPersonaRequest;
use Google\Service\Games\UnlinkPersonaResponse;

/**
 * The "recall" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gamesService = new Google\Service\Games(...);
 *   $recall = $gamesService->recall;
 *  </code>
 */
class Recall extends \Google\Service\Resource
{
  /**
   * Retrieve the Recall tokens from all requested games that is associated with
   * the PGS Player encoded in the provided recall session id. The API is only
   * available for users that have an active PGS Player profile.
   * (recall.gamesPlayerTokens)
   *
   * @param string $sessionId Required. Opaque server-generated string that
   * encodes all the necessary information to identify the PGS player / Google
   * user and application.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string applicationIds Required. The application IDs from the
   * Google Play developer console for the games to return scoped ids for.
   * @return RetrieveGamesPlayerTokensResponse
   * @throws \Google\Service\Exception
   */
  public function gamesPlayerTokens($sessionId, $optParams = [])
  {
    $params = ['sessionId' => $sessionId];
    $params = array_merge($params, $optParams);
    return $this->call('gamesPlayerTokens', [$params], RetrieveGamesPlayerTokensResponse::class);
  }
  /**
   * Retrieve the last Recall token from all developer games that is associated
   * with the PGS Player encoded in the provided recall session id. The API is
   * only available for users that have active PGS Player profile.
   * (recall.lastTokenFromAllDeveloperGames)
   *
   * @param string $sessionId Required. Opaque server-generated string that
   * encodes all the necessary information to identify the PGS player / Google
   * user and application.
   * @param array $optParams Optional parameters.
   * @return RetrieveDeveloperGamesLastPlayerTokenResponse
   * @throws \Google\Service\Exception
   */
  public function lastTokenFromAllDeveloperGames($sessionId, $optParams = [])
  {
    $params = ['sessionId' => $sessionId];
    $params = array_merge($params, $optParams);
    return $this->call('lastTokenFromAllDeveloperGames', [$params], RetrieveDeveloperGamesLastPlayerTokenResponse::class);
  }
  /**
   * Associate the PGS Player principal encoded in the provided recall session id
   * with an in-game account (recall.linkPersona)
   *
   * @param LinkPersonaRequest $postBody
   * @param array $optParams Optional parameters.
   * @return LinkPersonaResponse
   * @throws \Google\Service\Exception
   */
  public function linkPersona(LinkPersonaRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('linkPersona', [$params], LinkPersonaResponse::class);
  }
  /**
   * Delete all Recall tokens linking the given persona to any player (with or
   * without a profile). (recall.resetPersona)
   *
   * @param ResetPersonaRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ResetPersonaResponse
   * @throws \Google\Service\Exception
   */
  public function resetPersona(ResetPersonaRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resetPersona', [$params], ResetPersonaResponse::class);
  }
  /**
   * Retrieve all Recall tokens associated with the PGS Player encoded in the
   * provided recall session id. The API is only available for users that have
   * active PGS Player profile. (recall.retrieveTokens)
   *
   * @param string $sessionId Required. Opaque server-generated string that
   * encodes all the necessary information to identify the PGS player / Google
   * user and application.
   * @param array $optParams Optional parameters.
   * @return RetrievePlayerTokensResponse
   * @throws \Google\Service\Exception
   */
  public function retrieveTokens($sessionId, $optParams = [])
  {
    $params = ['sessionId' => $sessionId];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveTokens', [$params], RetrievePlayerTokensResponse::class);
  }
  /**
   * Delete a Recall token linking the PGS Player principal identified by the
   * Recall session and an in-game account identified either by the 'persona' or
   * by the token value. (recall.unlinkPersona)
   *
   * @param UnlinkPersonaRequest $postBody
   * @param array $optParams Optional parameters.
   * @return UnlinkPersonaResponse
   * @throws \Google\Service\Exception
   */
  public function unlinkPersona(UnlinkPersonaRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('unlinkPersona', [$params], UnlinkPersonaResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Recall::class, 'Google_Service_Games_Resource_Recall');
