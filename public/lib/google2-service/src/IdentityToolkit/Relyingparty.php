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

class Relyingparty extends \Google\Model
{
  /**
   * whether or not to install the android app on the device where the link is
   * opened
   *
   * @var bool
   */
  public $androidInstallApp;
  /**
   * minimum version of the app. if the version on the device is lower than this
   * version then the user is taken to the play store to upgrade the app
   *
   * @var string
   */
  public $androidMinimumVersion;
  /**
   * android package name of the android app to handle the action code
   *
   * @var string
   */
  public $androidPackageName;
  /**
   * whether or not the app can handle the oob code without first going to web
   *
   * @var bool
   */
  public $canHandleCodeInApp;
  /**
   * The recaptcha response from the user.
   *
   * @var string
   */
  public $captchaResp;
  /**
   * The recaptcha challenge presented to the user.
   *
   * @var string
   */
  public $challenge;
  /**
   * The url to continue to the Gitkit app
   *
   * @var string
   */
  public $continueUrl;
  /**
   * The email of the user.
   *
   * @var string
   */
  public $email;
  /**
   * iOS app store id to download the app if it's not already installed
   *
   * @var string
   */
  public $iOSAppStoreId;
  /**
   * the iOS bundle id of iOS app to handle the action code
   *
   * @var string
   */
  public $iOSBundleId;
  /**
   * The user's Gitkit login token for email change.
   *
   * @var string
   */
  public $idToken;
  /**
   * The fixed string "identitytoolkit#relyingparty".
   *
   * @var string
   */
  public $kind;
  /**
   * The new email if the code is for email change.
   *
   * @var string
   */
  public $newEmail;
  /**
   * The request type.
   *
   * @var string
   */
  public $requestType;
  /**
   * The IP address of the user.
   *
   * @var string
   */
  public $userIp;

  /**
   * whether or not to install the android app on the device where the link is
   * opened
   *
   * @param bool $androidInstallApp
   */
  public function setAndroidInstallApp($androidInstallApp)
  {
    $this->androidInstallApp = $androidInstallApp;
  }
  /**
   * @return bool
   */
  public function getAndroidInstallApp()
  {
    return $this->androidInstallApp;
  }
  /**
   * minimum version of the app. if the version on the device is lower than this
   * version then the user is taken to the play store to upgrade the app
   *
   * @param string $androidMinimumVersion
   */
  public function setAndroidMinimumVersion($androidMinimumVersion)
  {
    $this->androidMinimumVersion = $androidMinimumVersion;
  }
  /**
   * @return string
   */
  public function getAndroidMinimumVersion()
  {
    return $this->androidMinimumVersion;
  }
  /**
   * android package name of the android app to handle the action code
   *
   * @param string $androidPackageName
   */
  public function setAndroidPackageName($androidPackageName)
  {
    $this->androidPackageName = $androidPackageName;
  }
  /**
   * @return string
   */
  public function getAndroidPackageName()
  {
    return $this->androidPackageName;
  }
  /**
   * whether or not the app can handle the oob code without first going to web
   *
   * @param bool $canHandleCodeInApp
   */
  public function setCanHandleCodeInApp($canHandleCodeInApp)
  {
    $this->canHandleCodeInApp = $canHandleCodeInApp;
  }
  /**
   * @return bool
   */
  public function getCanHandleCodeInApp()
  {
    return $this->canHandleCodeInApp;
  }
  /**
   * The recaptcha response from the user.
   *
   * @param string $captchaResp
   */
  public function setCaptchaResp($captchaResp)
  {
    $this->captchaResp = $captchaResp;
  }
  /**
   * @return string
   */
  public function getCaptchaResp()
  {
    return $this->captchaResp;
  }
  /**
   * The recaptcha challenge presented to the user.
   *
   * @param string $challenge
   */
  public function setChallenge($challenge)
  {
    $this->challenge = $challenge;
  }
  /**
   * @return string
   */
  public function getChallenge()
  {
    return $this->challenge;
  }
  /**
   * The url to continue to the Gitkit app
   *
   * @param string $continueUrl
   */
  public function setContinueUrl($continueUrl)
  {
    $this->continueUrl = $continueUrl;
  }
  /**
   * @return string
   */
  public function getContinueUrl()
  {
    return $this->continueUrl;
  }
  /**
   * The email of the user.
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
   * iOS app store id to download the app if it's not already installed
   *
   * @param string $iOSAppStoreId
   */
  public function setIOSAppStoreId($iOSAppStoreId)
  {
    $this->iOSAppStoreId = $iOSAppStoreId;
  }
  /**
   * @return string
   */
  public function getIOSAppStoreId()
  {
    return $this->iOSAppStoreId;
  }
  /**
   * the iOS bundle id of iOS app to handle the action code
   *
   * @param string $iOSBundleId
   */
  public function setIOSBundleId($iOSBundleId)
  {
    $this->iOSBundleId = $iOSBundleId;
  }
  /**
   * @return string
   */
  public function getIOSBundleId()
  {
    return $this->iOSBundleId;
  }
  /**
   * The user's Gitkit login token for email change.
   *
   * @param string $idToken
   */
  public function setIdToken($idToken)
  {
    $this->idToken = $idToken;
  }
  /**
   * @return string
   */
  public function getIdToken()
  {
    return $this->idToken;
  }
  /**
   * The fixed string "identitytoolkit#relyingparty".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The new email if the code is for email change.
   *
   * @param string $newEmail
   */
  public function setNewEmail($newEmail)
  {
    $this->newEmail = $newEmail;
  }
  /**
   * @return string
   */
  public function getNewEmail()
  {
    return $this->newEmail;
  }
  /**
   * The request type.
   *
   * @param string $requestType
   */
  public function setRequestType($requestType)
  {
    $this->requestType = $requestType;
  }
  /**
   * @return string
   */
  public function getRequestType()
  {
    return $this->requestType;
  }
  /**
   * The IP address of the user.
   *
   * @param string $userIp
   */
  public function setUserIp($userIp)
  {
    $this->userIp = $userIp;
  }
  /**
   * @return string
   */
  public function getUserIp()
  {
    return $this->userIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Relyingparty::class, 'Google_Service_IdentityToolkit_Relyingparty');
