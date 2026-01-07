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

namespace Google\Service\Datastream;

class SalesforceProfile extends \Google\Model
{
  /**
   * Required. Domain endpoint for the Salesforce connection.
   *
   * @var string
   */
  public $domain;
  protected $oauth2ClientCredentialsType = Oauth2ClientCredentials::class;
  protected $oauth2ClientCredentialsDataType = '';
  protected $userCredentialsType = UserCredentials::class;
  protected $userCredentialsDataType = '';

  /**
   * Required. Domain endpoint for the Salesforce connection.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Connected app authentication.
   *
   * @param Oauth2ClientCredentials $oauth2ClientCredentials
   */
  public function setOauth2ClientCredentials(Oauth2ClientCredentials $oauth2ClientCredentials)
  {
    $this->oauth2ClientCredentials = $oauth2ClientCredentials;
  }
  /**
   * @return Oauth2ClientCredentials
   */
  public function getOauth2ClientCredentials()
  {
    return $this->oauth2ClientCredentials;
  }
  /**
   * User-password authentication.
   *
   * @param UserCredentials $userCredentials
   */
  public function setUserCredentials(UserCredentials $userCredentials)
  {
    $this->userCredentials = $userCredentials;
  }
  /**
   * @return UserCredentials
   */
  public function getUserCredentials()
  {
    return $this->userCredentials;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SalesforceProfile::class, 'Google_Service_Datastream_SalesforceProfile');
