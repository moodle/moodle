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

namespace Google\Service\PlayGrouping\Resource;

use Google\Service\PlayGrouping\VerifyTokenRequest;
use Google\Service\PlayGrouping\VerifyTokenResponse;

/**
 * The "tokens" collection of methods.
 * Typical usage is:
 *  <code>
 *   $playgroupingService = new Google\Service\PlayGrouping(...);
 *   $tokens = $playgroupingService->apps_tokens;
 *  </code>
 */
class AppsTokens extends \Google\Service\Resource
{
  /**
   * Verify an API token by asserting the app and persona it belongs to. The
   * verification is a protection against client-side attacks and will fail if the
   * contents of the token don't match the provided values. A token must be
   * verified before it can be used to manipulate user tags. (tokens.verify)
   *
   * @param string $appPackage Required. App the token belongs to. Format:
   * apps/{package_name}
   * @param string $token Required. The token to be verified. Format:
   * tokens/{token}
   * @param VerifyTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return VerifyTokenResponse
   * @throws \Google\Service\Exception
   */
  public function verify($appPackage, $token, VerifyTokenRequest $postBody, $optParams = [])
  {
    $params = ['appPackage' => $appPackage, 'token' => $token, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('verify', [$params], VerifyTokenResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppsTokens::class, 'Google_Service_PlayGrouping_Resource_AppsTokens');
