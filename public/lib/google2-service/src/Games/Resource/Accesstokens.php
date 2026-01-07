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

use Google\Service\Games\GeneratePlayGroupingApiTokenResponse;
use Google\Service\Games\GenerateRecallPlayGroupingApiTokenResponse;

/**
 * The "accesstokens" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gamesService = new Google\Service\Games(...);
 *   $accesstokens = $gamesService->accesstokens;
 *  </code>
 */
class Accesstokens extends \Google\Service\Resource
{
  /**
   * Generates a Play Grouping API token for the PGS user identified by the
   * attached credential. (accesstokens.generatePlayGroupingApiToken)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string packageName Required. App package name to generate the
   * token for (e.g. com.example.mygame).
   * @opt_param string persona Required. Persona to associate with the token.
   * Persona is a developer-provided stable identifier of the user. Must be
   * deterministically generated (e.g. as a one-way hash) from the user account ID
   * and user profile ID (if the app has the concept), according to the
   * developer's own user identity system.
   * @return GeneratePlayGroupingApiTokenResponse
   * @throws \Google\Service\Exception
   */
  public function generatePlayGroupingApiToken($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('generatePlayGroupingApiToken', [$params], GeneratePlayGroupingApiTokenResponse::class);
  }
  /**
   * Generates a Play Grouping API token for the PGS user identified by the Recall
   * session ID provided in the request.
   * (accesstokens.generateRecallPlayGroupingApiToken)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string packageName Required. App package name to generate the
   * token for (e.g. com.example.mygame).
   * @opt_param string persona Required. Persona to associate with the token.
   * Persona is a developer-provided stable identifier of the user. Must be
   * deterministically generated (e.g. as a one-way hash) from the user account ID
   * and user profile ID (if the app has the concept), according to the
   * developer's own user identity system.
   * @opt_param string recallSessionId Required. Opaque server-generated string
   * that encodes all the necessary information to identify the PGS player /
   * Google user and application. See
   * https://developer.android.com/games/pgs/recall/recall-setup on how to
   * integrate with Recall and get session ID.
   * @return GenerateRecallPlayGroupingApiTokenResponse
   * @throws \Google\Service\Exception
   */
  public function generateRecallPlayGroupingApiToken($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('generateRecallPlayGroupingApiToken', [$params], GenerateRecallPlayGroupingApiTokenResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Accesstokens::class, 'Google_Service_Games_Resource_Accesstokens');
