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

namespace Google\Service\IdentityToolkit;

class UserInfoProviderUserInfo extends \Google\Model
{
  /**
   * The user's display name at the IDP.
   *
   * @var string
   */
  public $displayName;
  /**
   * User's email at IDP.
   *
   * @var string
   */
  public $email;
  /**
   * User's identifier at IDP.
   *
   * @var string
   */
  public $federatedId;
  /**
   * User's phone number.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * The user's photo url at the IDP.
   *
   * @var string
   */
  public $photoUrl;
  /**
   * The IdP ID. For white listed IdPs it's a short domain name, e.g.,
   * google.com, aol.com, live.net and yahoo.com. For other OpenID IdPs it's the
   * OP identifier.
   *
   * @var string
   */
  public $providerId;
  /**
   * User's raw identifier directly returned from IDP.
   *
   * @var string
   */
  public $rawId;
  /**
   * User's screen name at Twitter or login name at Github.
   *
   * @var string
   */
  public $screenName;

  /**
   * The user's display name at the IDP.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * User's email at IDP.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * User's identifier at IDP.
   *
   * @param string $federatedId
   */
  public function setFederatedId($federatedId)
  {
    $this->federatedId = $federatedId;
  }
  /**
   * @return string
   */
  public function getFederatedId()
  {
    return $this->federatedId;
  }
  /**
   * User's phone number.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
  /**
   * The user's photo url at the IDP.
   *
   * @param string $photoUrl
   */
  public function setPhotoUrl($photoUrl)
  {
    $this->photoUrl = $photoUrl;
  }
  /**
   * @return string
   */
  public function getPhotoUrl()
  {
    return $this->photoUrl;
  }
  /**
   * The IdP ID. For white listed IdPs it's a short domain name, e.g.,
   * google.com, aol.com, live.net and yahoo.com. For other OpenID IdPs it's the
   * OP identifier.
   *
   * @param string $providerId
   */
  public function setProviderId($providerId)
  {
    $this->providerId = $providerId;
  }
  /**
   * @return string
   */
  public function getProviderId()
  {
    return $this->providerId;
  }
  /**
   * User's raw identifier directly returned from IDP.
   *
   * @param string $rawId
   */
  public function setRawId($rawId)
  {
    $this->rawId = $rawId;
  }
  /**
   * @return string
   */
  public function getRawId()
  {
    return $this->rawId;
  }
  /**
   * User's screen name at Twitter or login name at Github.
   *
   * @param string $screenName
   */
  public function setScreenName($screenName)
  {
    $this->screenName = $screenName;
  }
  /**
   * @return string
   */
  public function getScreenName()
  {
    return $this->screenName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserInfoProviderUserInfo::class, 'Google_Service_IdentityToolkit_UserInfoProviderUserInfo');
