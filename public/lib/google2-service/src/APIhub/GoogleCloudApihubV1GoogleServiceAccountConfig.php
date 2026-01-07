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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1GoogleServiceAccountConfig extends \Google\Model
{
  /**
   * Required. The service account to be used for authenticating request. The
   * `iam.serviceAccounts.getAccessToken` permission should be granted on this
   * service account to the impersonator service account.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Required. The service account to be used for authenticating request. The
   * `iam.serviceAccounts.getAccessToken` permission should be granted on this
   * service account to the impersonator service account.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1GoogleServiceAccountConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1GoogleServiceAccountConfig');
