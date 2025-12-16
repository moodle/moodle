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

namespace Google\Service\Pubsub;

class OidcToken extends \Google\Model
{
  /**
   * Optional. Audience to be used when generating OIDC token. The audience
   * claim identifies the recipients that the JWT is intended for. The audience
   * value is a single case-sensitive string. Having multiple values (array) for
   * the audience field is not supported. More info about the OIDC JWT token
   * audience here: https://tools.ietf.org/html/rfc7519#section-4.1.3 Note: if
   * not specified, the Push endpoint URL will be used.
   *
   * @var string
   */
  public $audience;
  /**
   * Optional. [Service account
   * email](https://cloud.google.com/iam/docs/service-accounts) used for
   * generating the OIDC token. For more information on setting up
   * authentication, see [Push
   * subscriptions](https://cloud.google.com/pubsub/docs/push).
   *
   * @var string
   */
  public $serviceAccountEmail;

  /**
   * Optional. Audience to be used when generating OIDC token. The audience
   * claim identifies the recipients that the JWT is intended for. The audience
   * value is a single case-sensitive string. Having multiple values (array) for
   * the audience field is not supported. More info about the OIDC JWT token
   * audience here: https://tools.ietf.org/html/rfc7519#section-4.1.3 Note: if
   * not specified, the Push endpoint URL will be used.
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
   * Optional. [Service account
   * email](https://cloud.google.com/iam/docs/service-accounts) used for
   * generating the OIDC token. For more information on setting up
   * authentication, see [Push
   * subscriptions](https://cloud.google.com/pubsub/docs/push).
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
class_alias(OidcToken::class, 'Google_Service_Pubsub_OidcToken');
