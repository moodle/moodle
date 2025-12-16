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

namespace Google\Service\MyBusinessVerifications\Resource;

use Google\Service\MyBusinessVerifications\GenerateInstantVerificationTokenRequest;
use Google\Service\MyBusinessVerifications\GenerateInstantVerificationTokenResponse;

/**
 * The "verificationTokens" collection of methods.
 * Typical usage is:
 *  <code>
 *   $mybusinessverificationsService = new Google\Service\MyBusinessVerifications(...);
 *   $verificationTokens = $mybusinessverificationsService->verificationTokens;
 *  </code>
 */
class VerificationTokens extends \Google\Service\Resource
{
  /**
   * Generate a token for the provided location data to verify the location.
   * (verificationTokens.generate)
   *
   * @param GenerateInstantVerificationTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GenerateInstantVerificationTokenResponse
   * @throws \Google\Service\Exception
   */
  public function generate(GenerateInstantVerificationTokenRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generate', [$params], GenerateInstantVerificationTokenResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerificationTokens::class, 'Google_Service_MyBusinessVerifications_Resource_VerificationTokens');
