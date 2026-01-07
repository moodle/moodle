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

namespace Google\Service\PaymentsResellerSubscription\Resource;

use Google\Service\PaymentsResellerSubscription\GenerateUserSessionRequest;
use Google\Service\PaymentsResellerSubscription\GenerateUserSessionResponse;

/**
 * The "userSessions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $paymentsresellersubscriptionService = new Google\Service\PaymentsResellerSubscription(...);
 *   $userSessions = $paymentsresellersubscriptionService->partners_userSessions;
 *  </code>
 */
class PartnersUserSessions extends \Google\Service\Resource
{
  /**
   * This API replaces user authorized OAuth consent based APIs (Create, Entitle).
   * Issues a timed session token for the given user intent. You can use the
   * session token to redirect the user to Google to finish the signup flow. You
   * can re-generate new session token repeatedly for the same request if
   * necessary, regardless of the previous tokens being expired or not. By
   * default, the session token is valid for 1 hour. (userSessions.generate)
   *
   * @param string $parent Required. The parent, the partner that can resell.
   * Format: partners/{partner}
   * @param GenerateUserSessionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GenerateUserSessionResponse
   * @throws \Google\Service\Exception
   */
  public function generate($parent, GenerateUserSessionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generate', [$params], GenerateUserSessionResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartnersUserSessions::class, 'Google_Service_PaymentsResellerSubscription_Resource_PartnersUserSessions');
