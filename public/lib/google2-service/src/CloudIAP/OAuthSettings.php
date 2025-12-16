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

namespace Google\Service\CloudIAP;

class OAuthSettings extends \Google\Collection
{
  protected $collection_key = 'programmaticClients';
  /**
   * Domain hint to send as hd=? parameter in OAuth request flow. Enables
   * redirect to primary IDP by skipping Google's login screen.
   * https://developers.google.com/identity/protocols/OpenIDConnect#hd-param
   * Note: IAP does not verify that the id token's hd claim matches this value
   * since access behavior is managed by IAM policies.
   *
   * @var string
   */
  public $loginHint;
  /**
   * Optional. List of client ids allowed to use IAP programmatically.
   *
   * @var string[]
   */
  public $programmaticClients;

  /**
   * Domain hint to send as hd=? parameter in OAuth request flow. Enables
   * redirect to primary IDP by skipping Google's login screen.
   * https://developers.google.com/identity/protocols/OpenIDConnect#hd-param
   * Note: IAP does not verify that the id token's hd claim matches this value
   * since access behavior is managed by IAM policies.
   *
   * @param string $loginHint
   */
  public function setLoginHint($loginHint)
  {
    $this->loginHint = $loginHint;
  }
  /**
   * @return string
   */
  public function getLoginHint()
  {
    return $this->loginHint;
  }
  /**
   * Optional. List of client ids allowed to use IAP programmatically.
   *
   * @param string[] $programmaticClients
   */
  public function setProgrammaticClients($programmaticClients)
  {
    $this->programmaticClients = $programmaticClients;
  }
  /**
   * @return string[]
   */
  public function getProgrammaticClients()
  {
    return $this->programmaticClients;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OAuthSettings::class, 'Google_Service_CloudIAP_OAuthSettings');
