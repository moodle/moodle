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

namespace Google\Service\Backupdr;

class WorkforceIdentityBasedOAuth2ClientID extends \Google\Model
{
  /**
   * Output only. First party OAuth Client ID for Google Identities.
   *
   * @var string
   */
  public $firstPartyOauth2ClientId;
  /**
   * Output only. Third party OAuth Client ID for External Identity Providers.
   *
   * @var string
   */
  public $thirdPartyOauth2ClientId;

  /**
   * Output only. First party OAuth Client ID for Google Identities.
   *
   * @param string $firstPartyOauth2ClientId
   */
  public function setFirstPartyOauth2ClientId($firstPartyOauth2ClientId)
  {
    $this->firstPartyOauth2ClientId = $firstPartyOauth2ClientId;
  }
  /**
   * @return string
   */
  public function getFirstPartyOauth2ClientId()
  {
    return $this->firstPartyOauth2ClientId;
  }
  /**
   * Output only. Third party OAuth Client ID for External Identity Providers.
   *
   * @param string $thirdPartyOauth2ClientId
   */
  public function setThirdPartyOauth2ClientId($thirdPartyOauth2ClientId)
  {
    $this->thirdPartyOauth2ClientId = $thirdPartyOauth2ClientId;
  }
  /**
   * @return string
   */
  public function getThirdPartyOauth2ClientId()
  {
    return $this->thirdPartyOauth2ClientId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkforceIdentityBasedOAuth2ClientID::class, 'Google_Service_Backupdr_WorkforceIdentityBasedOAuth2ClientID');
