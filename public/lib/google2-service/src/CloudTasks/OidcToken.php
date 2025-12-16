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

namespace Google\Service\CloudTasks;

class OidcToken extends \Google\Model
{
  /**
   * Audience to be used when generating OIDC token. If not specified, the URI
   * specified in target will be used.
   *
   * @var string
   */
  public $audience;
  /**
   * [Service account email](https://cloud.google.com/iam/docs/service-accounts)
   * to be used for generating OIDC token. The service account must be within
   * the same project as the queue. The caller must have
   * iam.serviceAccounts.actAs permission for the service account.
   *
   * @var string
   */
  public $serviceAccountEmail;

  /**
   * Audience to be used when generating OIDC token. If not specified, the URI
   * specified in target will be used.
   *
   * @param string $audience
   */
  public function setAudience($audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return string
   */
  public function getAudience()
  {
    return $this->audience;
  }
  /**
   * [Service account email](https://cloud.google.com/iam/docs/service-accounts)
   * to be used for generating OIDC token. The service account must be within
   * the same project as the queue. The caller must have
   * iam.serviceAccounts.actAs permission for the service account.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OidcToken::class, 'Google_Service_CloudTasks_OidcToken');
