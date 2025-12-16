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

use Google\Service\DataPortability\CheckAccessTypeRequest;
use Google\Service\DataPortability\CheckAccessTypeResponse;

/**
 * The "accessType" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataportabilityService = new Google\Service\DataPortability(...);
 *   $accessType = $dataportabilityService->accessType;
 *  </code>
 */
class AccessType extends \Google\Service\Resource
{
  /**
   * Gets the access type of the token. (accessType.check)
   *
   * @param CheckAccessTypeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CheckAccessTypeResponse
   * @throws \Google\Service\Exception
   */
  public function check(CheckAccessTypeRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('check', [$params], CheckAccessTypeResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessType::class, 'Google_Service_DataPortability_Resource_AccessType');
