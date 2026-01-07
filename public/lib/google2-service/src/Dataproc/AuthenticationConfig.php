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

namespace Google\Service\Dataproc;

class AuthenticationConfig extends \Google\Model
{
  /**
   * If AuthenticationType is unspecified then END_USER_CREDENTIALS is used for
   * 3.0 and newer runtimes, and SERVICE_ACCOUNT is used for older runtimes.
   */
  public const USER_WORKLOAD_AUTHENTICATION_TYPE_AUTHENTICATION_TYPE_UNSPECIFIED = 'AUTHENTICATION_TYPE_UNSPECIFIED';
  /**
   * Use service account credentials for authenticating to other services.
   */
  public const USER_WORKLOAD_AUTHENTICATION_TYPE_SERVICE_ACCOUNT = 'SERVICE_ACCOUNT';
  /**
   * Use OAuth credentials associated with the workload creator/user for
   * authenticating to other services.
   */
  public const USER_WORKLOAD_AUTHENTICATION_TYPE_END_USER_CREDENTIALS = 'END_USER_CREDENTIALS';
  /**
   * Optional. Authentication type for the user workload running in containers.
   *
   * @var string
   */
  public $userWorkloadAuthenticationType;

  /**
   * Optional. Authentication type for the user workload running in containers.
   *
   * Accepted values: AUTHENTICATION_TYPE_UNSPECIFIED, SERVICE_ACCOUNT,
   * END_USER_CREDENTIALS
   *
   * @param self::USER_WORKLOAD_AUTHENTICATION_TYPE_* $userWorkloadAuthenticationType
   */
  public function setUserWorkloadAuthenticationType($userWorkloadAuthenticationType)
  {
    $this->userWorkloadAuthenticationType = $userWorkloadAuthenticationType;
  }
  /**
   * @return self::USER_WORKLOAD_AUTHENTICATION_TYPE_*
   */
  public function getUserWorkloadAuthenticationType()
  {
    return $this->userWorkloadAuthenticationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthenticationConfig::class, 'Google_Service_Dataproc_AuthenticationConfig');
