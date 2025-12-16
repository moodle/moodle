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

namespace Google\Service\AndroidEnterprise\Resource;

use Google\Service\AndroidEnterprise\EnrollmentToken;

/**
 * The "enrollmentTokens" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidenterpriseService = new Google\Service\AndroidEnterprise(...);
 *   $enrollmentTokens = $androidenterpriseService->enrollmentTokens;
 *  </code>
 */
class EnrollmentTokens extends \Google\Service\Resource
{
  /**
   * Returns a token for device enrollment. The DPC can encode this token within
   * the QR/NFC/zero-touch enrollment payload or fetch it before calling the on-
   * device API to authenticate the user. The token can be generated for each
   * device or reused across multiple devices. (enrollmentTokens.create)
   *
   * @param string $enterpriseId Required. The ID of the enterprise.
   * @param EnrollmentToken $postBody
   * @param array $optParams Optional parameters.
   * @return EnrollmentToken
   * @throws \Google\Service\Exception
   */
  public function create($enterpriseId, EnrollmentToken $postBody, $optParams = [])
  {
    $params = ['enterpriseId' => $enterpriseId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], EnrollmentToken::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnrollmentTokens::class, 'Google_Service_AndroidEnterprise_Resource_EnrollmentTokens');
