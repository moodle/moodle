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

class VerifyAssertionResponse extends \Google\Collection
{
  protected $collection_key = 'verifiedProvider';
  /**
   * The action code.
   *
   * @var string
   */
  public $action;
  /**
   * URL for OTA app installation.
   *
   * @var string
   */
  public $appInstallationUrl;
  /**
   * The custom scheme used by mobile app.
   *
   * @var string
   */
  public $appScheme;
  /**
   * The opaque value used by the client to maintain context info between the
   * authentication request and the IDP callback.
   *
   * @var string
   */
  public $context;
  /**
   * The birth date of the IdP account.
   *
   * @var string
   */
  public $dateOfBirth;
  /**
   * The display name of the user.
   *
   * @var string
   */
  public $displayName;
  /**
   * The email returned by the IdP. NOTE: The federated login user may not own
   * the email.
   *
   * @var string
   */
  public $email;
  /**
   * It's true if the email is recycled.
   *
   * @var bool
   */
  public $emailRecycled;
  /**
   * The value is true if the IDP is also the email provider. It means the user
   * owns the email.
   *
   * @var bool
   */
  public $emailVerified;
  /**
   * Client error code.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * If idToken is STS id token, then this field will be expiration time of STS
   * id token in seconds.
   *
   * @var string
   */
  public $expiresIn;
  /**
   * The unique ID identifies the IdP account.
   *
   * @var string
   */
  public $federatedId;
  /**
   * The first name of the user.
   *
   * @var string
   */
  public $firstName;
  /**
   * The full name of the user.
   *
   * @var string
   */
  public $fullName;
  /**
   * The ID token.
   *
   * @var string
   */
  public $idToken;
  /**
   * It's the identifier param in the createAuthUri request if the identifier is
   * an email. It can be used to check whether the user input email is different
   * from the asserted email.
   *
   * @var string
   */
  public $inputEmail;
  /**
   * True if it's a new user sign-in, false if it's a returning user.
   *
   * @var bool
   */
  public $isNewUser;
  /**
   * The fixed string "identitytoolkit#VerifyAssertionResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * The language preference of the user.
   *
   * @var string
   */
  public $language;
  /**
   * The last name of the user.
   *
   * @var string
   */
  public $lastName;
  /**
   * The RP local ID if it's already been mapped to the IdP account identified
   * by the federated ID.
   *
   * @var string
   */
  public $localId;
  /**
   * Whether the assertion is from a non-trusted IDP and need account linking
   * confirmation.
   *
   * @var bool
   */
  public $needConfirmation;
  /**
   * Whether need client to supply email to complete the federated login flow.
   *
   * @var bool
   */
  public $needEmail;
  /**
   * The nick name of the user.
   *
   * @var string
   */
  public $nickName;
  /**
   * The OAuth2 access token.
   *
   * @var string
   */
  public $oauthAccessToken;
  /**
   * The OAuth2 authorization code.
   *
   * @var string
   */
  public $oauthAuthorizationCode;
  /**
   * The lifetime in seconds of the OAuth2 access token.
   *
   * @var int
   */
  public $oauthExpireIn;
  /**
   * The OIDC id token.
   *
   * @var string
   */
  public $oauthIdToken;
  /**
   * The user approved request token for the OpenID OAuth extension.
   *
   * @var string
   */
  public $oauthRequestToken;
  /**
   * The scope for the OpenID OAuth extension.
   *
   * @var string
   */
  public $oauthScope;
  /**
   * The OAuth1 access token secret.
   *
   * @var string
   */
  public $oauthTokenSecret;
  /**
   * The original email stored in the mapping storage. It's returned when the
   * federated ID is associated to a different email.
   *
   * @var string
   */
  public $originalEmail;
  /**
   * The URI of the public accessible profiel picture.
   *
   * @var string
   */
  public $photoUrl;
  /**
   * The IdP ID. For white listed IdPs it's a short domain name e.g. google.com,
   * aol.com, live.net and yahoo.com. If the "providerId" param is set to OpenID
   * OP identifer other than the whilte listed IdPs the OP identifier is
   * returned. If the "identifier" param is federated ID in the createAuthUri
   * request. The domain part of the federated ID is returned.
   *
   * @var string
   */
  public $providerId;
  /**
   * Raw IDP-returned user info.
   *
   * @var string
   */
  public $rawUserInfo;
  /**
   * If idToken is STS id token, then this field will be refresh token.
   *
   * @var string
   */
  public $refreshToken;
  /**
   * The screen_name of a Twitter user or the login name at Github.
   *
   * @var string
   */
  public $screenName;
  /**
   * The timezone of the user.
   *
   * @var string
   */
  public $timeZone;
  /**
   * When action is 'map', contains the idps which can be used for confirmation.
   *
   * @var string[]
   */
  public $verifiedProvider;

  /**
   * The action code.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * URL for OTA app installation.
   *
   * @param string $appInstallationUrl
   */
  public function setAppInstallationUrl($appInstallationUrl)
  {
    $this->appInstallationUrl = $appInstallationUrl;
  }
  /**
   * @return string
   */
  public function getAppInstallationUrl()
  {
    return $this->appInstallationUrl;
  }
  /**
   * The custom scheme used by mobile app.
   *
   * @param string $appScheme
   */
  public function setAppScheme($appScheme)
  {
    $this->appScheme = $appScheme;
  }
  /**
   * @return string
   */
  public function getAppScheme()
  {
    return $this->appScheme;
  }
  /**
   * The opaque value used by the client to maintain context info between the
   * authentication request and the IDP callback.
   *
   * @param string $context
   */
  public function setContext($context)
  {
    $this->context = $context;
  }
  /**
   * @return string
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * The birth date of the IdP account.
   *
   * @param string $dateOfBirth
   */
  public function setDateOfBirth($dateOfBirth)
  {
    $this->dateOfBirth = $dateOfBirth;
  }
  /**
   * @return string
   */
  public function getDateOfBirth()
  {
    return $this->dateOfBirth;
  }
  /**
   * The display name of the user.
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
   * The email returned by the IdP. NOTE: The federated login user may not own
   * the email.
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
   * It's true if the email is recycled.
   *
   * @param bool $emailRecycled
   */
  public function setEmailRecycled($emailRecycled)
  {
    $this->emailRecycled = $emailRecycled;
  }
  /**
   * @return bool
   */
  public function getEmailRecycled()
  {
    return $this->emailRecycled;
  }
  /**
   * The value is true if the IDP is also the email provider. It means the user
   * owns the email.
   *
   * @param bool $emailVerified
   */
  public function setEmailVerified($emailVerified)
  {
    $this->emailVerified = $emailVerified;
  }
  /**
   * @return bool
   */
  public function getEmailVerified()
  {
    return $this->emailVerified;
  }
  /**
   * Client error code.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * If idToken is STS id token, then this field will be expiration time of STS
   * id token in seconds.
   *
   * @param string $expiresIn
   */
  public function setExpiresIn($expiresIn)
  {
    $this->expiresIn = $expiresIn;
  }
  /**
   * @return string
   */
  public function getExpiresIn()
  {
    return $this->expiresIn;
  }
  /**
   * The unique ID identifies the IdP account.
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
   * The first name of the user.
   *
   * @param string $firstName
   */
  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;
  }
  /**
   * @return string
   */
  public function getFirstName()
  {
    return $this->firstName;
  }
  /**
   * The full name of the user.
   *
   * @param string $fullName
   */
  public function setFullName($fullName)
  {
    $this->fullName = $fullName;
  }
  /**
   * @return string
   */
  public function getFullName()
  {
    return $this->fullName;
  }
  /**
   * The ID token.
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
   * It's the identifier param in the createAuthUri request if the identifier is
   * an email. It can be used to check whether the user input email is different
   * from the asserted email.
   *
   * @param string $inputEmail
   */
  public function setInputEmail($inputEmail)
  {
    $this->inputEmail = $inputEmail;
  }
  /**
   * @return string
   */
  public function getInputEmail()
  {
    return $this->inputEmail;
  }
  /**
   * True if it's a new user sign-in, false if it's a returning user.
   *
   * @param bool $isNewUser
   */
  public function setIsNewUser($isNewUser)
  {
    $this->isNewUser = $isNewUser;
  }
  /**
   * @return bool
   */
  public function getIsNewUser()
  {
    return $this->isNewUser;
  }
  /**
   * The fixed string "identitytoolkit#VerifyAssertionResponse".
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
   * The language preference of the user.
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * The last name of the user.
   *
   * @param string $lastName
   */
  public function setLastName($lastName)
  {
    $this->lastName = $lastName;
  }
  /**
   * @return string
   */
  public function getLastName()
  {
    return $this->lastName;
  }
  /**
   * The RP local ID if it's already been mapped to the IdP account identified
   * by the federated ID.
   *
   * @param string $localId
   */
  public function setLocalId($localId)
  {
    $this->localId = $localId;
  }
  /**
   * @return string
   */
  public function getLocalId()
  {
    return $this->localId;
  }
  /**
   * Whether the assertion is from a non-trusted IDP and need account linking
   * confirmation.
   *
   * @param bool $needConfirmation
   */
  public function setNeedConfirmation($needConfirmation)
  {
    $this->needConfirmation = $needConfirmation;
  }
  /**
   * @return bool
   */
  public function getNeedConfirmation()
  {
    return $this->needConfirmation;
  }
  /**
   * Whether need client to supply email to complete the federated login flow.
   *
   * @param bool $needEmail
   */
  public function setNeedEmail($needEmail)
  {
    $this->needEmail = $needEmail;
  }
  /**
   * @return bool
   */
  public function getNeedEmail()
  {
    return $this->needEmail;
  }
  /**
   * The nick name of the user.
   *
   * @param string $nickName
   */
  public function setNickName($nickName)
  {
    $this->nickName = $nickName;
  }
  /**
   * @return string
   */
  public function getNickName()
  {
    return $this->nickName;
  }
  /**
   * The OAuth2 access token.
   *
   * @param string $oauthAccessToken
   */
  public function setOauthAccessToken($oauthAccessToken)
  {
    $this->oauthAccessToken = $oauthAccessToken;
  }
  /**
   * @return string
   */
  public function getOauthAccessToken()
  {
    return $this->oauthAccessToken;
  }
  /**
   * The OAuth2 authorization code.
   *
   * @param string $oauthAuthorizationCode
   */
  public function setOauthAuthorizationCode($oauthAuthorizationCode)
  {
    $this->oauthAuthorizationCode = $oauthAuthorizationCode;
  }
  /**
   * @return string
   */
  public function getOauthAuthorizationCode()
  {
    return $this->oauthAuthorizationCode;
  }
  /**
   * The lifetime in seconds of the OAuth2 access token.
   *
   * @param int $oauthExpireIn
   */
  public function setOauthExpireIn($oauthExpireIn)
  {
    $this->oauthExpireIn = $oauthExpireIn;
  }
  /**
   * @return int
   */
  public function getOauthExpireIn()
  {
    return $this->oauthExpireIn;
  }
  /**
   * The OIDC id token.
   *
   * @param string $oauthIdToken
   */
  public function setOauthIdToken($oauthIdToken)
  {
    $this->oauthIdToken = $oauthIdToken;
  }
  /**
   * @return string
   */
  public function getOauthIdToken()
  {
    return $this->oauthIdToken;
  }
  /**
   * The user approved request token for the OpenID OAuth extension.
   *
   * @param string $oauthRequestToken
   */
  public function setOauthRequestToken($oauthRequestToken)
  {
    $this->oauthRequestToken = $oauthRequestToken;
  }
  /**
   * @return string
   */
  public function getOauthRequestToken()
  {
    return $this->oauthRequestToken;
  }
  /**
   * The scope for the OpenID OAuth extension.
   *
   * @param string $oauthScope
   */
  public function setOauthScope($oauthScope)
  {
    $this->oauthScope = $oauthScope;
  }
  /**
   * @return string
   */
  public function getOauthScope()
  {
    return $this->oauthScope;
  }
  /**
   * The OAuth1 access token secret.
   *
   * @param string $oauthTokenSecret
   */
  public function setOauthTokenSecret($oauthTokenSecret)
  {
    $this->oauthTokenSecret = $oauthTokenSecret;
  }
  /**
   * @return string
   */
  public function getOauthTokenSecret()
  {
    return $this->oauthTokenSecret;
  }
  /**
   * The original email stored in the mapping storage. It's returned when the
   * federated ID is associated to a different email.
   *
   * @param string $originalEmail
   */
  public function setOriginalEmail($originalEmail)
  {
    $this->originalEmail = $originalEmail;
  }
  /**
   * @return string
   */
  public function getOriginalEmail()
  {
    return $this->originalEmail;
  }
  /**
   * The URI of the public accessible profiel picture.
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
   * The IdP ID. For white listed IdPs it's a short domain name e.g. google.com,
   * aol.com, live.net and yahoo.com. If the "providerId" param is set to OpenID
   * OP identifer other than the whilte listed IdPs the OP identifier is
   * returned. If the "identifier" param is federated ID in the createAuthUri
   * request. The domain part of the federated ID is returned.
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
   * Raw IDP-returned user info.
   *
   * @param string $rawUserInfo
   */
  public function setRawUserInfo($rawUserInfo)
  {
    $this->rawUserInfo = $rawUserInfo;
  }
  /**
   * @return string
   */
  public function getRawUserInfo()
  {
    return $this->rawUserInfo;
  }
  /**
   * If idToken is STS id token, then this field will be refresh token.
   *
   * @param string $refreshToken
   */
  public function setRefreshToken($refreshToken)
  {
    $this->refreshToken = $refreshToken;
  }
  /**
   * @return string
   */
  public function getRefreshToken()
  {
    return $this->refreshToken;
  }
  /**
   * The screen_name of a Twitter user or the login name at Github.
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
  /**
   * The timezone of the user.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * When action is 'map', contains the idps which can be used for confirmation.
   *
   * @param string[] $verifiedProvider
   */
  public function setVerifiedProvider($verifiedProvider)
  {
    $this->verifiedProvider = $verifiedProvider;
  }
  /**
   * @return string[]
   */
  public function getVerifiedProvider()
  {
    return $this->verifiedProvider;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerifyAssertionResponse::class, 'Google_Service_IdentityToolkit_VerifyAssertionResponse');
