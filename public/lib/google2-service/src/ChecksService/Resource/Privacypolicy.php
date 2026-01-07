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

namespace Google\Service\ChecksService\Resource;

use Google\Service\ChecksService\AnalyzePrivacyPolicyRequest;
use Google\Service\ChecksService\AnalyzePrivacyPolicyResponse;

/**
 * The "privacypolicy" collection of methods.
 * Typical usage is:
 *  <code>
 *   $checksService = new Google\Service\ChecksService(...);
 *   $privacypolicy = $checksService->privacypolicy;
 *  </code>
 */
class Privacypolicy extends \Google\Service\Resource
{
  /**
   * Performs a synchronous analysis of a privacy policy, where the policy content
   * is mapped to privacy categories, data types, and purposes.
   * (privacypolicy.analyze)
   *
   * @param AnalyzePrivacyPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AnalyzePrivacyPolicyResponse
   */
  public function analyze(AnalyzePrivacyPolicyRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('analyze', [$params], AnalyzePrivacyPolicyResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Privacypolicy::class, 'Google_Service_ChecksService_Resource_Privacypolicy');
