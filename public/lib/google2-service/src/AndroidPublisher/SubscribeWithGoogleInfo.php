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

namespace Google\Service\AndroidPublisher;

class SubscribeWithGoogleInfo extends \Google\Model
{
  /**
   * The email address of the user when the subscription was purchased.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * The family name of the user when the subscription was purchased.
   *
   * @var string
   */
  public $familyName;
  /**
   * The given name of the user when the subscription was purchased.
   *
   * @var string
   */
  public $givenName;
  /**
   * The Google profile id of the user when the subscription was purchased.
   *
   * @var string
   */
  public $profileId;
  /**
   * The profile name of the user when the subscription was purchased.
   *
   * @var string
   */
  public $profileName;

  /**
   * The email address of the user when the subscription was purchased.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * The family name of the user when the subscription was purchased.
   *
   * @param string $familyName
   */
  public function setFamilyName($familyName)
  {
    $this->familyName = $familyName;
  }
  /**
   * @return string
   */
  public function getFamilyName()
  {
    return $this->familyName;
  }
  /**
   * The given name of the user when the subscription was purchased.
   *
   * @param string $givenName
   */
  public function setGivenName($givenName)
  {
    $this->givenName = $givenName;
  }
  /**
   * @return string
   */
  public function getGivenName()
  {
    return $this->givenName;
  }
  /**
   * The Google profile id of the user when the subscription was purchased.
   *
   * @param string $profileId
   */
  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }
  /**
   * @return string
   */
  public function getProfileId()
  {
    return $this->profileId;
  }
  /**
   * The profile name of the user when the subscription was purchased.
   *
   * @param string $profileName
   */
  public function setProfileName($profileName)
  {
    $this->profileName = $profileName;
  }
  /**
   * @return string
   */
  public function getProfileName()
  {
    return $this->profileName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscribeWithGoogleInfo::class, 'Google_Service_AndroidPublisher_SubscribeWithGoogleInfo');
