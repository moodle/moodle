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

namespace Google\Service\DataPortability\Resource;

use Google\Service\DataPortability\DataportabilityEmpty;
use Google\Service\DataPortability\ResetAuthorizationRequest;

/**
 * The "authorization" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataportabilityService = new Google\Service\DataPortability(...);
 *   $authorization = $dataportabilityService->authorization;
 *  </code>
 */
class Authorization extends \Google\Service\Resource
{
  /**
   * Revokes OAuth tokens and resets exhausted scopes for a user/project pair.
   * This method allows you to initiate a request after a new consent is granted.
   * This method also indicates that previous archives can be garbage collected.
   * You should call this method when all jobs are complete and all archives are
   * downloaded. Do not call it only when you start a new job.
   * (authorization.reset)
   *
   * @param ResetAuthorizationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DataportabilityEmpty
   * @throws \Google\Service\Exception
   */
  public function reset(ResetAuthorizationRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reset', [$params], DataportabilityEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Authorization::class, 'Google_Service_DataPortability_Resource_Authorization');
